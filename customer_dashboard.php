<?php
require_once 'auth_terminal.php';
require_once 'config.php';

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

$staffStmt = $pdo->query("SELECT user_id, username FROM users WHERE usertype = 'staff' ORDER BY username");
$staffList = $staffStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* =========================
        GLOBAL BODY
    ========================= */
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        margin: 0;
        padding: 0;
        min-height: 100vh;
        background-color: white;
        padding-bottom: 200px;
    }

    body::before {
        content: "";
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(180deg, rgb(255, 255, 255) 0%, rgba(255, 255, 255, 0.8) 100%);
        z-index: -1;
    }

    /* =========================
    HEADER STYLES
    ========================= */
    .navbar {
        background-color: #ff6600 !important;
        height: 80px; 
        padding: 0 20px;
        display: flex;
        align-items: center;
        z-index: 1050;
    }

    .navbar .container-fluid {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: nowrap;
        height: 100%;
    }

    .navbar-brand {
        display: flex;
        align-items: center;
        height: 100%; 
        padding: 0;
        margin: 0;
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
        flex-shrink: 0;
    }

    .header-icon-btn {
        position: relative;
        background: none;
        border: none;
        color: #333;
        padding: 8px;
        transition: 0.2s;
    }

    .header-icon-btn:hover { color: #7b1e2b; }

    .cart-badge {
        position: absolute;
        top: 0;
        right: 0;
        background: #ef4444;
        color: white;
        font-size: 0.7rem;
        padding: 2px 6px;
        border-radius: 50%;
        font-weight: bold;
    }

    /* =========================
        SIDEBAR STYLES
    ========================= */
    .sidebar-categories {
        position: sticky;
        top: 100px;
        height: calc(100vh - 120px);
        background: white;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
    }

    .sidebar-scroll-area {
        overflow-y: auto;
        flex-grow: 1;
        padding-right: 5px;
    }

    .sidebar-scroll-area::-webkit-scrollbar { width: 5px; }
    .sidebar-scroll-area::-webkit-scrollbar-thumb { background: #ff6600; border-radius: 10px; }

    .category-list-item {
        padding: 12px 15px;
        margin-bottom: 8px;
        border-radius: 12px;
        cursor: pointer;
        transition: 0.3s;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
        color: #444;
        border: 1px solid transparent;
    }

    .category-list-item:hover {
        background: rgba(255, 102, 0, 0.1);
        color: #ff6600;
    }

    .category-list-item img {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        object-fit: cover;
    }

    .cat-checkbox {
        width: 18px;
        height: 18px;
        accent-color: #ff6600;
        cursor: pointer;
    }

    /* =========================
        PRODUCT DISPLAY CARDS
    ========================= */
    .product-display-card {
        height: 189px;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
        cursor: pointer;
        transition: 0.3s ease;
        background: #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .product-display-card:hover { transform: translateY(-5px); }

    .product-display-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-info-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 12px;
        background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, transparent 100%);
        color: white;
    }

    .stock-ribbon {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #000;
        color: #fff;
        font-size: 0.6rem;
        padding: 4px 8px;
        border-radius: 5px;
        font-weight: bold;
    }

    /* =========================
        ANALYTICS & SLIDER
    ========================= */
    .category-slider-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        padding: 0 45px;
        margin-bottom: 40px;
    }

    .category-container {
        display: flex;
        gap: 20px;
        overflow-x: auto;
        scroll-behavior: smooth;
        scrollbar-width: none;
        padding: 10px 0;
        width: 100%;
    }

    .category-item { flex: 0 0 calc(25% - 16px); min-width: 150px; }

    .category-card {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        cursor: pointer;
        transition: 0.4s ease;
        height: 120px;
        background: #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }

    .category-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 700;
        text-align: center;
    }

    .slider-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: #fff;
        border: none;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
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
        <a class="navbar-brand p-0" href="#">
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

            <a href="orders.php" class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-bold" style="font-size: 0.8rem;">My Orders</a>

            <div class="dropdown">
                <button class="header-icon-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
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

<div class="container-fluid mt-4 px-4">
    <div class="row">
        <div class="col-lg-3 d-none d-lg-block">
            <div class="sidebar-categories">
                <h5 class="fw-bold mb-3">Filters</h5>
                <div class="form-check mb-3 ms-2">
                    <input class="form-check-input" type="checkbox" id="selectAllCats" onchange="toggleAllCategories(this)">
                    <label class="form-check-label fw-bold" for="selectAllCats">Select All</label>
                </div>
                
                <div class="sidebar-scroll-area">
                    <?php foreach ($categories as $c): ?>
                    <label class="category-list-item" for="cat_<?= $c['categ_id'] ?>">
                        <input type="checkbox" class="cat-checkbox" 
                                id="cat_<?= $c['categ_id'] ?>" 
                                value="<?= $c['categ_id'] ?>" 
                                data-name="<?= htmlspecialchars($c['name'], ENT_QUOTES) ?>"
                                onchange="updateMultiFilters()">
                        <img src="<?= htmlspecialchars($c['image_path'] ?? '') ?>" alt="">
                        <?= htmlspecialchars($c['name']) ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-9 col-12">
            
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

            <h4 class="mb-3">Featured Menu</h4>
            <div class="category-slider-wrapper">
                <button class="slider-btn" style="left:0;" onclick="scrollSlider(-1)">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="transform:rotate(180deg)"><path d="M9 18L15 12L9 6"/></svg>
                </button>
                <div class="category-container" id="categorySlider">
                    <?php foreach ($categories as $c): ?>
                    <div class="category-item" onclick="displayProducts(<?= $c['categ_id'] ?>, '<?= htmlspecialchars($c['name'], ENT_QUOTES) ?>')">
                        <div class="category-card">
                            <img src="<?= htmlspecialchars($c['image_path'] ?? '') ?>" style="width:100%; height:100%; object-fit:cover;">
                            <div class="category-overlay"><?= htmlspecialchars($c['name']) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button class="slider-btn" style="right:0;" onclick="scrollSlider(1)">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M9 18L15 12L9 6"/></svg>
                </button>
            </div>

            <div id="productGallerySection" class="mt-2 d-none">
                <h3 id="selectedCategoryName" class="fw-bold mb-4 text-dark">Category</h3>
                <div class="row g-3" id="productGrid"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="productActionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 650px;">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-body p-4">
                <h3 id="modalProductName" class="fw-bold mb-4 text-dark border-bottom pb-2">Product Name</h3>
                <div class="row g-4">
                    <div class="col-md-5 position-relative">
                        <div id="modalDiscountBadge" class="badge bg-danger position-absolute mt-2 ms-2" style="display:none; z-index: 1; font-size: 0.9rem; border-radius: 5px;">-0% OFF</div>
                        <img id="modalProductImg" src="" class="rounded-4 w-100 shadow-sm" style="height: 250px; object-fit: cover; border: 1px solid #eee;">
                    </div>
                    <div class="col-md-7 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small fw-bold">PRICE</span>
                                <div id="modalPriceContainer" class="text-end">
                                    <span id="modalOriginalPrice" class="text-muted small text-decoration-line-through me-2" style="display:none; color: #dc3545 !important;">₱0.00</span>
                                    <span id="modalProductPrice" class="text-danger fw-bold h4 mb-0">₱0.00</span>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted small fw-bold">STOCK</span>
                                <span class="fw-bold text-dark" id="stockCount">0</span>
                            </div>
                            <div class="mb-4">
                                <label class="small fw-bold text-uppercase text-muted mb-2 d-block">Select Quantity</label>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-bold me-2">Quantity</span>
                                    <div class="input-group" style="width: 160px;">
                                        <button class="btn btn-outline-dark border-2 fw-bold" onclick="changeQty(-1)">-</button>
                                        <span id="modalQty" class="form-control text-center border-2 fw-bold bg-light">1</span>
                                        <button class="btn btn-outline-dark border-2 fw-bold" onclick="changeQty(1)">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <button id="modalAddToCartBtn" class="btn btn-success w-100 py-3 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2">
                                     Add to Cart
                                </button>
                            </div>
                            <div class="col-6">
                                <button id="modalBuyNowBtn" class="btn w-100 py-2 rounded-3 text-white fw-bold d-flex flex-column align-items-center justify-content-center" style="background-color: #ff6600; height: 100%; border: none;" onclick="confirmBuyNow()">
                                    <span style="font-size: 0.9rem;">Buy Now</span>
                                    <span id="modalBuyNowTotal" style="font-size: 1.1rem; margin-top:-2px;">₱0.00</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cartItemsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 25px; overflow: hidden;">
            <div class="modal-header bg-white border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="form-check m-0">
                        <input class="form-check-input border-2" type="checkbox" id="selectAllCart" style="width: 20px; height: 20px; cursor: pointer;">
                    </div>
                    <h5 class="fw-bold m-0 text-dark">Review Your Cart</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-light" style="max-height: 60vh; overflow-y: auto;">
                <div id="cartModalBody" class="p-4"></div>
            </div>
            <div class="modal-footer bg-white border-top p-4 flex-column align-items-stretch">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted fw-bold">Total Savings:</span>
                    <span class="text-success fw-bold">-₱<span id="cartModalDiscount">0.00</span></span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold m-0">Final Total:</h4>
                    <h4 class="fw-bold m-0 text-danger">₱<span id="cartModalTotal">0.00</span></h4>
                </div>
                <button id="submitOrderBtn" class="btn btn-warning w-100 py-3 rounded-3 fw-bold shadow-sm" style="background-color: #ff6600; border: none; color: white;" data-bs-toggle="modal" data-bs-target="#deliveryModal" disabled>
                    Proceed to Checkout
                </button>
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

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="wsToast" class="toast align-items-center text-white bg-dark border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <span id="toastMessage"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
<script>
let cart = {}; 
const userid = "<?= $_SESSION['user_id'] ?>";
const productsData = <?= json_encode($itemsByCategory) ?>;
let stockChart;
let lastTopItemIds = []; 

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

// --- WEBSOCKET CONNECTION ---
const socket = new WebSocket("ws://localhost:3000?usertype=customer");

socket.onopen = function() {
    console.log("[WebSocket] Connected");
    socket.send(JSON.stringify({ event: "client_connected", userid: userid }));
};

socket.onmessage = function(event) {
    const data = JSON.parse(event.data);

    if (data.event === "initial_logs") {
        const feed = document.getElementById('activityFeed');
        feed.innerHTML = ''; 
        data.logs.forEach(log => appendToFeed(log.message));
    }

    if (data.event === "live_stats") {
        document.getElementById('liveOrderCount').innerText = data.total_orders;
    }

    if (data.event === "activity_alert") {
        // --- FIX: Always append the message to the feed regardless of prod_id ---
        appendToFeed(data.message);
        
        // --- UPDATED STOCK LOGIC (Only runs if prod_id exists) ---
        if (data.prod_id !== undefined) {
            // 1. Update the local productsData variable so the UI knows the new stock
            Object.keys(productsData).forEach(catId => {
                let product = productsData[catId].find(p => String(p.prod_id) === String(data.prod_id));
                if (product) {
                    product.stock = data.new_stock;
                }
            });

            // 2. Refresh the product grid. 
            updateMultiFilters();

            // 3. Update the Modal if it is currently open for this specific product
            if (currentSelectedProduct && String(currentSelectedProduct.prod_id) === String(data.prod_id)) {
                currentSelectedProduct.stock = data.new_stock; 
                const stockCountEl = document.getElementById('stockCount');
                if (stockCountEl) {
                    stockCountEl.innerText = data.new_stock;
                }
                if (currentQty > data.new_stock) {
                    currentQty = Math.max(1, data.new_stock);
                    document.getElementById('modalQty').innerText = currentQty;
                    calculateModalTotalPrice();
                }
                updateModalButtonStates(currentQty, data.new_stock);
                
                const buyBtn = document.getElementById('modalBuyNowBtn');
                if (data.new_stock <= 0) {
                    buyBtn.classList.add('disabled', 'opacity-50');
                    buyBtn.onclick = null;
                } else {
                    buyBtn.classList.remove('disabled', 'opacity-50');
                    buyBtn.onclick = () => confirmBuyNow();
                }
            }
        }
    }

    if (data.event === "stock_update" && stockChart) {
        const labels = Object.keys(data.chartData).map(id => {
            const cb = document.querySelector(`.cat-checkbox[value="${id}"]`);
            return cb ? cb.getAttribute('data-name') : `Cat ${id}`;
        });
        stockChart.data.labels = labels;
        stockChart.data.datasets[0].data = Object.values(data.chartData);
        stockChart.update('none');
    }

    if (data.event === "db_category_changed" || data.event === "order_confirmed") {
        showAckToast("System Synchronized");
        setTimeout(() => window.location.reload(), 1500);
    }

    if (data.event === "update_best_sellers") {
        lastTopItemIds = data.top_item_ids;
        renderBestSellerBadges(data.top_item_ids);
    }
};

// UPDATED: Removed timestamp logic
function appendToFeed(msg) {
    const feed = document.getElementById('activityFeed');
    if (!feed) return;
    const div = document.createElement('div');
    div.className = 'feed-item';
    div.style.fontSize = "0.85rem";
    div.style.marginBottom = "5px";
    div.innerHTML = `<span>${msg}</span>`;
    feed.prepend(div);
    if (feed.childNodes.length > 5) feed.removeChild(feed.lastChild);
}

function applySoldOutBadgeRealTime(prodId) {
    const cards = document.querySelectorAll('.product-display-card');
    cards.forEach(card => {
        const onclickAttr = card.getAttribute('onclick');
        if (onclickAttr && (onclickAttr.includes(`"prod_id":"${prodId}"`) || onclickAttr.includes(`"prod_id":${prodId}`))) {
            if (!card.querySelector('.stock-ribbon')) {
                const ribbon = document.createElement('div');
                ribbon.className = 'stock-ribbon';
                ribbon.innerText = 'SOLD OUT';
                card.appendChild(ribbon);
                card.removeAttribute('onclick'); 
                card.style.opacity = "0.7";
            }
        }
    });
}

function showAckToast(message) {
    const toastEl = document.getElementById('wsToast');
    if (toastEl) {
        document.getElementById('toastMessage').innerText = message;
        new bootstrap.Toast(toastEl).show();
    }
}

function sendWithAck(payload) {
    const requestId = "req_" + Date.now();
    payload.requestId = requestId;
    socket.send(JSON.stringify(payload));
}

function scrollSlider(dir) {
    const s = document.getElementById('categorySlider');
    s.scrollBy({ left: dir * s.clientWidth, behavior: 'smooth' });
}

function toggleAllCategories(source) {
    const checkboxes = document.querySelectorAll('.cat-checkbox');
    checkboxes.forEach(cb => cb.checked = source.checked);
    updateMultiFilters();
}

function updateMultiFilters() {
    const grid = document.getElementById('productGrid');
    const checkboxes = document.querySelectorAll('.cat-checkbox');
    const gallery = document.getElementById('productGallerySection');
    grid.innerHTML = ''; 
    let hasSelection = false;

    checkboxes.forEach(cb => {
        if (cb.checked) {
            hasSelection = true;
            const catId = cb.value;
            const catName = cb.getAttribute('data-name');
            const items = productsData[catId] || [];
            if (items.length > 0) {
                grid.innerHTML += `<div class="col-12 mt-4 mb-2"><h4 class="fw-bold text-dark border-bottom pb-2">${catName}</h4></div>`;
                items.forEach(item => {
                    const isSoldOut = parseInt(item.stock) <= 0;
                    grid.innerHTML += `
                        <div class="col-6 col-md-6 col-lg-4">
                            <div class="product-display-card position-relative" style="${isSoldOut ? 'opacity: 0.7;' : ''}" onclick="${isSoldOut ? '' : `openActionModal(${JSON.stringify(item).replace(/"/g, '&quot;')})`}">
                                <img src="${item.image_path}">
                                ${parseFloat(item.discount) > 0 ? `<div class="discount-ribbon" style="position:absolute; top:10px; left:10px; background:#ef4444; color:white; padding:2px 8px; border-radius:5px; font-size:0.7rem;">-${item.discount}%</div>` : ''}
                                ${isSoldOut ? '<div class="stock-ribbon">SOLD OUT</div>' : ''}
                                <div class="product-info-overlay">
                                    <h6 class="mb-0 text-truncate">${item.name}</h6>
                                    <span class="fw-bold text-warning">₱${parseFloat(item.price).toFixed(2)}</span>
                                </div>
                            </div>
                        </div>`;
                });
            }
        }
    });
    hasSelection ? gallery.classList.remove('d-none') : gallery.classList.add('d-none');
    if(lastTopItemIds.length > 0) renderBestSellerBadges(lastTopItemIds);
}

function displayProducts(catId, catName) {
    document.querySelectorAll('.cat-checkbox').forEach(cb => cb.checked = false);
    const targetBox = document.getElementById('cat_' + catId);
    if(targetBox) targetBox.checked = true;
    updateMultiFilters();
    document.getElementById('productGallerySection').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

let currentSelectedProduct = null, currentQty = 1;

function updateModalButtonStates(qty, stock) {
    const buttons = document.querySelectorAll('#productActionModal .modal-body button');
    if(buttons[0]) buttons[0].disabled = (qty <= 1);
    if(buttons[2]) buttons[2].disabled = (qty >= stock || stock <= 0);
}

function calculateModalTotalPrice() {
    if (!currentSelectedProduct) return;
    const basePrice = parseFloat(currentSelectedProduct.price);
    const discountPercent = parseFloat(currentSelectedProduct.discount) || 0;
    let unitPrice = basePrice * (1 - (discountPercent / 100));
    const totalPrice = unitPrice * currentQty;
    const totalDisplay = document.getElementById('modalBuyNowTotal');
    if(totalDisplay) totalDisplay.innerText = '₱' + totalPrice.toFixed(2);
}

function openActionModal(product) {
    currentSelectedProduct = JSON.parse(JSON.stringify(product)); 
    currentQty = 1;
    document.getElementById('modalProductName').innerText = product.name;
    document.getElementById('modalProductImg').src = product.image_path;
    document.getElementById('modalQty').innerText = currentQty;
    const basePrice = parseFloat(product.price);
    const discountPercent = parseFloat(product.discount) || 0;
    const priceDisplay = document.getElementById('modalProductPrice');
    const oldPriceDisplay = document.getElementById('modalOriginalPrice');
    const discountBadge = document.getElementById('modalDiscountBadge');

    if (discountPercent > 0) {
        priceDisplay.innerText = '₱' + (basePrice * (1 - (discountPercent / 100))).toFixed(2);
        oldPriceDisplay.innerText = '₱' + basePrice.toFixed(2);
        oldPriceDisplay.style.display = 'inline';
        discountBadge.innerText = `-${discountPercent}% OFF`;
        discountBadge.style.display = 'block';
    } else {
        priceDisplay.innerText = '₱' + basePrice.toFixed(2);
        oldPriceDisplay.style.display = 'none';
        discountBadge.style.display = 'none';
    }

    const stockInt = parseInt(product.stock);
    document.getElementById('stockCount').innerText = stockInt;
    calculateModalTotalPrice();
    updateModalButtonStates(currentQty, stockInt);

    document.getElementById('modalAddToCartBtn').onclick = () => {
        addToCartWS(product.prod_id, product.name, product.price, product.category_name || product.category, product.discount, product.image_path);
        bootstrap.Modal.getInstance(document.getElementById('productActionModal')).hide();
    };

    const buyBtn = document.getElementById('modalBuyNowBtn');
    if (stockInt <= 0) {
        buyBtn.classList.add('disabled', 'opacity-50');
        buyBtn.onclick = null;
    } else {
        buyBtn.classList.remove('disabled', 'opacity-50');
        buyBtn.onclick = () => confirmBuyNow();
    }
    new bootstrap.Modal(document.getElementById('productActionModal')).show();
}

function changeQty(amt) {
    const stock = parseInt(currentSelectedProduct.stock);
    if (stock <= 0) return;
    currentQty = Math.max(1, Math.min(currentQty + amt, stock));
    document.getElementById('modalQty').innerText = currentQty;
    calculateModalTotalPrice();
    updateModalButtonStates(currentQty, stock);
}

function confirmBuyNow() {
    if (!currentSelectedProduct || currentQty <= 0) return;
    
    const basePrice = parseFloat(currentSelectedProduct.price);
    const discountPercent = parseFloat(currentSelectedProduct.discount) || 0;
    const discountedPrice = basePrice * (1 - (discountPercent / 100));
    const totalPrice = discountedPrice * currentQty;

    if (!confirm(`Buy ${currentQty}x ${currentSelectedProduct.name} now for ₱${totalPrice.toFixed(2)}?`)) return;
    
    const productModal = bootstrap.Modal.getInstance(document.getElementById('productActionModal'));
    if (productModal) productModal.hide();
    
    cart = {}; 
    let id = String(currentSelectedProduct.prod_id);
    cart[id] = { 
        id: id, 
        name: currentSelectedProduct.name, 
        price: basePrice, 
        discount: discountPercent, 
        qty: currentQty, 
        category: currentSelectedProduct.category_name || currentSelectedProduct.category, 
        image_path: currentSelectedProduct.image_path 
    };
    
    updateCartUI();
    new bootstrap.Modal(document.getElementById('deliveryModal')).show();
}

function addToCartWS(id, name, price, category, discount, image_path) {
    sendWithAck({ event: "menu_click", item_id: id });
    id = String(id);
    if (!cart[id]) {
        cart[id] = { id, name, price: parseFloat(price), discount: parseFloat(discount), qty: 0, category, image_path };
    }
    cart[id].qty++;
    updateCartUI();
}

function updateCartUI() {
    let totalQty = 0;
    Object.values(cart).forEach(item => totalQty += item.qty);
    const badge = document.getElementById('cartBadgeCount');
    if (badge) {
        badge.innerText = totalQty;
        totalQty > 0 ? badge.classList.remove('d-none') : badge.classList.add('d-none');
    }
    calculateCartTotals();
}

function renderCartModal() {
    const container = document.getElementById('cartModalBody');
    container.innerHTML = '';
    const items = Object.values(cart).filter(i => i.qty > 0);
    if(!items.length) { container.innerHTML = '<div class="text-center py-5 text-muted">Your cart is empty</div>'; return; }

    const grouped = items.reduce((acc, item) => {
        let catLabel = item.category;
        if (!isNaN(catLabel) && productsData[catLabel]) {
            const cb = document.querySelector(`.cat-checkbox[value="${catLabel}"]`);
            if(cb) catLabel = cb.getAttribute('data-name');
        }
        if (!acc[catLabel]) acc[catLabel] = [];
        acc[catLabel].push(item);
        return acc;
    }, {});

    let html = '';
    for (const cat in grouped) {
        html += `<div class="text-muted small fw-bold text-uppercase mt-4 mb-2 ps-2" style="border-left: 4px solid #ff6600; padding-left: 10px; letter-spacing: 1px;">${cat}</div>`;
        grouped[cat].forEach(item => {
            let dp = item.discount > 0 ? item.price * (1 - item.discount/100) : item.price;
            html += `
            <div class="card border-0 rounded-4 shadow-sm mb-2 mx-2">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-auto"><input class="form-check-input item-checkbox border-2" type="checkbox" value="${item.id}" onchange="calculateCartTotals()" checked style="width: 18px; height: 18px; cursor: pointer;"></div>
                        <div class="col-auto"><img src="${item.image_path}" class="rounded-3" style="width: 50px; height: 50px; object-fit: cover;"></div>
                        <div class="col">
                            <div class="fw-bold small text-dark">${item.name}</div>
                            <div class="text-danger fw-bold small">₱${dp.toFixed(2)}</div>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex align-items-center gap-2 bg-light rounded-pill px-2 border">
                                <button class="btn btn-sm p-0 border-0 fw-bold" onclick="changeCartQty('${item.id}', -1)" style="width: 20px;">-</button>
                                <span class="small fw-bold" style="min-width: 20px; text-align: center;">${item.qty}</span>
                                <button class="btn btn-sm p-0 border-0 fw-bold" onclick="changeCartQty('${item.id}', 1)" style="width: 20px;">+</button>
                            </div>
                        </div>
                        <div class="col-auto text-end" style="min-width: 85px;"><span class="fw-bold text-dark">₱${(dp * item.qty).toFixed(2)}</span></div>
                    </div>
                </div>
            </div>`;
        });
    }
    container.innerHTML = html;
    document.getElementById('selectAllCart').onclick = (e) => {
        document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = e.target.checked);
        calculateCartTotals();
    };
    calculateCartTotals();
}

function changeCartQty(id, amt) {
    if (cart[id]) {
        if (amt > 0) sendWithAck({ event: "menu_click", item_id: id });
        cart[id].qty += amt;
        if (cart[id].qty <= 0) delete cart[id];
        updateCartUI();
        renderCartModal();
    }
}

function calculateCartTotals() {
    let total = 0, savings = 0, checkedAny = false;
    document.querySelectorAll('.item-checkbox').forEach(cb => {
        if (cb.checked && cart[cb.value]) {
            checkedAny = true;
            const item = cart[cb.value];
            let dp = item.discount > 0 ? item.price * (1 - item.discount/100) : item.price;
            total += dp * item.qty;
            savings += (item.price - dp) * item.qty;
        }
    });
    const totalEl = document.getElementById('cartModalTotal');
    const discountEl = document.getElementById('cartModalDiscount');
    const btnEl = document.getElementById('submitOrderBtn');
    if(totalEl) totalEl.textContent = total.toFixed(2);
    if(discountEl) discountEl.textContent = savings.toFixed(2);
    if(btnEl) btnEl.disabled = !checkedAny;
}

function confirmDelivery() {
    const duetime = document.getElementById("deliveryDueTime").value;
    const address = document.getElementById("deliveryAddress").value;
    if(!duetime || !address) return alert("Fill all details.");

    const checkedItems = [];
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');

    if (checkboxes.length > 0) {
        checkboxes.forEach(cb => {
            if(cart[cb.value]) checkedItems.push(cart[cb.value]);
        });
    } else {
        Object.values(cart).forEach(item => checkedItems.push(item));
    }

    if (checkedItems.length === 0) return alert("No items selected.");

    socket.send(JSON.stringify({ 
        event: "create_order", 
        customer_id: userid, 
        due_time: duetime, 
        delivery_address: address, 
        items: checkedItems 
    }));

    bootstrap.Modal.getInstance(document.getElementById('deliveryModal')).hide();
    
    checkedItems.forEach(item => delete cart[item.id]);
    updateCartUI(); 
    alert("Order Sent Successfully!");
}

function renderBestSellerBadges(topIds) {
    document.querySelectorAll('.best-seller-badge').forEach(el => el.remove());
    topIds.forEach((id, index) => {
        const card = document.querySelector(`[onclick*="openActionModal"][onclick*="${id}"]`);
        if (card) {
            const badge = document.createElement('div');
            badge.className = 'best-seller-badge';
            badge.innerHTML = (index === 0) ? '🏆 #1' : '🔥 Top';
            badge.style.position = 'absolute';
            badge.style.top = '10px';
            badge.style.right = '10px';
            badge.style.backgroundColor = (index === 0) ? '#ffc107' : '#fd7e14';
            badge.style.color = '#000';
            badge.style.padding = '3px 8px';
            badge.style.borderRadius = '5px';
            badge.style.fontSize = '0.65rem';
            badge.style.fontWeight = 'bold';
            badge.style.zIndex = '10';
            card.appendChild(badge);
        }
    });
}

window.addEventListener('DOMContentLoaded', initStockChart);
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>