<?php
require 'db.php';

// Engpässe = Stände mit Wartezeit > 10 Minuten
$stmt = $pdo->query("
    SELECT s.id, s.name, s.kategorie, s.wartezeit,
           COUNT(b.id) AS offene_bestellungen
    FROM staende s
    LEFT JOIN bestellungen b ON b.stand_id = s.id AND b.status IN ('offen','in_bearbeitung')
    WHERE s.aktiv = 1
    GROUP BY s.id
    ORDER BY s.wartezeit DESC
");
$staende = $stmt->fetchAll();

// Manuelle Wartezeit-Anpassung
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['stand_id'], $_POST['wartezeit'])) {
    $pdo->prepare('UPDATE staende SET wartezeit = ? WHERE id = ?')
        ->execute([(int)$_POST['wartezeit'], (int)$_POST['stand_id']]);
    header('Location: engpasssteuerung.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Engpasssteuerung</title>
    <link rel="stylesheet" href="style.css">
</head>

<php require "php_functions.php"; ?>
<body>
<div class="container">
    <h1><u>Engpasssteuerung</u></h1>

    <?php foreach ($staende as $s): ?>
      <div style="border:2px solid <?= $s['wartezeit'] > 10 ? '#cc0000' : '#333' ?>;
                  padding:16px; border-radius:8px; margin-bottom:16px;">
        <h2 style="margin-top:0;">Stand: <?= htmlspecialchars($s['name']) ?></h2>
        <p>Aktuelle Wartezeit: <strong><?= $s['wartezeit'] ?> Min</strong>
           <?= $s['wartezeit'] > 10 ? ' ⚠️' : '' ?></p>
        <p>Offene Bestellungen: <strong><?= $s['offene_bestellungen'] ?></strong></p>

        <form method="post" style="display:inline;">
            <input type="hidden" name="stand_id" value="<?= $s['id'] ?>">
            <label>Wartezeit anpassen:
                <input type="number" name="wartezeit" value="<?= $s['wartezeit'] ?>"
                       min="0" style="width:60px;">
                Min
            </label>
            <button type="submit">Speichern</button>
        </form>
      </div>
    <?php endforeach; ?>

    <button onclick="goToPage('stand_ansicht_index.php')">Standansicht anzeigen</button>
    <button onclick="goToPage('admin_index.php')">Zurück</button>
</div>
<footer>
    <p>© 2026 Festival App – by Abilas Sivarajah & Lucas Kessler & Michael Linn</p>
</footer>
<script src="script.js"></script>
</body>
</html>
