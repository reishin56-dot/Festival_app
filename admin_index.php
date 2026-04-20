<!DOCTYPE html>
<html lang="de">

<head>
  <meta charset="UTF-8">
  <title>Admin Index</title>
  <link rel="stylesheet" href="style.css">
</head>
<php require "php_functions.php"; ?>
<body>
  <div class="container">
    <h1><!--🔧--> Admin Index</h1>
    <p>Willkommen im Admin-Be ich. Bitte wählen Sie eine Option:</p>
    <table>
      <tr>
        <td><button onclick="goToPage('engpasssteuerung.php')">Engpässe</button></td>
      </tr>
      <tr>
        <td><button onclick="goToPage('statistik.php')"> Statistik</button></td>
      </tr>
      <tr>
        <td><button onclick="goToPage('role_selection.php')"> Zurück zum Hauptmenü</button></td>
      </tr>
      

    </table>

  

  <script src="script.js"></script>
</body>

</html>