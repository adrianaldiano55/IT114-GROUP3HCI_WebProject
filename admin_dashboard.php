<?php
require_once 'auth_terminal.php';
require_once 'config.php';


$productStmt = $pdo->query("SELECT prod_id, name, price, stock, discount, image_path, category FROM products");
$products = $productStmt->fetchAll(PDO::FETCH_ASSOC); 

$categoryStmt = $pdo->query("SELECT categ_id, name, image_path FROM categories");
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

$status = $_GET['status'] ?? 'Product';
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
        <div class="col-lg-9 col-12">
            <div class="row g-3 mb-4">

                <div class="col-md-3">
<!-- ANALYTICS (PENDING CHANGES) -->
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

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
            <div>
                <h3 class="fw-bold mb-0">Product and Category Management</h3>
                <p class="text-muted small">Manage product items and categories</p>
            </div>
        </div>
        <?php if ($status === 'Product'): ?>
            <button class="btn btn-sm btn-outline-success fw-bold px-3 rounded-pill mb-2" onclick="addProduct()">
                Add New Product
            </button>
        <?php elseif ($status === 'Category'): ?>
            <button class="btn btn-sm btn-outline-success fw-bold px-3 rounded-pill mb-2" onclick="addCategory()">
                Add New Category
            </button>
        <?php endif; ?>
        <div class="mb-4">
            <div class="btn-group" role="group">
                <button class="btn btn-outline-warning status-filter-btn" data-status="Product" onclick="setStatusFilter('Product')">Products</button>
                <button class="btn btn-outline-warning status-filter-btn" data-status="Category" onclick="setStatusFilter('Category')">Categories</button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
<!-- PRODUCTS TABLE HEADERS -->
                <?php if ($status === 'Product'): ?>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Discount</th>
                        <th>Image</th>
                        <th>Category</th>
                        <th>Action</th>
                    </tr>
<!-- CATEGORY TABLE HEADERS -->
                <?php elseif ($status === 'Category'): ?>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                <?php endif; ?>
                </thead>
                <tbody id="productsTbody">
<!-- PRODUCTS TABLE ROWS -->
                <?php if ($status === 'Product'): ?>
                    <?php if (empty($products)): ?>
                        <tr><td colspan="8" class="text-center py-4 text-muted">No products found yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td class="fw-bold"><?= (int)$p['prod_id'] ?></td>
                                <td class="fw-bold"><?= $p['name'] ?></td>
                                <td class="fw-small"><?= number_format((float)$p['price'], 2) ?></td>
                                <td class="fw-small"><?= (int)$p['stock'] ?></td>
                                <td class="fw-small"><?= (float)$p['discount'] ?>%</td>
                                <td class="fw-small"><img src="<?= htmlspecialchars($p['image_path']) ?>" alt="Product Image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;"></td>
                                <td class="fw-small"><?= htmlspecialchars($p['category'] ?? 'None') ?></td>
                                <td>    
                                    <button class="btn btn-sm btn-outline-secondary fw-bold px-3 rounded-pill" 
                                        onclick="updateProduct(
                                            <?= (int)$p['prod_id'] ?>,
                                            '<?= htmlspecialchars($p['name'] ?? '', ENT_QUOTES) ?>',
                                            '<?= htmlspecialchars((string)($p['price'] ?? ''), ENT_QUOTES) ?>',
                                            '<?= htmlspecialchars((string)($p['stock'] ?? ''), ENT_QUOTES) ?>',
                                            '<?= (float)($p['discount'] ?? 0) ?>',
                                            '<?= htmlspecialchars($p['image_path'] ?? '', ENT_QUOTES) ?>',
                                            '<?= htmlspecialchars($p['category'] ?? '', ENT_QUOTES) ?>'
                                        )">
                                        Update
                                    </button>                                    
                                    <button class="btn btn-sm btn-outline-primary px-3 rounded-pill"
                                        onclick="deleteProduct(
                                            <?= (int)$p['prod_id'] ?>,
                                        )">
                                        Delete
                                    </button>
                                </td>
                            </tr>   
                        <?php endforeach; ?>
                    <?php endif; ?>
<!-- CATEGORY TABLE ROWS -->
                <?php elseif ($status === 'Category'): ?>
                    <?php if (empty($categories)): ?>
                        <tr><td colspan="8" class="text-center py-4 text-muted">No categories found yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($categories as $c): ?>
                            <tr>
                                <td class="fw-bold"><?= (int)$c['categ_id'] ?></td>
                                <td class="fw-bold"><?= $c['name'] ?></td>
                                <td class="fw-small"><img src="<?= htmlspecialchars($c['image_path']) ?>" alt="Category Image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;"></td>
                                <td>    
                                    <button class="btn btn-sm btn-outline-secondary fw-bold px-3 rounded-pill" 
                                        onclick="updateCategory(
                                            <?= (int)$c['categ_id'] ?>,
                                            '<?= htmlspecialchars($c['name'] ?? '', ENT_QUOTES) ?>',
                                            '<?= htmlspecialchars($c['image_path'] ?? '', ENT_QUOTES) ?>'
                                        )">
                                        Update
                                    </button>                                    
                                    <button class="btn btn-sm btn-outline-primary px-3 rounded-pill"
                                        onclick="deleteCategory(
                                            <?= (int)$c['categ_id'] ?>
                                        )">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endif; ?>

                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="updateProductModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="fw-bold">
                    Update Product 
                </h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <input type="hidden" id="updProdId">
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="text-muted small d-block mb-1">
                        Product Name
                    </label>
                    <input type="text" class="form-control rounded-pill" id="updName" value="">
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="text-muted small d-block mb-1">
                            Price
                        </label>
                        <input type="number" step="0.01" class="form-control rounded-pill" id="updPrice" value="">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="text-muted small d-block mb-1">
                            Stock
                        </label>
                        <input type="number" class="form-control rounded-pill" id="updStock" value="">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block mb-1">Discount (%)</label>
                <input type="number" step="0.01" class="form-control rounded-pill" id="updDiscount" value="">
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block mb-1">
                        Product Image
                    </label>
                    <input type="file" class="form-control" id="updProductImage" accept="images/*">
                </div>
                <div class="mb-3">
                    <img id="updProductImagePreview" src="" style=" width: 90px; height: 90px; object-fit: cover; border-radius: 10px; border: 1px solid #ddd;">
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block mb-1">Category</label>
                    <select class="form-select rounded-pill" id="updCategory">
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['categ_id'] ?>">
                                <?= htmlspecialchars($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="btn btn-dark rounded-pill px-4" onclick="submitupdateProduct()">
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="updateCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="fw-bold">
                    Update Category 
                </h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="updCategId">
                <div class="mb-3">
                    <label class="text-muted small d-block mb-1">
                        Category Name
                    </label>
                    <input type="text" class="form-control rounded-pill" id="updCategName" value="">
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block mb-1">
                        Product Image
                    </label>
                    <input type="file" class="form-control" id="updCategImage" accept="images/*">
                </div>
                <div class="mb-3">
                    <img id="updCategImagePreview" src="" style=" width: 90px; height: 90px; object-fit: cover; border-radius: 10px; border: 1px solid #ddd;">
                </div>
                <button class="btn btn-dark rounded-pill px-4" onclick="submitupdateCategory()">
                    Save Changes
                </button>
            </div>
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
        "activity_alert"
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
}

// =========================
// ADD PRODUCT (CHANGED)
// =========================
function addProduct() {
    document.getElementById('updProdId').value = '';
    document.getElementById('updName').value = '';
    document.getElementById('updPrice').value = '';
    document.getElementById('updStock').value = '';
    document.getElementById('updDiscount').value = '';
    document.getElementById('updCategory').value = '';
    document.getElementById('updProductImage').value = '';
    document.getElementById('updProductImagePreview').src = '';
    new bootstrap.Modal(document.getElementById('updateProductModal')).show();
}

// =========================
// ADD CATEGORY (CHANGED)
// =========================
function addCategory() {
    document.getElementById('updCategId').value = '';
    document.getElementById('updCategName').value = '';
    document.getElementById('updCategImage').value = '';
    document.getElementById('updCategImagePreview').src = '';
    new bootstrap.Modal(document.getElementById('updateCategoryModal')).show();
}

// =========================
// UPDATE PRODUCT (CHANGED)
// =========================
function updateProduct(prod_id, name, price, stock, discount, image_path, category) {
    document.getElementById('updProdId').value = prod_id;
    document.getElementById('updName').value = name;
    document.getElementById('updPrice').value = price;
    document.getElementById('updStock').value = stock;
    document.getElementById('updDiscount').value = discount;
    document.getElementById('updProductImagePreview').src = image_path;
    document.getElementById('updCategory').value = category;
    new bootstrap.Modal(document.getElementById('updateProductModal')).show();
}

// =========================
// UPDATE CATEGORY (CHANGED)
// =========================
function updateCategory(categ_id, name, image_path) {
    document.getElementById('updCategId').value = categ_id;
    document.getElementById('updCategName').value = name;
    document.getElementById('updCategImagePreview').src = image_path;
    new bootstrap.Modal(document.getElementById('updateCategoryModal')).show();
}


// =========================
// DELETE PRODUCT (WEBSOCKET)
// =========================
function deleteProduct(prodId) {
    if (!socket || socket.readyState !== WebSocket.OPEN) {
        console.error("WebSocket not connected");
        return;
    }

    if (!confirm("Are you sure you want to delete this product?")) return;

    socket.send(JSON.stringify({
        type: "delete_product",
        payload: { productId: prodId }
    }));
}

// =========================
// DELETE CATEGORY (WEBSOCKET)
// =========================
function deleteCategory(categId) {
    if (!socket || socket.readyState !== WebSocket.OPEN) {
        console.error("WebSocket not connected");
        return;
    }

    if (!confirm("Are you sure you want to delete this category?")) return;

    socket.send(JSON.stringify({
        type: "delete_category",
        payload: { categoryId: categId }
    }));
}

// =========================
// SUBMIT UPDATE PRODUCT (WEBSOCKET)
// =========================
function submitupdateProduct() {
    const prod_id = document.getElementById("updProdId").value;
    const name = document.getElementById("updName").value;
    const price = document.getElementById("updPrice").value;
    const stock = document.getElementById("updStock").value;
    const discount = document.getElementById("updDiscount").value;
    const imageInput = document.getElementById("updProductImage");
// Seperates image path to just the image file then adds in images/ to match databse pathing
    let image = "";
    if (imageInput.files.length > 0) {
        image = "images/" + imageInput.files[0].name;
    }
    const category = document.getElementById("updCategory").value;

    if (!name || !price || !stock || !discount || !image || !category) {
        alert("Please fill all fields.");
        return;
    }
    if (!prod_id) {
        createProductWS(name, price, stock, discount, category, image);
    } else {
        updateProductWS(prod_id, name, price, stock, discount, category, image);
    }
    bootstrap.Modal.getInstance(document.getElementById('updateProductModal')).hide();
}

// =========================
// SUBMIT UPDATE CATEGORY (WEBSOCKET)
// =========================
function submitupdateCategory() {
    const categ_id = document.getElementById("updCategId").value;
    const name = document.getElementById("updCategName").value;
    const imageInput = document.getElementById("updCategImage");
// Seperates image path to just the image file then adds in images/ to match databse pathing
    let image = "";
    if (imageInput.files.length > 0) {
        image = "images/" + imageInput.files[0].name;
    }

    if (!categ_id || !name || !image) {
        alert("Please fill all fields.");
        return;
    }
    if (!categ_id) {
        createCategoryWS(name, image);
    } else {
        updateCategoryWS(categ_id, name, image);
    }
    bootstrap.Modal.getInstance(document.getElementById('updateCategoryModal')).hide();
}


// =========================
// SEND UPDATE VIA WS
// =========================
function updateProductWS(prod_id, name, price, stock, discount, image, category) {
    if (!socket || socket.readyState !== WebSocket.OPEN) {
        console.error("WebSocket not connected");
        return;
    }

    socket.send(JSON.stringify({
        type: "update_product",
        payload: {
            prod_id: prod_id,
            name: name,
            price: price,
            stock: stock,
            discount: discount,
            image: image,
            category: category
        }
    }));
}

function updateCategoryWS(categ_id, name, image) {
    if (!socket || socket.readyState !== WebSocket.OPEN) {
        console.error("WebSocket not connected");
        return;
    }

    socket.send(JSON.stringify({
        type: "update_category",
        payload: {
            categ_id: categ_id,
            name: name,
            image: image
        }
    }));
}

// =========================
// SEND CREATE VIA WS
// =========================
function createProductWS(name, price, stock, discount, category, image) {
    if (!socket || socket.readyState !== WebSocket.OPEN) {
        console.error("WebSocket not connected");
        return;
    }

    socket.send(JSON.stringify({
        type: "create_product",
        payload: {
            name: name,
            price: price,
            stock: stock,
            discount: discount,
            image: image,
            category: category
        }
    }));
}

function createCategoryWS(name, image) {
    if (!socket || socket.readyState !== WebSocket.OPEN) {
        console.error("WebSocket not connected");
        return;
    }

    socket.send(JSON.stringify({
        type: "create_category",
        payload: {
            name: name,
            image: image
        }
    }));
}
</script>
</body>
</html>