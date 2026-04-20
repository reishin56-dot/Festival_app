<?php
session_start();
if (!isset($_SESSION['ticket_id'])) {
    header('Location: index.php');
    exit;
}
require 'db.php';

$stmt = $pdo->prepare('SELECT credits FROM tickets WHERE id = ?');
$stmt->execute([$_SESSION['ticket_id']]);
$ticket = $stmt->fetch();
$credits = $ticket['credits'] ?? 0;
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Menü</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <h1>Menü</h1>
    <p>Willkommen, <?= htmlspecialchars($_SESSION['name']) ?>!</p>
    <p><strong>Guthaben: <?= number_format($credits, 2) ?> Credits</strong></p>
    <table>
      <tr><td><button onclick="goToPage('order_food.php')">Essen bestellen</button></td></tr>
      <tr><td><button onclick="goToPage('notification.php')">Meine Bestellungen</button></td></tr>
      <tr><td><button onclick="goToPage('share_orders.php')">Freunde einladen</button></td></tr>
      <tr><td><button onclick="goToPage('buy_credits.php')">Guthaben aufladen</button></td></tr>
    </table>
    <button onclick="window.location.href='index.php'">Abmelden</button>
  </div>
  <script src="script.js"></script>
</body>
</html>
