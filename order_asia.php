<?php
require 'session.php';
require 'php_functions.php';
nurFuerBenutzer();

$fehler = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mengen = [
        4 => (int)($_POST['qty_ramen']     ?? 0),
        5 => (int)($_POST['qty_sushi']     ?? 0),
        6 => (int)($_POST['qty_dumplings'] ?? 0),
    ];
    $preise = [4 => 8.00, 5 => 10.00, 6 => 6.00];

    $ergebnis = bestellungAufgeben($_SESSION['ticket_id'], 'asia', $mengen, $preise);
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
    <title>Asia</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1 onclick="goToPage('order_food.php')">Asia</h1>
    <p>Guthaben: <strong><?= number_format($credits, 2) ?> Credits</strong></p>

    <?php if ($fehler): ?>
      <p style="color:red;"><?= htmlspecialchars($fehler) ?></p>
    <?php endif; ?>

    <form method="post">
      <input type="hidden" id="qty_ramen"     name="qty_ramen"     value="0">
      <input type="hidden" id="qty_sushi"     name="qty_sushi"     value="0">
      <input type="hidden" id="qty_dumplings" name="qty_dumplings" value="0">

      <div class="item">
          Ramen - 8 Credits
          <button type="button" class="btn" onclick="change('ramen', -1)">-</button>
          <span id="ramen">0</span>
          <button type="button" class="btn" onclick="change('ramen', 1)">+</button>
      </div>
      <div class="item">
          Sushi - 10 Credits
          <button type="button" class="btn" onclick="change('sushi', -1)">-</button>
          <span id="sushi">0</span>
          <button type="button" class="btn" onclick="change('sushi', 1)">+</button>
      </div>
      <div class="item">
          Dumplings - 6 Credits
          <button type="button" class="btn" onclick="change('dumplings', -1)">-</button>
          <span id="dumplings">0</span>
          <button type="button" class="btn" onclick="change('dumplings', 1)">+</button>
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
