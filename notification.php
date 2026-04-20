<?php
session_start();
if (!isset($_SESSION['ticket_id'])) { header('Location: index.php'); exit; }
require 'php_functions.php';

$bestellungen = getBestellungenByTicket($_SESSION['ticket_id']);

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
