<?php
require_once 'auth_terminal.php';
require_once 'config.php';

// check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// check role
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'admin') {
    http_response_code(403);
    echo "403 Forbidden - Admins only";
    exit();
}

$auditSmt = $pdo->prepare("SELECT id, user_id, action, details, created_at FROM audit_logs ORDER BY created_at DESC LIMIT ? OFFSET ?");
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 100;
$offset = ($page - 1) * $limit;
$auditSmt = $pdo->prepare("
    SELECT id, user_id, action, details, created_at 
    FROM audit_logs 
    ORDER BY created_at DESC 
    LIMIT ? OFFSET ?
");
$auditSmt->bindValue(1, $limit, PDO::PARAM_INT);
$auditSmt->bindValue(2, $offset, PDO::PARAM_INT);
$auditSmt->execute();
$totalStmt = $pdo->query("SELECT COUNT(*) FROM audit_logs");
$totalRows = $totalStmt->fetchColumn();
$totalPages = ceil($totalRows / $limit);
$audit = $auditSmt->fetchAll(PDO::FETCH_ASSOC);


function e($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        margin: 0;
        padding: 0;
        min-height: 100vh;
        background-color: white;
        padding-bottom: 100px;
    }

    body::before {
        content: "";
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: linear-gradient(180deg, rgb(255, 255, 255) 0%, rgba(255, 255, 255, 0.8) 100%);
        z-index: -1;
    }

    .navbar {
        background-color: #ff6600 !important;
        height: 80px; 
        padding: 0 20px;
        display: flex;
        align-items: center;
        z-index: 1050;
    }

    .navbar-brand {
        display: flex;
        align-items: center;
        height: 100%; 
        padding: 0; margin: 0;
        flex-shrink: 0;
        margin-left: -15px;
    }

    .nav-logo {
        height: 100px; 
        width: auto;
        max-height: 200%; 
        object-fit: contain;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .header-icon-btn {
        position: relative;
        background: none;
        border: none;
        color: #333;
        padding: 8px;
        transition: 0.2s;
    }

    .audit-table-container {
    max-height: 600px;   /* adjust as needed */
    overflow-y: auto;
    border-radius: 10px;
}

</style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container-fluid d-flex justify-content-between align-items-center px-4">
            <img src="images/logo.png" alt="Logo" class="nav-logo"> 
        <div class="header-right">
                <form method="POST" action="admin_dashboard.php" class="m-0">
                    <input type="hidden" name="csrf_token"
                        value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <button type="submit"
                        class="dropdown-item text-danger fw-bold border-0 bg-transparent w-100 text-start">
                            Home
                    </button>
                </form>           
            <div class="dropdown">
                <button class="header-icon-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 21C20 19.6044 20 18.9067 19.8278 18.3389C19.44 17.0605 18.4395 16.06 17.1611 15.6722C16.5933 15.5 15.8956 15.5 14.5 15.5H9.5C8.10444 15.5 7.40665 15.5 6.83886 15.6722C5.56045 16.06 4.56004 17.0605 4.17224 18.3389C4 18.9067 4 19.6044 4 21M16.5 7.5C16.5 9.98528 14.4853 12 12 12C9.51472 12 7.5 9.98528 7.5 7.5C7.5 5.01472 9.51472 3 12 3C14.4853 3 16.5 5.01472 16.5 7.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>

                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-2 p-3" style="border-radius: 15px; min-width: 200px;">
                    <li class="px-2 mb-2"><small class="text-muted text-uppercase fw-bold">User Information</small></li>
                    <li><span class="dropdown-item fw-bold text-primary">@<?= e($_SESSION['username'] ?? 'User') ?></span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="logout.php" class="m-0">
                            <input type="hidden" name="csrf_token"
                                value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                            <button type="submit"
                                class="dropdown-item text-danger fw-bold border-0 bg-transparent w-100 text-start">
                                Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="orders-container">
        <div class="col-12">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
            <div>
                <h3 class="fw-bold mb-0">Audit Logs</h3>
                <p class="text-muted small">View all action logs</p>
            </div>
        </div>
            <div class="table-responsive audit-table-container">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>User_ID</th>
                        <th>Action</th>
                        <th>Details</th>

                    </tr>
                </thead>
                <tbody id="productsTbody">
                    <?php if (empty($audit)): ?>
                        <tr><td colspan="8" class="text-center py-4 text-muted">No audit logs found yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($audit as $a): ?>
                            <tr>
                                <td class="fw-bold"><?= e((int)$a['id']) ?></td>
                                <td class="fw-bold"><?= e((int)$a['user_id']) ?></td>
                                <td class="fw-bold"><?= e($a['action']) ?></td>
                                <td class="fw-small"><?= e($a['details']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                                </td>
                </tbody>
            </table>
            <nav>
                <ul class="pagination justify-content-center mt-3">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- CSRF TOKEN -->
<script>
const csrfToken = <?= json_encode($_SESSION['csrf_token']) ?>;
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

const userid = "<?= $_SESSION['user_id'] ?>";
const socket = new WebSocket("ws://localhost:3000?usertype=admin");

socket.onopen = function() {
    socket.send(JSON.stringify({ event: "client_connected", userid: userid }));
};

// =========================
// WEBSOCKET MESSAGE HANDLER 
// =========================
socket.onmessage = function(event) {
    const data = JSON.parse(event.data);

    console.log("WS Message:", data);

    // Dynamic extraction logic for dashboard panels
    if (data.event === "initial_logs") {
        const feed = document.getElementById('activityFeed');
        if (feed) {
            feed.innerHTML = ''; 
            data.logs.forEach(log => appendToFeed(log.message));
        }
    }

    const refreshEvents = [
        "order_updated",
        "order_deleted",
        "order_confirmed",

        "product_created",
        "product_updated",
        "product_deleted",

        "category_created",
        "category_updated",
        "category_deleted",
    ];
    if (refreshEvents.includes(data.event)) {
        location.reload();
    }
};
</script>
</body>
</html>