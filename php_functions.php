<?php
function changePage($seite = null) {
    if ($seite === null) {
        if (isset($_GET['page'])) {
            $seite = $_GET['page'];
        } else {
            $seite = 'index.php'; // Fallback
        }
    }
    header("Location: " . $seite);
    exit();
}

// Automatische Weiterleitung bei direktem Aufruf
changePage();
?>