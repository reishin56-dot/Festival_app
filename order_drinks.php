<?php
session_start();
if (!isset($_SESSION['ticket_id'])) { header('Location: index.php'); exit; }
require 'php_functions.php';

$fehler = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mengen = [
        7 => (int)($_POST['qty_wasser']   ?? 0),
        8 => (int)($_POST['qty_bier']     ?? 0),
        9 => (int)($_POST['qty_cocktail'] ?? 0),
    ];
    $preise = [7 => 5.00, 8 => 8.00, 9 => 12.00];

    $ergebnis = bestellungAufgeben($_SESSION['ticket_id'], 'getraenke', $mengen, $preise);
    if (isset($ergebnis['fehler'])) {
        $fehler = $ergebnis['fehler'];
    } else {
        header('Location: notification.php?erfolg=1');
        exit;
    }
}

$credits = getCredits($_SESSION['ticket_id']);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Getränke</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1 onclick="goToPage('order_food.php')">Getränke</h1>
    <p>Guthaben: <strong><?= number_format($credits, 2) ?> Credits</strong></p>

    <?php if ($fehler): ?>
      <p style="color:red;"><?= htmlspecialchars($fehler) ?></p>
    <?php endif; ?>

    <form method="post">
      <input type="hidden" id="qty_wasser"   name="qty_wasser"   value="0">
      <input type="hidden" id="qty_bier"     name="qty_bier"     value="0">
      <input type="hidden" id="qty_cocktail" name="qty_cocktail" value="0">

      <div class="item">
          Wasser - 5 Credits
          <button type="button" class="btn" onclick="change('wasser', -1)">-</button>
          <span id="wasser">0</span>
          <button type="button" class="btn" onclick="change('wasser', 1)">+</button>
      </div>
      <div class="item">
          Cocktail - 12 Credits
          <button type="button" class="btn" onclick="change('cocktail', -1)">-</button>
          <span id="cocktail">0</span>
          <button type="button" class="btn" onclick="change('cocktail', 1)">+</button>
      </div>
      <div class="item">
          Bier - 8 Credits
          <button type="button" class="btn" onclick="change('bier', -1)">-</button>
          <span id="bier">0</span>
          <button type="button" class="btn" onclick="change('bier', 1)">+</button>
      </div>
      <hr>
      <h3>Gesamt: <span id="total">0</span> Credits</h3>
      <button type="submit">Jetzt bestellen</button>
    </form>
    <br>
    <button onclick="goToPage('order_food.php')">Zurück</button>
</div>
<script src="script.js"></script>
</body>
</html>
