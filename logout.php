<?php
require_once 'config.php';
$_SESSION[date('Y-m-d H:i:s')] = $users['logout_at'];
session_destroy();
header('Location: index.php');
