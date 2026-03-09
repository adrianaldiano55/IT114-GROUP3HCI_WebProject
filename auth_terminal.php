<?php
// auth_terminal.php
require_once 'config.php';

if (!isset($_SESSION['password'], $_SESSION['usertype'])) {
    header('Location: user_login.php');
    exit;
}