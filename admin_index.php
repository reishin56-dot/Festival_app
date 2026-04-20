<?php
require 'session.php';
nurFuerAdmin();
?>
<!DOCTYPE html>
<html lang="de">

<head>
  <meta charset="UTF-8">
  <title>Admin Index</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <div class="container">
    <h1><!--🔧--> Admin Index</h1>
    <p>Willkommen im Admin-Bereich. Bitte wählen Sie eine Option:</p>
    <table>
      <tr>
        <td><button onclick="goToPage('engpasssteuerung.php')">Engpässe</button></td>
      </tr>
      <tr>
        <td><button onclick="goToPage('statistik.php')"> Statistik</button></td>
      </tr>
      <tr>
        <td><button onclick="window.location.href='logout.php'">Abmelden</button></td>
      </tr>
      

    </table>

  

  <script src="script.js"></script>
</body>

</html>