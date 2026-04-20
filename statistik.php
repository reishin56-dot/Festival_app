<?php
require 'db.php';

$zeitraum = $_GET['zeitraum'] ?? 'heute';

$filter = match($zeitraum) {
    'heute'  => 'DATE(b.bestellt_am) = CURDATE()',
    'woche'  => 'YEARWEEK(b.bestellt_am, 1) = YEARWEEK(CURDATE(), 1)',
    'monat'  => 'MONTH(b.bestellt_am) = MONTH(CURDATE()) AND YEAR(b.bestellt_am) = YEAR(CURDATE())',
    'jahr'   => 'YEAR(b.bestellt_am) = YEAR(CURDATE())',
    default  => '1=1',
};

$stmt = $pdo->query("
    SELECT p.name, p.kategorie, SUM(bp.menge) AS menge, SUM(bp.menge * bp.einzelpreis) AS umsatz
    FROM bestellpositionen bp
    JOIN produkte p ON p.id = bp.produkt_id
    JOIN bestellungen b ON b.id = bp.bestellung_id
    WHERE b.status != 'storniert' AND $filter
    GROUP BY p.id
    ORDER BY menge DESC
");
$stats = $stmt->fetchAll();

$maxMenge = max(array_column($stats, 'menge') ?: [1]);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Statistik Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>

<php require "php_functions.php"; ?>

<body>
<div class="container">
    <h1><u>Statistik Board</u></h1>

    <form method="get" style="margin: 20px 0;">
        <label for="zeitraum">Zeitraum:</label>
        <select id="zeitraum" name="zeitraum" onchange="this.form.submit()"
                style="padding:8px 12px; font-size:16px; border-radius:5px;">
            <option value="heute" <?= $zeitraum === 'heute' ? 'selected' : '' ?>>Heute</option>
            <option value="woche" <?= $zeitraum === 'woche' ? 'selected' : '' ?>>Diese Woche</option>
            <option value="monat" <?= $zeitraum === 'monat' ? 'selected' : '' ?>>Dieser Monat</option>
            <option value="jahr"  <?= $zeitraum === 'jahr'  ? 'selected' : '' ?>>Dieses Jahr</option>
        </select>
    </form>

    <div class="chart-container" style="border:2px solid #333; padding:20px; border-radius:8px;">
        <?php if (empty($stats)): ?>
          <p>Keine Daten für diesen Zeitraum.</p>
        <?php else: ?>
          <?php foreach ($stats as $row): ?>
            <div class="chart-item">
                <label class="chart-label">
                    <u><?= htmlspecialchars($row['name']) ?>:</u>
                    <?= (int)$row['menge'] ?> Verkäufe
                    (<?= number_format($row['umsatz'], 0) ?> Credits)
                </label>
                <div class="chart-bar-wrapper" style="width:<?= round($row['menge'] / $maxMenge * 100) ?>%;">
                    <div class="chart-bar chart-bar-<?= htmlspecialchars($row['kategorie']) ?>"
                         style="width:100%;"></div>
                </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <br>
    <button onclick="goToPage('admin_index.php')">Zurück</button>
</div>
<script src="script.js"></script>
</body>
</html>
