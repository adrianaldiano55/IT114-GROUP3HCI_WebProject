<?php
require_once 'auth_terminal.php';
require_once 'config.php';

// check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// check role
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'staff') {
    http_response_code(403);
    echo "403 Forbidden - Staff only";
    exit();
}

$catStmt = $pdo->query("SELECT categ_id, name, image_path FROM categories ORDER BY name");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

$itemStmt = $pdo->query("
    SELECT m.prod_id, m.name, m.price, m.stock, m.discount, m.image_path,
        c.name AS category_name, c.categ_id AS category
    FROM products m
    LEFT JOIN categories c ON c.categ_id = m.category
    ORDER BY c.name, m.name
");
$items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

$itemsByCategory = [];
foreach ($items as $item) {
    $itemsByCategory[$item['category']][] = $item;
}
// Fetch orders
$user_id = $_SESSION['user_id'];

// 1. Fetch products as a Key-Pair (ID => Name)
$productStmt = $pdo->query("SELECT prod_id, name FROM products");
$products = $productStmt->fetchAll(PDO::FETCH_KEY_PAIR); 
$status = htmlspecialchars($_GET['status'] ?? 'PROCESSING', ENT_QUOTES, 'UTF-8');

 $ordStmt = $pdo->prepare("
SELECT *
FROM orders
WHERE UPPER(status) = ?
ORDER BY created_at DESC
");
$ordStmt->execute([$status]);
$orders = $ordStmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Only fetch items if there are orders to avoid SQL errors
if (!empty($orders)) {
    $orderIds = array_column($orders, 'order_id');
    $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
    $oritStmt = $pdo->prepare("
        SELECT *
        FROM order_items
        WHERE order_id IN ($placeholders)
    ");
    $oritStmt->execute($orderIds);
    $order_items = $oritStmt->fetchAll(PDO::FETCH_ASSOC);

 // 3. Map items to orders
foreach ($orders as &$order) {
    $summary = [];

    foreach ($order_items as $item) {
        if ($item['order_id'] == $order['order_id']) {

            $pId = $item['product'];
            $prodName = $products[$pId] ?? 'Unknown Item';

            $summary[] = [
                'name' => $prodName,
                'qty'  => (int)$item['quantity']
            ];
        }
    }

    $order['product_summary'] = $summary;
}
unset($order);
}

function e_html($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function e_js($str) {
    return json_encode((string)$str, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

    .cart-badge {
        position: absolute;
        top: 0; right: 0;
        background: #ef4444;
        color: white;
        font-size: 0.7rem;
        padding: 2px 6px;
        border-radius: 50%;
        font-weight: bold;
    }

    .orders-container {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        margin-top: 30px;
    }

    .status-badge {
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    /* Updated Status Colors for your ENUM */
    .status-pending { background: #e2e3e5; color: #383d41; }
    .status-processing { background: #fff3cd; color: #856404; }
    .status-completed { background: #d4edda; color: #155724; }
    .status-cancelled { background: #f8d7da; color: #721c24; }

    .btn-group .btn {
        border-radius: 10px !important;
        margin-right: 5px;
        font-weight: 600;
    }

    .search-input {
        border-radius: 10px;
        border: 1px solid #ddd;
        padding: 8px 15px;
    }
        #activityFeed {
        height: 100px;
        overflow-y: auto;
        font-size: 0.8rem;
    }

    .feed-item {
        padding: 5px 0;
        border-bottom: 1px solid #f8f9fa;
        animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
    to { opacity: 1; }
    }

    /* Analytics Specific Styles */
    .insight-card {
        background: #fff;
        border-radius: 15px;
        padding: 15px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        height: 100%;
        border: 1px solid #f0f0f0;
    }
    #activityFeed {
        height: 100px;
        overflow-y: auto;
        font-size: 0.8rem;
    }
    .feed-item {
        padding: 5px 0;
        border-bottom: 1px solid #f8f9fa;
        animation: fadeIn 0.5s ease;
    }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container-fluid d-flex justify-content-between align-items-center px-4">
        <img src="images/logo.png" alt="Logo" class="nav-logo"> 
        <div class="header-right">
            <div class="dropdown">
                <button class="header-icon-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 21C20 19.6044 20 18.9067 19.8278 18.3389C19.44 17.0605 18.4395 16.06 17.1611 15.6722C16.5933 15.5 15.8956 15.5 14.5 15.5H9.5C8.10444 15.5 7.40665 15.5 6.83886 15.6722C5.56045 16.06 4.56004 17.0605 4.17224 18.3389C4 18.9067 4 19.6044 4 21M16.5 7.5C16.5 9.98528 14.4853 12 12 12C9.51472 12 7.5 9.98528 7.5 7.5C7.5 5.01472 9.51472 3 12 3C14.4853 3 16.5 5.01472 16.5 7.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-2 p-3" style="border-radius: 15px; min-width: 200px;">
                    <li class="px-2 mb-2"><small class="text-muted text-uppercase fw-bold">User Information</small></li>
                    <li><span class="dropdown-item fw-bold text-primary">@<?= e_html($_SESSION['username'] ?? 'User') ?></span></li>
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
    <!-- Live Sync, Activity Feed, and Stock Tracking Panel Area -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="insight-card text-center">
                <h6 class="text-muted fw-bold small">TOTAL ORDERS</h6>
                <h2 class="fw-bold text-primary mb-0" id="liveOrderCount">0</h2>
                <small class="text-success small">● Live Sync</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="insight-card">
                <h6 class="text-muted fw-bold small mb-1">ACTIVITY FEED</h6>
                <div id="activityFeed"><div class="text-muted small">No recent activity...</div></div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="insight-card">
                <h6 class="text-muted fw-bold small mb-1">STOCK PER CATEGORY</h6>
                <canvas id="stockChart" style="max-height: 100px;"></canvas>
            </div>
        </div>
    </div>

    <div class="orders-container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
            <div>
                <h3 class="fw-bold mb-0">Order Dashboard</h3>
                <p class="text-muted small">Manage and track customer orders</p>
            </div>
        </div>
        <div class="mb-4">
            <div class="btn-group" role="group">
                <button class="btn btn-outline-warning status-filter-btn" data-status="PROCESSING" onclick="setStatusFilter('PROCESSING')">Processing</button>
                <button class="btn btn-outline-warning status-filter-btn" data-status="PENDING" onclick="setStatusFilter('PENDING')">Pending</button>
                <button class="btn btn-outline-warning status-filter-btn" data-status="COMPLETED" onclick="setStatusFilter('COMPLETED')">Completed</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Order #</th>
                        <th>Total</th>
                        <th>Order Items</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody id="ordersTbody">
                    <?php if (empty($orders)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">No orders found yet. Start ordering now!</td></tr>
                    <?php else: ?>
                        <?php foreach ($orders as $o): 
                            $displayStr = str_pad($o['order_id'], 4, '0', STR_PAD_LEFT);
                            $rawStatus = strtoupper(trim($o['status'] ?? 'PENDING'));
                            $badgeClass = 'status-' . strtolower($rawStatus);
                            $summary = $o['product_summary'] ?? [];
                            $dueRaw = $o['due_at'] ?? '';
                            $due = (!empty($dueRaw) && $dueRaw !== '00:00:00' && $dueRaw !== '0000-00-00 00:00:00')
                                ? date('h:i A', strtotime($dueRaw))
                                : 'Not set';
                            $addr = (!empty($o['address']) && $o['address'] !== 'NULL') ? e_html($o['address']) : 'Store Pickup';
                        ?> 
                            <tr
                                data-order-id="<?= (int)$o['order_id'] ?>"
                                data-display="<?= htmlspecialchars($displayStr, ENT_QUOTES, 'UTF-8') ?>"
                                data-summary="<?= e_html(implode("\n", array_map(fn($i) => $i['name'].' x '.$i['qty'], $o['product_summary'] ?? []))) ?>"
                                data-due="<?= e_html($due) ?>"
                                data-addr="<?= e_html($addr) ?>"
                                data-total="<?= e_html(number_format((float)$o['price_total'], 2)) ?>"
                                data-status="<?= e_html($rawStatus) ?>"
                            >
                                <td class="fw-bold">#<?= $displayStr ?></td>
                                <td class="fw-bold text-danger">₱<?= number_format((float)($o['price_total'] ?? 0), 2) ?></td>
                                <td class="small">
                                <?php
                                $lines = array_map(function($i){
                                    return e_html($i['name']) . ' x ' . (int)$i['qty'];
                                }, $o['product_summary'] ?? []);

                                echo nl2br(implode("\n", $lines));
                                ?>
                                </td>
                                <td>
                                    <span class="status-badge <?= $badgeClass ?>">
                                        <?= htmlspecialchars($rawStatus) ?>
                                    </span>
                                </td>
                                <td class="small text-muted"><?= date('M d, Y h:i A', strtotime($o['created_at'])) ?></td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-sm btn-outline-secondary"
                                        onclick="viewOrderDetailsFromRow(this.closest('tr'))">
                                        Details
                                    </button>
                                        <?php if ($rawStatus === 'PROCESSING'): ?>
                                            <button class="btn btn-sm btn-outline-primary px-3 rounded-pill"
                                                onclick="openUpdateModalFromRow(this.closest('tr'))">
                                                Accept
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($rawStatus === 'PENDING'): ?>
                                            <button class="btn btn-sm btn-outline-primary px-3 rounded-pill"
                                                onclick="openUpdateModalFromRow(this.closest('tr'))">
                                                Complete
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="orderDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="fw-bold">Order #<span id="detId"></span> Summary</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="text-muted small d-block">Items Ordered</label>
                    <p class="fw-bold" id="detSummary"></p>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="text-muted small d-block">Estimated Time Delivery</label>
                        <p class="fw-bold" id="detDue"></p>
                    </div>
                    <div class="col-6">
                        <label class="text-muted small d-block">Total Amount</label>
                        <p class="fw-bold text-danger">₱<span id="detTotal"></span></p>
                    </div>
                </div>
                <div class="mb-0">
                    <label class="text-muted small d-block">Delivery Address</label>
                    <p class="fw-bold" id="detAddr"></p>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button class="btn btn-dark w-100 rounded-pill" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deliveryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <input type="hidden" id="updateOrderId">
            <div class="modal-header">
                <h5 class="fw-bold">Update Delivery Details</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <label class="form-label fw-bold">Due Time</label>
                <div id="deliveryDueTime" class="form-control mb-3 bg-light"></div>
                <label class="form-label fw-bold">Address</label>
                <div id="deliveryAddress" class="form-control mb-3 bg-light"></div>
            </div>
                <div class="modal-footer">
                <?php if ($status === 'PROCESSING'): ?>
                    <button class="btn btn-primary w-100" onclick="submitDeliveryUpdate()">
                        Accept Order
                    </button>
                <?php elseif ($status === 'PENDING'): ?>
                    <button class="btn btn-primary w-100" onclick="submitDeliveryUpdate()">
                        Complete Order
                    </button>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<!-- CSRF TOKEN -->
<script>
const csrfToken = <?= json_encode($_SESSION['csrf_token']) ?>;
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
let cart = {}; 
const userid = "<?= $_SESSION['user_id'] ?>";
let stockChart;

const categoryNames = <?= json_encode(
    array_column($categories, 'name', 'categ_id'),
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
) ?>;

// --- INITIALIZE CHART ---
function initStockChart() {
    const ctx = document.getElementById('stockChart').getContext('2d');
    stockChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [], 
            datasets: [{
                label: 'Qty',
                data: [],
                backgroundColor: '#ff6600',
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: { display: false }, y: { ticks: { font: { size: 9 } }, grid: { display: false } } }
        }
    });
}

const socket = new WebSocket("ws://localhost:3000?usertype=staff");


const categoryMap = <?= json_encode(
    array_column($categories, 'name', 'categ_id'),
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
) ?>;

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

    if (data.event === "live_stats") {
        const liveCounter = document.getElementById('liveOrderCount');
        if (liveCounter) {
            liveCounter.innerText = data.total_orders;
        }
    }

    if (data.event === "activity_alert") {
        appendToFeed(data.message);
    }

    if (data.event === "stock_update" && stockChart) {

        const labels = Object.keys(data.chartData).map(id => {
            return categoryNames[id] || `Cat ${id}`;
        });

        stockChart.data.labels = labels;
        stockChart.data.datasets[0].data = Object.values(data.chartData);

        stockChart.update('none');
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

function appendToFeed(msg) {
    const feed = document.getElementById('activityFeed');
    if (!feed) return;

    const div = document.createElement('div');
    div.className = 'feed-item';
    div.style.fontSize = "0.85rem";
    div.style.marginBottom = "5px";

    const span = document.createElement('span');
    span.textContent = msg;

    div.appendChild(span);

    feed.prepend(div);

    if (feed.childNodes.length > 5) {
        feed.removeChild(feed.lastChild);
    }
}
// =========================
// SET TABLE STATUS (NEW)
// =========================
function setStatusFilter(status) {
    window.location.href = "?status=" + status;
};


// =========================
// VIEW ROW DETAILS (CHANGED)
// =========================
function viewOrderDetailsFromRow(row) {
    const id = row.dataset.display || '';
    const summary = row.dataset.summary || 'No items';
    const due = row.dataset.due || 'Not set';
    const addr = row.dataset.addr || 'Store Pickup';
    const total = row.dataset.total || '0.00';

    const modal = document.getElementById('orderDetailsModal');

    document.getElementById('detId').innerText = id;
    document.getElementById('detSummary').innerText = summary;
    document.getElementById('detDue').innerText = due;
    document.getElementById('detAddr').innerText = addr;
    document.getElementById('detTotal').innerText = total;

    const bsModal = bootstrap.Modal.getOrCreateInstance(modal);
    bsModal.show();
}

// =========================
// OPEN MODAL (CHANGED)
// =========================
function openUpdateModalFromRow(row) {
    const orderId = row.dataset.orderId;
    const due = row.dataset.due;
    const addr = row.dataset.addr;

    document.getElementById("updateOrderId").value = orderId;

    document.getElementById("deliveryDueTime").innerText = due;
    document.getElementById("deliveryAddress").innerText = addr;

    new bootstrap.Modal(document.getElementById('deliveryModal')).show();
}


// =========================
// SUBMIT UPDATE
// =========================
function submitDeliveryUpdate() {
    const orderId = document.getElementById("updateOrderId").value;

    let orderstatus = "";
    <?php if ($status === 'PROCESSING'): ?>
        orderstatus = "PENDING";
    <?php elseif ($status === 'PENDING'): ?>
        orderstatus = "COMPLETED";
    <?php endif; ?>

    updateOrderDelivery(orderId, orderstatus);

    bootstrap.Modal.getInstance(
        document.getElementById('deliveryModal')
    ).hide();
}


// =========================
// SEND UPDATE VIA WS
// =========================
function updateOrderDelivery(orderId, orderstatus) {
    if (!socket || socket.readyState !== WebSocket.OPEN) {
        console.error("WebSocket not connected");
        return;
    }

    socket.send(JSON.stringify({
        type: "update_staff_delivery",
        csrf_token: csrfToken,
        payload: {
            order_id: orderId,
            status: orderstatus
        }
    }));
};


// =========================
// TIME FORMAT FIX
// =========================
function convertToTimeInput(timeStr) {
    if (!timeStr || timeStr === "Not set") return "";

    const [time, modifier] = timeStr.split(' ');
    let [hours, minutes] = time.split(':');

    if (modifier === 'PM' && hours !== '12') {
        hours = parseInt(hours, 10) + 12;
    }
    if (modifier === 'AM' && hours === '12') {
        hours = '00';
    }

    return `${hours.toString().padStart(2, '0')}:${minutes}`;
};

// Initialize stock chart component on ready
window.addEventListener('DOMContentLoaded', initStockChart);
</script>
</body>
</html>