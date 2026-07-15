<?php
// config.php

if(!isset($_SESSION))
    session_start();

$dotenvPath = __DIR__ . '/.env';
if (file_exists($dotenvPath)) {
    foreach (file($dotenvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        $parts = explode('=', $line, 2);
        if (count($parts) !== 2) {
            continue;
        }

        [$name, $value] = $parts;
        $name = trim($name);
        $value = trim($value);
        $value = trim($value, "'\"");

        if (getenv($name) === false) {
            putenv("{$name}={$value}");
        }
        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
        }
    }
}

function require_env(string $name): string
{
    $value = getenv($name);
    if ($value === false) {
        $value = $_ENV[$name] ?? false;
    }
    if ($value === false) {
        die("Missing required environment variable: {$name}");
    }
    return $value;
}

$dsn = getenv('DB_DSN');
if ($dsn === false) {
    $dbHost = require_env('DB_HOST');
    $dbName = require_env('DB_NAME');
    $dbCharset = getenv('DB_CHARSET') ?: 'utf8mb4';
    $dsn = "mysql:host={$dbHost};dbname={$dbName};charset={$dbCharset}";
}
$dbUser = require_env('DB_USER');
$dbPass = require_env('DB_PASS');

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (Exception $e) {
    die('DB connection failed');
}

// ---- Admin credentials (simple) ----

define('ADMIN_USER', require_env('ADMIN_USER'));

define('ADMIN_PASS', require_env('ADMIN_PASS'));

// ---- WebSocket push bridge ----
// Make sure this function is defined ONLY ONCE.
if (!function_exists('send_ws_message')) {
    function send_ws_message(array $payload): void
    {
        $fp = @fsockopen('127.0.0.1', 9001, $errno, $errstr, 0.5);
        if (!$fp) {
            return;
        }
        fwrite($fp, json_encode($payload) . "\n");
        fclose($fp);
    }
}
