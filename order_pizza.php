<?php
session_start();
if (!isset($_SESSION['ticket_id'])) { header('Location: index.php'); exit; }
require 'db.php';

$fehler  = '';
$erfolg  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mengen = [
        1 => (int)($_POST['qty_margherita'] ?? 0),  // Margherita  5 Credits
        3 => (int)($_POST['qty_spezial']    ?? 0),  // Spezial    12 Credits
        2 => (int)($_POST['qty_pepperoni']  ?? 0),  // Pepperoni   8 Credits
    ];
    $preise = [1 => 5.00, 3 => 12.00, 2 => 8.00];

    $gesamt = 0;
    foreach ($mengen as $pid => $menge) {
        $gesamt += $menge * $preise[$pid];
    }

    if ($gesamt == 0) {
        $fehler = 'Bitte mindestens ein Produkt auswählen.';
    } else {
        $stmt = $pdo->prepare('SELECT credits FROM tickets WHERE id = ?');
        $stmt->execute([$_SESSION['ticket_id']]);
        $credits = $stmt->fetchColumn();

        if ($credits < $gesamt) {
            $fehler = 'Nicht genug Credits! Guthaben: ' . number_format($credits, 2);
        } else {
            // Stand mit kürzester Wartezeit wählen
            $standStmt = $pdo->query("SELECT id FROM staende WHERE kategorie='pizza' AND aktiv=1 ORDER BY wartezeit ASC LIMIT 1");
            $stand_id  = $standStmt->fetchColumn();

            $pdo->beginTransaction();
            $pdo->prepare('INSERT INTO bestellungen (ticket_id, stand_id, gesamt_credits) VALUES (?,?,?)')
                ->execute([$_SESSION['ticket_id'], $stand_id, $gesamt]);
            $bid = $pdo->lastInsertId();

            foreach ($mengen as $pid => $menge) {
                if ($menge > 0) {
                    $pdo->prepare('INSERT INTO bestellpositionen (bestellung_id, produkt_id, menge, einzelpreis) VALUES (?,?,?,?)')
                        ->execute([$bid, $pid, $menge, $preise[$pid]]);
                }
            }
            $pdo->prepare('UPDATE tickets SET credits = credits - ? WHERE id = ?')
                ->execute([$gesamt, $_SESSION['ticket_id']]);
            $pdo->commit();

            header('Location: notification.php?erfolg=1');
            exit;
        }
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
    <title>Pizza</title>
    <link rel="stylesheet" href="style.css">
</head>
<php require "php_functions.php"; ?>
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
