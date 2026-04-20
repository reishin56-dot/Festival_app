<!DOCTYPE html>
<html lang="de">

<head>
  <meta charset="UTF-8">
  <title>Menü</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>

  <div class="container">
    <h1><!--📱--> Menü</h1>
    <p>Was möchten sie tun?</p>
    <table>

      <tr>
        <td><button onclick="goToPage('order_food.php')">Essen bestellen</button></td>
      </tr>
      <tr>
        <td><button onclick="goToPage('notification.php')">Meine Bestellungen</button></td>
      </tr>
      <tr>
        <td><button onclick="goToPage('share_orders.php')">Freunde einladen</button></td>
      </tr>
      <tr>
        <td><button onclick="goToPage('buy_credits.php')">Guthaben aufladen</button></td>
      </tr>
    </table>
    <button onclick="goBack()">Zurück</button>
  </div>

  <script src="script.js"></script>
</body>

</html>