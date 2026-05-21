<?php
require_once 'auth_terminal.php';
require_once 'config.php';

// Fetch orders
$user_id = $_SESSION['user_id'];

// 1. Fetch products as a Key-Pair (ID => Name)
$productStmt = $pdo->query("SELECT prod_id, name FROM products");
$products = $productStmt->fetchAll(PDO::FETCH_KEY_PAIR); 
$status = $_GET['status'] ?? 'PROCESSING';

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
             $summary[] = $prodName . ' x ' . $item['quantity'];
        }
    }
        $order['product_summary'] = !empty($summary) ? implode('<br>', $summary) : 'No items found';
    }
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
                    <li><span class="dropdown-item fw-bold text-primary">@<?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger fw-bold" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-4">
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
                            $summary = $o['product_summary'] ?? 'No items';
                            $due = (!empty($o['due_at']) && $o['due_at'] != '00:00:00')? date('h:i A', strtotime($o['due_at'])): 'Not set';
                            $addr = (!empty($o['address']) && $o['address'] != 'NULL') ? htmlspecialchars($o['address']) : 'Store Pickup';
                        ?> 
                            <tr data-order-id="<?= (int)$o['order_id'] ?>" data-status="<?= $rawStatus ?>">
                                <td class="fw-bold">#<?= $displayStr ?></td>
                                <td class="fw-bold text-danger">₱<?= number_format((float)($o['price_total'] ?? 0), 2) ?></td>
                                <td class="small"><?= $summary ?></td> 
                                <td><span class="status-badge <?= $badgeClass ?>"><?= $rawStatus ?></span></td>
                                <td class="small text-muted"><?= date('M d, Y h:i A', strtotime($o['created_at'])) ?></td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button class="btn btn-sm btn-outline-secondary fw-bold px-3 rounded-pill" 
                                            onclick="viewOrderDetails(
                                                '<?= $displayStr ?>',
                                                '<?= addslashes($summary) ?>',
                                                '<?= $due ?>',
                                                '<?= addslashes($addr) ?>',
                                                '<?= number_format((float)$o['price_total'], 2) ?>'
                                            )">
                                            Details
                                        </button>
                                        <?php if ($rawStatus === 'PROCESSING'): ?>
                                            <button class="btn btn-sm btn-outline-primary px-3 rounded-pill"
                                                onclick="openUpdateModal(
                                                    <?= (int)$o['order_id'] ?>,
                                                    '<?= $due ?>',
                                                    '<?= addslashes($addr) ?>'
                                                )">
                                                Accept
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($rawStatus === 'PENDING'): ?>
                                            <button class="btn btn-sm btn-outline-primary px-3 rounded-pill"
                                                onclick="openUpdateModal(
                                                    <?= (int)$o['order_id'] ?>,
                                                    '<?= $due ?>',
                                                    '<?= addslashes($addr) ?>'
                                                )">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
let cart = {}; 
const userid = "<?= $_SESSION['user_id'] ?>";

const socket = new WebSocket("ws://localhost:3000?usertype=staff");

socket.onopen = function() {
    socket.send(JSON.stringify({ event: "client_connected", userid: userid }));
};

// =========================
// WEBSOCKET MESSAGE HANDLER 
// =========================
socket.onmessage = function(event) {
    const data = JSON.parse(event.data);

    console.log("WS Message:", data);

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
// =========================
// SET TABLE STATUS (NEW)
// =========================
function setStatusFilter(status) {
    window.location.href = "?status=" + status;
};


// =========================
// VIEW DETAILS (UNCHANGED)
// =========================
function viewOrderDetails(id, summary, due, addr, total) {
    document.getElementById('detId').innerText = id;
    document.getElementById('detSummary').innerHTML = summary; 
    document.getElementById('detDue').innerText = due;
    document.getElementById('detAddr').innerText = addr;
    document.getElementById('detTotal').innerText = total;
    new bootstrap.Modal(document.getElementById('orderDetailsModal')).show();
};

// =========================
// OPEN UPDATE MODAL
// =========================
function openUpdateModal(orderId, due, address) {
    document.getElementById("updateOrderId").value = orderId;

    document.getElementById("deliveryDueTime").innerText = due;
    document.getElementById("deliveryAddress").innerText = address;

    new bootstrap.Modal(document.getElementById('deliveryModal')).show();
};


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
</script>
</body>
</html>