<?php
session_start();
if (!isset($_SESSION['ticket_id'])) { header('Location: index.php'); exit; }
require 'db.php';

$fehler = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mengen = [
        4 => (int)($_POST['qty_ramen']     ?? 0),  // Ramen      8 Credits
        5 => (int)($_POST['qty_sushi']     ?? 0),  // Sushi     10 Credits
        6 => (int)($_POST['qty_dumplings'] ?? 0),  // Dumplings  6 Credits
    ];
    $preise = [4 => 8.00, 5 => 10.00, 6 => 6.00];

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
            $standStmt = $pdo->query("SELECT id FROM staende WHERE kategorie='asia' AND aktiv=1 ORDER BY wartezeit ASC LIMIT 1");
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
