<?php
// auth_terminal.php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// LOGIN CHECK

if (!isset($_SESSION['password'], $_SESSION['usertype'])) {
    header('Location: user_login.php');
    exit;
}

// TOKEN GENERATION

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>