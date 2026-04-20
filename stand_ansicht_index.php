<?php
require 'db.php';

$stand_id = (int)($_GET['stand'] ?? 1);

// Status-Update via POST (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bestellung_id'], $_POST['status'])) {
    $erlaubteStatus = ['offen', 'in_bearbeitung', 'abgeschlossen', 'storniert'];
    if (in_array($_POST['status'], $erlaubteStatus)) {
        $pdo->prepare('UPDATE bestellungen SET status = ? WHERE id = ?')
            ->execute([$_POST['status'], (int)$_POST['bestellung_id']]);
    }
    header('Content-Type: application/json');
    echo json_encode(['ok' => true]);
    exit;
}

$staende = $pdo->query("SELECT id, name FROM staende WHERE aktiv = 1 ORDER BY kategorie")->fetchAll();

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
$bestellungen = $stmt->fetchAll();

$statusFlow = ['offen' => 'in_bearbeitung', 'in_bearbeitung' => 'abgeschlossen'];
$statusLabel = ['offen' => 'Erstellt', 'in_bearbeitung' => 'In Zubereitung', 'abgeschlossen' => 'Bereit'];
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Standansicht</title>
    <link rel="stylesheet" href="style.css">
</head>

<php require "php_functions.php"; ?>

<body>
<div class="stand-card">
    <div class="stand-header">
        <div class="stand-title">
            <form method="get" style="display:inline;">
                Stand:
                <select name="stand" onchange="this.form.submit()" style="font-size:16px;">
                    <?php foreach ($staende as $s): ?>
                      <option value="<?= $s['id'] ?>" <?= $s['id'] == $stand_id ? 'selected' : '' ?>>
                          <?= htmlspecialchars($s['name']) ?>
                      </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
        <div class="stand-stock">Offen: <strong><?= count($bestellungen) ?></strong></div>
    </div>

    <div class="order-list">
        <?php if (empty($bestellungen)): ?>
          <p style="padding:20px;">Keine offenen Bestellungen.</p>
        <?php else: ?>
          <?php foreach ($bestellungen as $b): ?>
            <div class="order-item">
                <div class="order-top">
                    <span class="order-id">#<?= $b['id'] ?></span>
                    <span class="order-details"><?= htmlspecialchars($b['artikel']) ?></span>
                    <span class="order-time"><?= date('H:i', strtotime($b['bestellt_am'])) ?></span>
                </div>
                <div class="order-extra">Ticket: <?= htmlspecialchars($b['ticketnummer']) ?></div>
                <button class="status-btn"
                        data-id="<?= $b['id'] ?>"
                        data-status="<?= $b['status'] ?>"
                        onclick="nextStatus(this)">
                    <?= $statusLabel[$b['status']] ?? $b['status'] ?>
                </button>
            </div>
            <div class="divider"></div>
          <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <button class="back-btn" onclick="goToPage('engpasssteuerung.php')">Zurück</button>
</div>

<footer>
    <p>© 2026 Festival App – by Abilas Sivarajah & Lucas Kessler & Michael Linn</p>
</footer>

<script src="script.js"></script>
<script>
const statusFlow  = {offen: 'in_bearbeitung', in_bearbeitung: 'abgeschlossen'};
const statusLabel = {offen: 'Erstellt', in_bearbeitung: 'In Zubereitung', abgeschlossen: 'Bereit'};

function nextStatus(btn) {
    const id         = btn.dataset.id;
    const current    = btn.dataset.status;
    const next       = statusFlow[current];
    if (!next) return;

    fetch('stand_ansicht_index.php?stand=<?= $stand_id ?>', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'bestellung_id=' + id + '&status=' + next
    }).then(() => {
        btn.dataset.status = next;
        btn.innerText      = statusLabel[next];
        if (next === 'abgeschlossen') {
            btn.style.background  = '#7dbb7f';
            btn.style.color       = '#fff';
            btn.style.borderColor = '#5a9a5c';
        } else {
            btn.style.background  = '#ffcc00';
            btn.style.borderColor = '#d4a400';
            btn.style.color       = '#1a1a1a';
        }
    });
}
</script>
</body>
</html>
