<?php
// ── Session starten ───────────────────────────────────────────────────────────

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Hilfsfunktionen ───────────────────────────────────────────────────────────

function istEingeloggt() {
    return isset($_SESSION['ticket_id']);
}

function istAdmin() {
    return isset($_SESSION['admin']) && $_SESSION['admin'] === true;
}

function nurFuerBenutzer() {
    if (!istEingeloggt()) {
        header('Location: index.php');
        exit;
    }
}

function nurFuerAdmin() {
    if (!istAdmin()) {
        header('Location: admin_login.php');
        exit;
    }
}

function logout() {
    $_SESSION = [];
    session_destroy();
    header('Location: role_selection.php');
    exit;
}
