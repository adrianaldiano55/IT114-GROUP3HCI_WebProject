<?php
require_once 'auth_terminal.php';
require_once 'config.php';

// Fetch orders
$user_id = $_SESSION['user_id'];

// 1. Fetch products as a Key-Pair (ID => Name)
$productStmt = $pdo->query("SELECT prod_id, name FROM products");
$products = $productStmt->fetchAll(PDO::FETCH_KEY_PAIR); 

$ordStmt = $pdo->prepare("
    SELECT *
    FROM orders
    WHERE customer_id = ?
    ORDER BY created_at DESC
");
$ordStmt->execute([$user_id]);
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

// Fetch delivery staff
$staffStmt = $pdo->query("
    SELECT user_id, username
    FROM users
    WHERE usertype = 'staff'
");
$staffList = $staffStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
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
<body onload="checkDeletedOrders()">

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container-fluid d-flex justify-content-between align-items-center px-4">
        <a class="navbar-brand p-0" href="customer_dashboard.php">
            <img src="images/logo.png" alt="Logo" class="nav-logo"> 
        </a>

        <div class="header-right">
            <button class="header-icon-btn" data-bs-toggle="modal" data-bs-target="#cartItemsModal" onclick="renderCartModal()">
                <svg width="24" height="24" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.347 18.31a1.847 1.847 0 1 0 0-3.693 1.847 1.847 0 0 0 0 3.693M14.503 18.31a1.847 1.847 0 1 0 0-3.694 1.847 1.847 0 0 0 0 3.694" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M14.503 14.617H4.347V1.69H2.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="m4.347 3.537 12.926.923-.923 6.463H4.347" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
                <span class="cart-badge d-none" id="cartBadgeCount">0</span>
            </button>

            <a href="customer_dashboard.php" class="btn btn-sm btn-dark rounded-pill px-3 fw-bold" style="font-size: 0.8rem;">Back to Menu</a>

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
                <h3 class="fw-bold mb-0">My Purchase History</h3>
                <p class="text-muted small">Manage and track your recent orders</p>
            </div>
            <div class="d-flex gap-2">
                <input type="text" id="orderSearch" class="form-control search-input" placeholder="Search Order #">
            </div>
        </div>

        <div class="mb-4">
            <div class="btn-group" role="group">
                <button class="btn btn-outline-warning active status-filter-btn" data-status="ALL" onclick="setStatusFilter('ALL')">All Orders</button>
                <button class="btn btn-outline-warning status-filter-btn" data-status="ONGOING" onclick="setStatusFilter('ONGOING')">On-going</button>
                <button class="btn btn-outline-warning status-filter-btn" data-status="CLAIMED" onclick="setStatusFilter('CLAIMED')">Claimed</button>
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
                                        <?php if ($rawStatus === 'PROCESSING' || $rawStatus === 'COMPLETED'): ?>
                                            <span class="fw-bold text-success px-2" style="cursor: default;">COD</span>
                                        <?php endif; ?>
                                        <button class="btn btn-sm btn-outline-secondary fw-bold px-3 rounded-pill" 
                                                onclick="viewOrderDetails('<?= $displayStr ?>', '<?= addslashes($summary) ?>', '<?= $due ?>', '<?= addslashes($addr) ?>', '<?= number_format((float)$o['price_total'], 2) ?>')">
                                            Details
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger px-2 rounded-pill" onclick="deleteOrderRow(this, <?= (int)$o['order_id'] ?>)">
                                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg>
                                        </button>
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

<div class="modal fade" id="cartItemsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header"><h5 class="fw-bold m-0">Review Your Cart</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle m-0">
                        <tbody id="cartModalBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <h5 class="fw-bold">Total: ₱<span id="cartModalTotal">0.00</span></h5>
                <button id="submitOrderBtn" class="btn btn-warning fw-bold px-4" data-bs-toggle="modal" data-bs-target="#deliveryModal" disabled>Proceed to Checkout</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deliveryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="fw-bold">Delivery Details</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-4">
                <label class="form-label fw-bold">Due Time</label><input type="time" id="deliveryDueTime" class="form-control mb-3">
                <label class="form-label fw-bold">Address</label><input type="text" id="deliveryAddress" class="form-control mb-3">
            </div>
            <div class="modal-footer"><button class="btn btn-success w-100" onclick="confirmDelivery()">Confirm Order</button></div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
let cart = {}; 
const userid = "<?= $_SESSION['user_id'] ?>";

const socket = new WebSocket("ws://localhost:3000?usertype=customer");

socket.onopen = function() {
    socket.send(JSON.stringify({ event: "client_connected", userid: userid }));
};

socket.onmessage = function(event) {
    const data = JSON.parse(event.data);
    if (data.event === "order_confirmed" || data.event === "activity_alert") {
        setTimeout(() => location.reload(), 1000);
    }
};

function viewOrderDetails(id, summary, due, addr, total) {
    document.getElementById('detId').innerText = id;
    document.getElementById('detSummary').innerHTML = summary; 
    document.getElementById('detDue').innerText = due;
    document.getElementById('detAddr').innerText = addr;
    document.getElementById('detTotal').innerText = total;
    new bootstrap.Modal(document.getElementById('orderDetailsModal')).show();
}

function deleteOrderRow(btn, orderId) {
    if(confirm("Are you sure you want to clear this from your view?")) {
        let deleted = JSON.parse(localStorage.getItem('hiddenOrders') || "[]");
        deleted.push(orderId);
        localStorage.setItem('hiddenOrders', JSON.stringify(deleted));
        btn.closest('tr').remove();
    }
}

function checkDeletedOrders() {
    let deleted = JSON.parse(localStorage.getItem('hiddenOrders') || "[]");
    document.querySelectorAll("#ordersTbody tr").forEach(row => {
        let id = parseInt(row.getAttribute('data-order-id'));
        if(deleted.includes(id)) row.remove();
    });
}

document.getElementById('orderSearch').addEventListener('keyup', function() {
    let filter = this.value.toUpperCase();
    let rows = document.querySelector("#ordersTbody").rows;
    for (let i = 0; i < rows.length; i++) {
        let firstCol = rows[i].cells[0].textContent.toUpperCase();
        if(rows[i].cells.length > 1) {
            rows[i].style.display = firstCol.indexOf(filter) > -1 ? "" : "none";
        }
    }
});

function setStatusFilter(status) {
    document.querySelectorAll('.status-filter-btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    let rows = document.querySelectorAll("#ordersTbody tr");
    rows.forEach(row => {
        if(row.cells.length <= 1) return;
        let rowStatus = row.getAttribute('data-status');
        
        if(status === 'ALL') {
            row.style.display = '';
        } else if(status === 'ONGOING') {
            // Ongoing includes both Pending and Processing
            row.style.display = (rowStatus === 'PENDING' || rowStatus === 'PROCESSING') ? '' : 'none';
        } else if(status === 'CLAIMED') {
            // Claimed matches Completed
            row.style.display = (rowStatus === 'COMPLETED') ? '' : 'none';
        }
    });
}

function updateCartUI() {
    let totalQty = 0, totalAmount = 0;
    Object.values(cart).forEach(item => {
        if (item.qty <= 0) return;
        totalQty += item.qty;
        let price = item.discount > 0 ? item.price * (1 - item.discount/100) : item.price;
        totalAmount += price * item.qty;
    });
    const badge = document.getElementById('cartBadgeCount');
    if (totalQty > 0) { badge.innerText = totalQty; badge.classList.remove('d-none'); }
    else { badge.classList.add('d-none'); }
    document.getElementById('cartModalTotal').textContent = totalAmount.toFixed(2);
    document.getElementById('submitOrderBtn').disabled = (totalQty === 0);
}

function renderCartModal() {
    const tbody = document.getElementById('cartModalBody');
    tbody.innerHTML = '';
    const items = Object.values(cart).filter(i => i.qty > 0);
    if(!items.length) { tbody.innerHTML = '<tr><td class="text-center py-5">Cart is empty</td></tr>'; return; }
    items.forEach(item => {
        let price = item.discount > 0 ? item.price * (1 - item.discount/100) : item.price;
        tbody.innerHTML += `<tr><td class="ps-4"><b>${item.name}</b></td><td class="text-center">${item.qty}</td><td class="text-end">₱${(price * item.qty).toFixed(2)}</td></tr>`;
    });
}

function confirmDelivery() {
    const dueDateValue = document.getElementById("deliveryDueTime").value;
    const addressInput = document.getElementById("deliveryAddress").value;
    
    if(!dueDateValue || !addressInput) return alert("Fill all details.");

    const payload = {
        event: "create_order",
        customer_id: userid,
        due_at: dueDateValue, 
        address: addressInput, 
        items: Object.values(cart).map(item => ({
            id: item.id,
            name: item.name,
            qty: item.qty,
            price: item.price,
            discount: item.discount
        }))
    };

    socket.send(JSON.stringify(payload));
    bootstrap.Modal.getInstance(document.getElementById('deliveryModal')).hide();
    cart = {}; 
    updateCartUI(); 
    alert("Order Sent! Processing stock...");
}
</script>
</body>
</html>