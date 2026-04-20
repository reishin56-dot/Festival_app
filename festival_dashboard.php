<!DOCTYPE html>
<html lang="de">

<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link rel="stylesheet" href="style.css">
</head>

<php require "php_functions.php"; ?>
<body>

  <div class="container">
    <h1><!-- 🎉--> Dashboard</h1>
    <p>Hier bist du im Dashboard! </p>


    <br>

    <button onclick="goToPage('engpasssteuerung.php')">Engpasssteuerung</button>
     <button onclick="goToPage('statistik.php')">Statistik</button>

  </div>





  <footer>
  <p>© 2026 Festival App – by Abilas Sivarajah & Lucas Kessler & Michael Linn</p>
</footer>



  <script src="script.js"></script>
</body>

</html>