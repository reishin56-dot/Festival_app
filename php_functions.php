<?php
require_once 'db.php';

// ── Navigation ────────────────────────────────────────────────────────────────

function changePage($seite = null) {
    if ($seite === null) {
        $seite = isset($_GET['page']) ? $_GET['page'] : 'index.php';
    }
    header("Location: " . $seite);
    exit();
}

// Nur weiterleiten wenn die Datei direkt aufgerufen wird (nicht per require)
if (basename($_SERVER['SCRIPT_FILENAME']) === 'php_functions.php') {
    changePage();
}

// ── Tickets ───────────────────────────────────────────────────────────────────

function getTicketByNummer($ticketnummer) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT id, name, credits FROM tickets WHERE ticketnummer = ?');
    $stmt->execute([$ticketnummer]);
    return $stmt->fetch();
}

function getCredits($ticket_id) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT credits FROM tickets WHERE id = ?');
    $stmt->execute([$ticket_id]);
    return $stmt->fetchColumn();
}

// ── Bestellungen ──────────────────────────────────────────────────────────────

function getBestellungenByTicket($ticket_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT b.id, b.status, b.gesamt_credits, b.bestellt_am,
               s.name AS stand_name,
               GROUP_CONCAT(CONCAT(bp.menge, 'x ', p.name) ORDER BY p.name SEPARATOR ', ') AS artikel
        FROM bestellungen b
        JOIN staende s ON s.id = b.stand_id
        JOIN bestellpositionen bp ON bp.bestellung_id = b.id
        JOIN produkte p ON p.id = bp.produkt_id
        WHERE b.ticket_id = ?
        GROUP BY b.id
        ORDER BY b.bestellt_am DESC
    ");
    $stmt->execute([$ticket_id]);
    return $stmt->fetchAll();
}

function getOffeneBestellungenByStand($stand_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT b.id, b.status, b.bestellt_am, t.ticketnummer,
               GROUP_CONCAT(CONCAT(bp.menge, 'x ', p.name) ORDER BY p.name SEPARATOR ', ') AS artikel
        FROM bestellungen b
        JOIN tickets t ON t.id = b.ticket_id
        JOIN bestellpositionen bp ON bp.bestellung_id = b.id
        JOIN produkte p ON p.id = bp.produkt_id
        WHERE b.stand_id = ? AND b.status IN ('offen','in_bearbeitung')
        GROUP BY b.id
        ORDER BY b.bestellt_am ASC
    ");
    $stmt->execute([$stand_id]);
    return $stmt->fetchAll();
}

function bestellungAufgeben($ticket_id, $kategorie, $mengen, $preise) {
    global $pdo;

    $gesamt = 0;
    foreach ($mengen as $pid => $menge) {
        $gesamt += $menge * $preise[$pid];
    }

    if ($gesamt == 0) {
        return ['fehler' => 'Bitte mindestens ein Produkt auswählen.'];
    }

    $credits = getCredits($ticket_id);
    if ($credits < $gesamt) {
        return ['fehler' => 'Nicht genug Credits! Guthaben: ' . number_format($credits, 2)];
    }

    $standStmt = $pdo->query("SELECT id FROM staende WHERE kategorie='$kategorie' AND aktiv=1 ORDER BY wartezeit ASC LIMIT 1");
    $stand_id  = $standStmt->fetchColumn();

    $pdo->beginTransaction();
    $pdo->prepare('INSERT INTO bestellungen (ticket_id, stand_id, gesamt_credits) VALUES (?,?,?)')
        ->execute([$ticket_id, $stand_id, $gesamt]);
    $bid = $pdo->lastInsertId();

    foreach ($mengen as $pid => $menge) {
        if ($menge > 0) {
            $pdo->prepare('INSERT INTO bestellpositionen (bestellung_id, produkt_id, menge, einzelpreis) VALUES (?,?,?,?)')
                ->execute([$bid, $pid, $menge, $preise[$pid]]);
        }
    }
    $pdo->prepare('UPDATE tickets SET credits = credits - ? WHERE id = ?')
        ->execute([$gesamt, $ticket_id]);
    $pdo->commit();

    return ['erfolg' => true];
}

function bestellungStatusAktualisieren($bestellung_id, $status) {
    global $pdo;
    $erlaubt = ['offen', 'in_bearbeitung', 'abgeschlossen', 'storniert'];
    if (!in_array($status, $erlaubt)) return false;
    $pdo->prepare('UPDATE bestellungen SET status = ? WHERE id = ?')
        ->execute([$status, (int)$bestellung_id]);
    return true;
}

// ── Credits aufladen ──────────────────────────────────────────────────────────

function creditsAufladen($ticket_id, $positionen) {
    global $pdo;

    $gesamt_credits = 0;
    foreach ($positionen as $pos) {
        $gesamt_credits += $pos['menge'] * $pos['credits'];
    }

    if ($gesamt_credits == 0) {
        return ['fehler' => 'Bitte mindestens ein Paket auswählen.'];
    }

    $pdo->beginTransaction();
    foreach ($positionen as $pos) {
        for ($i = 0; $i < $pos['menge']; $i++) {
            $pdo->prepare('INSERT INTO credit_aufladungen (ticket_id, betrag_euro, credits) VALUES (?,?,?)')
                ->execute([$ticket_id, $pos['euro'], $pos['credits']]);
        }
    }
    $pdo->prepare('UPDATE tickets SET credits = credits + ? WHERE id = ?')
        ->execute([$gesamt_credits, $ticket_id]);
    $pdo->commit();

    return ['erfolg' => true];
}

// ── Stände ────────────────────────────────────────────────────────────────────

function getAlleStaende() {
    global $pdo;
    return $pdo->query("SELECT id, name FROM staende WHERE aktiv = 1 ORDER BY kategorie")->fetchAll();
}

function getStaendeMitEngpass() {
    global $pdo;
    return $pdo->query("
        SELECT s.id, s.name, s.kategorie, s.wartezeit,
               COUNT(b.id) AS offene_bestellungen
        FROM staende s
        LEFT JOIN bestellungen b ON b.stand_id = s.id AND b.status IN ('offen','in_bearbeitung')
        WHERE s.aktiv = 1
        GROUP BY s.id
        ORDER BY s.wartezeit DESC
    ")->fetchAll();
}

function warteZeitAktualisieren($stand_id, $wartezeit) {
    global $pdo;
    $pdo->prepare('UPDATE staende SET wartezeit = ? WHERE id = ?')
        ->execute([(int)$wartezeit, (int)$stand_id]);
}

// ── Statistik ─────────────────────────────────────────────────────────────────

function getStatistik($zeitraum) {
    global $pdo;
    $filter = match($zeitraum) {
        'heute' => 'DATE(b.bestellt_am) = CURDATE()',
        'woche' => 'YEARWEEK(b.bestellt_am, 1) = YEARWEEK(CURDATE(), 1)',
        'monat' => 'MONTH(b.bestellt_am) = MONTH(CURDATE()) AND YEAR(b.bestellt_am) = YEAR(CURDATE())',
        'jahr'  => 'YEAR(b.bestellt_am) = YEAR(CURDATE())',
        default => '1=1',
    };
    return $pdo->query("
        SELECT p.name, p.kategorie, SUM(bp.menge) AS menge, SUM(bp.menge * bp.einzelpreis) AS umsatz
        FROM bestellpositionen bp
        JOIN produkte p ON p.id = bp.produkt_id
        JOIN bestellungen b ON b.id = bp.bestellung_id
        WHERE b.status != 'storniert' AND $filter
        GROUP BY p.id
        ORDER BY menge DESC
    ")->fetchAll();
}
