<?php
session_start();
if (!isset($_SESSION['ticket_id'])) { header('Location: index.php'); exit; }
require 'db.php';

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
$stmt->execute([$_SESSION['ticket_id']]);
$bestellungen = $stmt->fetchAll();

$statusLabel = [
    'offen'          => 'Erstellt',
    'in_bearbeitung' => 'In Zubereitung',
    'abgeschlossen'  => 'Bereit zur Abholung',
    'storniert'      => 'Storniert',
];
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Meine Bestellungen</title>
    <link rel="stylesheet" href="style.css">
</head>



<body>
<div class="container">
    <h1 onclick="goToPage('menu.php')">Meine Bestellungen</h1>

    <?php if (isset($_GET['erfolg'])): ?>
      <p style="color:green;">Bestellung erfolgreich aufgegeben!</p>
    <?php endif; ?>

    <?php if (empty($bestellungen)): ?>
      <p>Noch keine Bestellungen vorhanden.</p>
    <?php else: ?>
      <ul style="list-style:none; padding:0;">
        <?php foreach ($bestellungen as $b): ?>
          <li style="border:1px solid #ccc; border-radius:8px; padding:12px; margin-bottom:10px;">
            <strong>#<?= $b['id'] ?></strong> – <?= htmlspecialchars($b['artikel']) ?><br>
            Stand: <?= htmlspecialchars($b['stand_name']) ?><br>
            Status: <strong><?= $statusLabel[$b['status']] ?? $b['status'] ?></strong><br>
            <small><?= htmlspecialchars($b['bestellt_am']) ?> – <?= number_format($b['gesamt_credits'], 2) ?> Credits</small>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <button onclick="goToPage('menu.php')">Zurück</button>
</div>
<script src="script.js"></script>
</body>
</html>
