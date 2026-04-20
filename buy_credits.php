<?php
session_start();
if (!isset($_SESSION['ticket_id'])) { header('Location: index.php'); exit; }
require 'db.php';

$fehler = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pakete = [
        '5'  => ['credits' => 5,  'euro' => 5.00],
        '10' => ['credits' => 10, 'euro' => 9.00],
        '20' => ['credits' => 20, 'euro' => 17.00],
    ];

    $gesamt_credits = 0;
    $gesamt_euro    = 0.00;
    $positionen     = [];

    foreach ($pakete as $key => $paket) {
        $menge = (int)($_POST['qty_credits' . $key] ?? 0);
        if ($menge > 0) {
            $gesamt_credits += $menge * $paket['credits'];
            $gesamt_euro    += $menge * $paket['euro'];
            $positionen[]    = ['menge' => $menge, 'credits' => $paket['credits'], 'euro' => $paket['euro']];
        }
    }

    if ($gesamt_credits == 0) {
        $fehler = 'Bitte mindestens ein Paket auswählen.';
    } else {
        $pdo->beginTransaction();
        foreach ($positionen as $pos) {
            for ($i = 0; $i < $pos['menge']; $i++) {
                $pdo->prepare('INSERT INTO credit_aufladungen (ticket_id, betrag_euro, credits) VALUES (?,?,?)')
                    ->execute([$_SESSION['ticket_id'], $pos['euro'], $pos['credits']]);
            }
        }
        $pdo->prepare('UPDATE tickets SET credits = credits + ? WHERE id = ?')
            ->execute([$gesamt_credits, $_SESSION['ticket_id']]);
        $pdo->commit();

        header('Location: menu.php');
        exit;
    }
}

$stmt = $pdo->prepare('SELECT credits FROM tickets WHERE id = ?');
$stmt->execute([$_SESSION['ticket_id']]);
$credits = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Guthaben aufladen</title>
    <link rel="stylesheet" href="style.css">
</head>
<php require "php_functions.php"; ?>
<body>
<div class="container">
    <h1 onclick="goToPage('menu.php')">Guthaben aufladen</h1>
    <p>Aktuelles Guthaben: <strong><?= number_format($credits, 2) ?> Credits</strong></p>

    <?php if ($fehler): ?>
      <p style="color:red;"><?= htmlspecialchars($fehler) ?></p>
    <?php endif; ?>

    <form method="post">
      <input type="hidden" id="qty_credits5"  name="qty_credits5"  value="0">
      <input type="hidden" id="qty_credits10" name="qty_credits10" value="0">
      <input type="hidden" id="qty_credits20" name="qty_credits20" value="0">

      <div class="item">
          5 Credits – 5 €
          <button type="button" class="btn" onclick="change('credits5', -1)">-</button>
          <span id="credits5">0</span>
          <button type="button" class="btn" onclick="change('credits5', 1)">+</button>
      </div>
      <div class="item">
          10 Credits – 9 €
          <button type="button" class="btn" onclick="change('credits10', -1)">-</button>
          <span id="credits10">0</span>
          <button type="button" class="btn" onclick="change('credits10', 1)">+</button>
      </div>
      <div class="item">
          20 Credits – 17 €
          <button type="button" class="btn" onclick="change('credits20', -1)">-</button>
          <span id="credits20">0</span>
          <button type="button" class="btn" onclick="change('credits20', 1)">+</button>
      </div>
      <hr>
      <h3>Gesamt: <span id="total">0</span> €</h3>
      <button type="submit">Aufladen</button>
    </form>
    <br>
    <button onclick="goToPage('menu.php')">Zurück</button>
</div>
<script src="script.js"></script>
</body>
</html>
