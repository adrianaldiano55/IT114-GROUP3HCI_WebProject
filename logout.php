<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// CSRF check
if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    http_response_code(403);
    exit('Invalid CSRF token');
}

if (!empty($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("
        INSERT INTO audit_logs (user_id, action, details, created_at)
        VALUES (?, 'LOGOUT', ?, NOW())
    ");

    $stmt->execute([
        $_SESSION['user_id'],
        "User logged out"
    ]);

    // optional: update logout time
    $stmt = $pdo->prepare("UPDATE users SET logout_at = NOW() WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
}
// Clear session data
$_SESSION = [];

// Delete session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy session
session_destroy();

// Redirect
header("Location: index.php");
exit;
