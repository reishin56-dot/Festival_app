<?php
$host      = "localhost";
$benutzer  = "root";
$passwort  = "";
$datenbank = "festival_db";

$verbindung = mysqli_connect($host, $benutzer, $passwort, $datenbank);
if (!$verbindung) {
    die("Verbindung fehlgeschlagen: " . mysqli_connect_error());
}
mysqli_set_charset($verbindung, "utf8mb4");

// PDO für komplexere Abfragen
$pdo = new PDO("mysql:host=$host;dbname=$datenbank;charset=utf8mb4", $benutzer, $passwort);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
