<?php
require 'session.php';

// Admin-Zugangsdaten (in einem echten Projekt: aus der Datenbank)
define('ADMIN_BENUTZER', 'admin');
define('ADMIN_PASSWORT', 'festival2026');

$fehler = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $benutzer = trim($_POST['benutzer'] ?? '');
    $passwort = $_POST['passwort'] ?? '';

    if ($benutzer === ADMIN_BENUTZER && $passwort === ADMIN_PASSWORT) {
        $_SESSION['admin'] = true;
        header('Location: admin_index.php');
        exit;
    } else {
        $fehler = 'Falscher Benutzername oder Passwort!';
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Admin Login</h1>

    <?php if ($fehler): ?>
      <p style="color:red;"><?= htmlspecialchars($fehler) ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="benutzer" placeholder="Benutzername" required><br><br>
        <input type="password" name="passwort" placeholder="Passwort" required><br><br>
        <button type="submit">Anmelden</button>
    </form>
    <br>
    <button onclick="window.location.href='role_selection.php'">Zurück</button>
</div>
<script src="script.js"></script>
</body>
</html>
