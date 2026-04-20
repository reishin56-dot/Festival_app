<?php
$host     = "localhost";
$benutzer = "root";        // XAMPP Standard
$passwort = "";            // XAMPP Standard (kein Passwort)
$datenbank = "festival";

$verbindung = mysqli_connect($host, $benutzer, $passwort, $datenbank);

// Prüfen ob Verbindung geklappt hat
if (!$verbindung) {
    die("Verbindung fehlgeschlagen: " . mysqli_connect_error());
}
?>