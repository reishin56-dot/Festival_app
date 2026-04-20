<?php
session_start();
if (!isset($_SESSION['ticket_id'])) { header('Location: index.php'); exit; }
require 'db.php';

$fehler = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mengen = [
        7 => (int)($_POST['qty_wasser']   ?? 0),  // Wasser    5 Credits
        8 => (int)($_POST['qty_bier']     ?? 0),  // Bier      8 Credits
        9 => (int)($_POST['qty_cocktail'] ?? 0),  // Cocktail 12 Credits
    ];
    $preise = [7 => 5.00, 8 => 8.00, 9 => 12.00];

    $gesamt = 0;
    foreach ($mengen as $pid => $menge) {
        $gesamt += $menge * $preise[$pid];
    }

    if ($gesamt == 0) {
        $fehler = 'Bitte mindestens ein Produkt auswählen.';
    } else {
        $credits = $pdo->prepare('SELECT credits FROM tickets WHERE id = ?');
        $credits->execute([$_SESSION['ticket_id']]);
        $credits = $credits->fetchColumn();

        if ($credits < $gesamt) {
            $fehler = 'Nicht genug Credits! Guthaben: ' . number_format($credits, 2);
        } else {
            $standStmt = $pdo->query("SELECT id FROM staende WHERE kategorie='getraenke' AND aktiv=1 ORDER BY wartezeit ASC LIMIT 1");
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
