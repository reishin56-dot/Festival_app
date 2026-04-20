<?php
session_start();
if (!isset($_SESSION['ticket_id'])) { header('Location: index.php'); exit; }
require 'php_functions.php';

$fehler  = '';
$erfolg  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mengen = [
        1 => (int)($_POST['qty_margherita'] ?? 0),
        3 => (int)($_POST['qty_spezial']    ?? 0),
        2 => (int)($_POST['qty_pepperoni']  ?? 0),
    ];
    $preise = [1 => 5.00, 3 => 12.00, 2 => 8.00];

    $ergebnis = bestellungAufgeben($_SESSION['ticket_id'], 'pizza', $mengen, $preise);
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
    <title>Pizza</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
<div class="container">
    <h1 onclick="goToPage('order_food.php')">Pizza</h1>
    <p>Guthaben: <strong><?= number_format($credits, 2) ?> Credits</strong></p>

    <?php if ($fehler): ?>
      <p style="color:red;"><?= htmlspecialchars($fehler) ?></p>
    <?php endif; ?>

    <form method="post">
      <input type="hidden" id="qty_margherita" name="qty_margherita" value="0">
      <input type="hidden" id="qty_spezial"    name="qty_spezial"    value="0">
      <input type="hidden" id="qty_pepperoni"  name="qty_pepperoni"  value="0">

      <div class="item">
          Margherita - 5 Credits
          <button type="button" class="btn" onclick="change('margherita', -1)">-</button>
          <span id="margherita">0</span>
          <button type="button" class="btn" onclick="change('margherita', 1)">+</button>
      </div>
      <div class="item">
          Spezial - 12 Credits
          <button type="button" class="btn" onclick="change('spezial', -1)">-</button>
          <span id="spezial">0</span>
          <button type="button" class="btn" onclick="change('spezial', 1)">+</button>
      </div>
      <div class="item">
          Pepperoni - 8 Credits
          <button type="button" class="btn" onclick="change('pepperoni', -1)">-</button>
          <span id="pepperoni">0</span>
          <button type="button" class="btn" onclick="change('pepperoni', 1)">+</button>
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
