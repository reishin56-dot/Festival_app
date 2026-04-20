<?php
$host     = "localhost";
$benutzer = "root";        // XAMPP Standard
$passwort = "";            // XAMPP Standard (kein Passwort)
$datenbank = "festival_db";

$verbindung = mysqli_connect($host, $benutzer, $passwort, $datenbank);

// Prüfen ob Verbindung geklappt hat
if (!$verbindung) {
    die("Verbindung fehlgeschlagen: " . mysqli_connect_error());
}
?>
$pdo = new PDO('mysql:host=localhost;dbname=festival_db;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
