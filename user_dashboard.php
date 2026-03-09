<?php
require_once 'auth_terminal.php';

if ($_SESSION['usertype'] !== 'customer') {
    header('Location: index.php');
    exit;
}
require_once 'config.php';

// Fetch categories
//$catStmt = $pdo->query("
  //  SELECT id, name
 //   FROM product_categories
  //  ORDER BY name
//");
//$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all active menu items (with category)
$itemStmt = $pdo->query(
// Updated query to include stock and discount 
    "
   SELECT prod_id, name, price, stock, discount, image_path, category
   FROM products 
");

 $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);


?>
<!DOCTYPE html>
<html>
<head>
    <title>Customer Kiosk</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f3f4f6;
            font-size: 0.95rem;
            padding-bottom: 160px; /* space for sticky footer cart */
        }

        .navbar {
            background-color: #ffffff;
            border-bottom: 1px solid #e5e7eb;
        }

        .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.05em;
        }

        .category-list {
            position: sticky;
            top: 70px;
            max-height: calc(100vh - 90px);
            overflow-y: auto;
        }

        .category-btn {
            text-align: left;
        }

        .product-grid {
            max-height: calc(100vh - 90px);
            overflow-y: auto;
            padding-bottom: 0.5rem;
        }

        .product-card {
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(15,23,42,0.08);
            border: 1px solid #e5e7eb;
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .product-img {
            height: 140px;
            object-fit: cover;
            width: 100%;
        }

        .product-body {
            padding: 0.6rem 0.75rem 0.75rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .product-name {
            font-weight: 600;
            margin-bottom: 0.1rem;
        }

        .product-price {
            font-weight: 700;
            color: #b91c1c;
        }

        .category-ribbon {
        position: absolute;
        top: 0.45rem;
        right: -0.3rem;
        background: #111827;
        color: #f9fafb;
        padding: 0.2rem 0.6rem;
        border-radius: 999px 0 0 999px;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        }
/* CSS for Discount Ribbon */
        .discount-ribbon {
        position: absolute;
        top: 1.8rem;
        right: -0.3rem;
        background: #dc2626;
        color: #f9fafb;
        padding: 0.2rem 0.6rem;
        border-radius: 999px 0 0 999px;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
        } 
/* CSS for Stock Ribbon */
        .stock-ribbon {
        position: absolute;
        top: 1.8rem;
        right: -0.3rem;
        background: #f08a03ff;
        color: #f9fafb;
        padding: 0.2rem 0.6rem;
        border-radius: 999px 0 0 999px;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
        }     

        /* Sticky footer cart */
        .cart-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #1f2937;
            color: #f9fafb;
            padding: 0.5rem 1rem;
            box-shadow: 0 -4px 15px rgba(15,23,42,0.6);
            z-index: 1050;
        }

        .cart-summary {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .cart-items-inline {
            display: flex;
            gap: 0.5rem;
            overflow-x: auto;
            max-width: 400px;
        }

        .cart-pill {
            background: #374151;
            border-radius: 999px;
            padding: 0.2rem 0.55rem;
            font-size: 0.8rem;
            white-space: nowrap;
        }

        .cart-pill strong {
            color: #fbbf24;
        }

        .cart-total {
            font-size: 1.1rem;
            font-weight: 700;
        }

        @media (max-width: 768px) {
            .cart-summary {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        /* Cart modal */
        .modal-cart-table thead th {
            position: sticky;
            top: 0;
            background-color: #f9fafb;
            z-index: 5;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light mb-2">
    <div class="container-fluid">
        <span class="navbar-brand">SELF-SERVICE ORDERING</span>
        <div class="ms-auto">
            <a href="logout.php" class="btn btn-outline-dark btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row g-3">
        <!-- Categories 
        <div class="col-md-2">
            <div class="card">
                <div class="card-header py-2">
                    <strong>Categories</strong>
                </div>
                <div class="card-body p-2 category-list">
                    <div class="d-grid gap-1">
                        <button class="btn btn-sm btn-primary category-btn"
                                data-category="ALL"
                                onclick="filterCategory('ALL')">
                            All
                        </button>
                        <?php //foreach ($categories as $c): ?>
                            <button class="btn btn-sm btn-outline-secondary category-btn"
                                    data-category="<?//= (int)$c['id'] ?>"
                                    onclick="filterCategory('<?//= (int)$c['id'] ?>')">
                                <?//= htmlspecialchars($c['name'] ?? '') ?>
                            </button>
                        <?php //endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
-->
        <!-- Products -->
        <div class="col-md-10">
            <div class="product-grid">
                <div class="row g-3" id="productsContainer">
                    <?php foreach ($items as $p): ?>
                        <div class="col-sm-6 col-md-4 col-lg-3 product-card-wrapper"
                                data-category-id="<?= (int)($p['category'] ?? 0) ?>"
                                data-stock="<?= (int)$p['stock'] ?>"
                                id="product_<?= (int)$p['prod_id'] ?>">
<!--                           <div class="product-card">
                                <? //php if (!empty($p['category_name']) && (int)$p['stock'] > 0): ?>
                                <div class="category-ribbon">
                                    <?//= htmlspecialchars($p['category_name']) ?>
                                </div>
                                <?//php endif; ?> -->
<!-- DISCOUNT RIBBON-->
                                <?php if (!empty($p['discount']) && (float)$p['discount'] > 0 && (int)$p['stock'] >= 0): ?>
                                <div class="discount-ribbon">
                                    <?= number_format((float)$p['discount'],0 ) ?>% OFF
                                </div>
                                <?php endif; ?>
<!-- STOCK RIBBON-->
                                <?php if ((int)$p['stock'] <= 0): ?>
                                <div class="stock-ribbon">
                                    OUT OF STOCK
                                </div>
                                <?php endif; ?>


                                <?php if (!empty($p['image_path']) && file_exists($p['image_path'])): ?>
                                    <img src="<?= htmlspecialchars($p['image_path']) ?>"
                                         class="product-img"
                                         alt="<?= htmlspecialchars($p['name'] ?? '') ?>">
                                <?php else: ?>
                                    <div class="product-img d-flex align-items-center justify-content-center bg-light text-muted">
                                        No Image
                                    </div>
                                <?php endif; ?>
                                <div class="product-body">
                                    <div class="product-name"><?= htmlspecialchars($p['name'] ?? '') ?></div>
                                    <div class="product-price mb-2">
                                        Regular ₱<?= number_format((float)$p['price'],2) ?>
                                    </div>
<!-- Added visible discounted price for items with discount-->
                                    <div class="product-price mb-2">
                                    <?php if(!empty($p['discount']) && (float)$p['discount'] > 0 && (int)$p['stock'] > 0): ?>  
                                        Discounted ₱<?= number_format((float)$p['price'] - $p['price'] * (float)$p['discount'] / 100,2) ?>
                                    <?php endif; ?>
                                    </div>
                                    <div class="mt-auto d-grid">
                                        <?php if ((int)$p['stock'] > 0): ?>
                                        <button class="btn btn-sm btn-success"
                                                onclick="addToCart(
                                                    <?= (int)$p['id'] ?>,
                                                    '<?= htmlspecialchars($p['name'] ?? '', ENT_QUOTES) ?>',
                                                    <?= (float)$p['price'] ?>,
// Added stock and discount parameters in addToCart function
                                                    <?= (float)$p['stock'] ?>,
                                                    <?=  !empty($p['discount']) ? (float)$p['discount'] : 0 ?>
                                                )">
                                            Add to Order
                                        </button>
<!-- Added condition for Out of Stock button-->
                                        <?php endif; ?>
                                        <?php if ((int)$p['stock'] <= 0): ?>
                                        <button class="btn btn-sm btn-secondary" disabled>
                                            Out of Stock
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (!$items): ?>
                        <div class="col-12 text-center text-muted mt-3">
                            No products available.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sticky Cart Footer -->
<div class="cart-footer">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="cart-summary">
            <div>
                <div class="small text-gray-300">Your Order</div>
                <div>
                    <span id="cartItemCount">0</span> item(s)
                </div>
            </div>
            <div class="cart-items-inline" id="cartInlineList">
                <!-- Pills of items, filled by JS -->
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="cart-total me-3">
                Total: ₱<span id="cartTotalAmount">0.00</span>
            </div>
            <button type="button"
                    class="btn btn-outline-light btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#cartItemsModal"
                    onclick="renderCartModal()">
                View Items
            </button>
            <button id="submitOrderBtn"
                    class="btn btn-warning btn-sm text-dark fw-bold"
                    onclick="submitOrder()"
                    disabled>
                Submit Order
            </button>
        </div>
    </div>
    <div class="mt-1 small" id="orderResult"></div>
</div>

<!-- Cart Items Modal -->
<div class="modal fade" id="cartItemsModal" tabindex="-1" aria-labelledby="cartItemsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header py-2">
        <h5 class="modal-title" id="cartItemsModalLabel">Your Order Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-2">
        <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
            <table class="table table-sm align-middle modal-cart-table">
                <thead class="table-light">
                    <tr>
                        <th>Item</th>
                        <th class="text-center" style="width:80px;">Qty</th>
                        <th class="text-end" style="width:100px;">Price</th>
                        <th class="text-end" style="width:100px;">Subtotal</th>
                        <th style="width:60px;" class="text-end"></th>
                    </tr>
                </thead>
                <tbody id="cartModalBody">
                    <!-- filled by JS -->
                </tbody>
            </table>
        </div>
      </div>
      <div class="modal-footer py-2">
        <div class="me-auto">
            <strong>Total: ₱<span id="cartModalTotal">0.00</span></strong>
        </div>
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Added Temporary Stock for Customer Add items
let tempStock = {}; 
let currentCategoryFilter = 'ALL';
let cart = {}; // id -> {id, name, price, qty, category}
updateCartUI();

function filterCategory(catId) {
    currentCategoryFilter = catId;
    document.querySelectorAll('.category-btn').forEach(btn => {
        if (btn.dataset.category === catId) {
            btn.classList.remove('btn-outline-secondary');
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-primary');
        } else {
            btn.classList.remove('btn-primary');
            if (btn.dataset.category === 'ALL') {
                btn.classList.add('btn-outline-secondary');
            } else {
                btn.classList.add('btn-outline-secondary');
            }
        }
    });

    document.querySelectorAll('#productsContainer .product-card-wrapper').forEach(card => {
        const itemCat = card.dataset.categoryId || '0';
        if (catId === 'ALL' || catId === itemCat) {
            card.classList.remove('d-none');
        } else {
            card.classList.add('d-none');
        }
    });
}
// Added update ProductUI function when Temporary Stock changes
function updateProductUI(id) {
    const card = document.getElementById(`product_${id}`);
    if (!card) return;

    const addBtn = card.querySelector('.product-body .btn');
    const stockRibbon = card.querySelector('.stock-ribbon');

    const newStock = tempStock[id];

    // Update display
    card.dataset.stock = newStock;

    if (addBtn) {
        if (newStock <= 0) {
            addBtn.disabled = true;
            addBtn.classList.remove("btn-success");
            addBtn.classList.add("btn-secondary");
            addBtn.textContent = "Out of Stock";
        } else {
            addBtn.disabled = false;
            addBtn.classList.add("btn-success");
            addBtn.classList.remove("btn-secondary");
            addBtn.textContent = "Add to Order";
        }
    }

    if (stockRibbon) {
        stockRibbon.style.display = newStock <= 0 ? "block" : "none";
    } else if (newStock <= 0) {
        const ribbon = document.createElement('div');
        ribbon.className = "stock-ribbon";
        ribbon.textContent = "OUT OF STOCK";
        card.appendChild(ribbon);
    }
}

// Added variables stock and discount in addToCart function
function addToCart(id, name, price, category, stock, discount) {
    id = String(id);

// Stops Add to Cart when Stock is 0
    if (tempStock[id] <= 0) {
        return; // Do nothing
    }

// Reduces Temporary Stock
    tempStock[id]--;


// Added Discount parameter in Cart
    if (!cart[id]) {
        cart[id] = {id: id, name: name, price: parseFloat(price), discount: parseFloat(discount), qty: 0, category: category, stock: parseFloat(stock)};
    }
    cart[id].qty++;
// Added productUI update from Temporary Stock
    updateProductUI(id);
    updateCartUI();
}

function updateCartUI() {
    let totalQty = 0;
    let totalAmount = 0;
    const inlineList = document.getElementById('cartInlineList');
    inlineList.innerHTML = '';

    Object.values(cart).forEach(item => {
        if (item.qty <= 0) return;
        totalQty += item.qty;
// Added UpdateProdUI function on each cart Item
        updateProductUI(item.id);

// Discount Spot for total calculation
        let unitPrice = item.price;
        if (item.discount > 0) {
            const discountAmount = item.price * (item.discount / 100);
            unitPrice = item.price - discountAmount;

        } 
        totalAmount += unitPrice * item.qty;

        const pill = document.createElement('div');
        pill.className = 'cart-pill';
        pill.innerHTML = `<strong>${item.qty}×</strong> ${item.name}`;
        inlineList.appendChild(pill);
    });

    document.getElementById('cartItemCount').textContent = totalQty;
    document.getElementById('cartTotalAmount').textContent = totalAmount.toFixed(2);
    document.getElementById('submitOrderBtn').disabled = (totalQty === 0);

    // also reflect in modal total if open
    document.getElementById('cartModalTotal').textContent = totalAmount.toFixed(2);

// Update ribbons and button depending on stock
    Object.values(cart).forEach(i => {
    const card = document.getElementById(`product_${i.id}`);
    if (!card) return;

// Update stock calculation based on Temporary Stock
    const id = i.id;
    const newStock = tempStock[id];
    card.dataset.stock = newStock;

    // Update button
    const btn = card.querySelector('button.btn-success');
    if (btn) {
        if (newStock <= 0) {
            btn.classList.remove('btn-success');
            btn.classList.add('btn-secondary');
            btn.disabled = true;
            btn.textContent = "Out of Stock";
        }
    }

    // Update ribbons
    const categoryRibbon = card.querySelector('.category-ribbon');
    const discountRibbon = card.querySelector('.discount-ribbon');
    const stockRibbon    = card.querySelector('.stock-ribbon');

    // Hide category ribbon when stock = 0
    if (categoryRibbon) {
        categoryRibbon.style.display = newStock > 0 ? 'block' : 'none';
    }

    // Hide discount ribbon if stock = 0?
    if (discountRibbon) {
        discountRibbon.style.display = newStock > 0 ? 'block' : 'none';
    }

    // Show stock ribbon if out of stock
    if (stockRibbon) {
        stockRibbon.style.display = newStock <= 0 ? 'block' : 'none';
    } else if (newStock <= 0) {
        // Create ribbon dynamically
        const ribbon = document.createElement('div');
        ribbon.className = 'stock-ribbon';
        ribbon.textContent = "OUT OF STOCK";
        card.appendChild(ribbon);
    }
});
}

// Renders items inside the modal
function renderCartModal() {
    const tbody = document.getElementById('cartModalBody');
    tbody.innerHTML = '';

    let totalAmount = 0;

    const items = Object.values(cart).filter(i => i.qty > 0);
    if (!items.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-muted py-3">
                    Your cart is empty.
                </td>
            </tr>`;
    } else {
        items.forEach(item => {
// Discount Spot for calculation
            let unitPrice = item.price;
            if (item.discount > 0) {
                const discountAmount = item.price * (item.discount / 100);
                unitPrice = item.price - discountAmount;

            } 
            const sub = unitPrice * item.qty;
            totalAmount += sub;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${escapeHtml(item.name)}</td>
                <td class="text-center">
                    <input type="number"
                           min="1"
                           value="${item.qty}"
                           class="form-control form-control-sm text-center"
                           style="width:70px;"
                           onchange="changeCartQty('${item.id}', this.value)">
                </td>
// Adjusted Menu_item price and Total to fit discount items (By: Adrian Aldiano)
                <td class="text-end">₱${unitPrice.toFixed(2)}</td>
                <td class="text-end">₱${sub.toFixed(2)}</td>
                <td class="text-end">
                    <button type="button"
                            class="btn btn-sm btn-outline-danger"
                            onclick="removeFromCart('${item.id}')">
                        &times;
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    document.getElementById('cartModalTotal').textContent = totalAmount.toFixed(2);
}

function changeCartQty(id, value) {
    id = String(id);
    let qty = parseInt(value, 10);
    if (isNaN(qty) || qty <= 0) {
        delete cart[id];
    } else {
        if (!cart[id]) return;
        cart[id].qty = qty;
    }
    updateCartUI();
    renderCartModal();
}

function removeFromCart(id) {
    id = String(id);
    if (cart[id]) {
        tempStock[id] += cart[id].qty;
// Added Temporary Stock back upon removing from cart
        delete cart[id];
    }
// Added Refresh to ProductUI
    updateProductUI(id);
    updateCartUI();
    renderCartModal();
}

// Submit order to backend
function submitOrder() {
    const items = Object.values(cart).filter(i => i.qty > 0);
    if (!items.length) return;
// Added discount into submitOrder function payload)
    const payload = items.map(i => ({
        id: i.id,
        qty: i.qty,
        price: i.price,
        discount: i.discount,
    }));

    const fd = new FormData();
    fd.append('items', JSON.stringify(payload));

    fetch('api_create_order.php', {
        method: 'POST',
        body: fd
    })
        .then(r => r.json())
        .then(res => {
            if (!res.success) {
                alert(res.error || 'Error creating order.');
                return;
            }

            const displayNum = res.order_number ?? res.order_id;
            const resultDiv = document.getElementById('orderResult');
            resultDiv.innerHTML =
                `Your Order Number: <span class="text-warning fw-bold h5 mb-0">${displayNum}</span> ` +
                `<span class="small text-white-50 ms-2">Please present this to the teller.</span><button onclick="window.location.replace(window.location.href);" class="btn btn-warning ms-3">New Order</button>`;

            // reset cart
            cart = {};
            updateCartUI();
        })
        .catch(() => {
            alert('Network error submitting order.');
        });
// Added Temporary Stock Reset upon Submit
    tempStock = {}; 
}

function escapeHtml(str) {
    if (str == null) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// Init
document.addEventListener('DOMContentLoaded', () => {
    filterCategory('ALL');
// Added Temporary Stock Refresh and Initialization
    document.querySelectorAll('.product-card-wrapper').forEach(card => {
        tempStock[card.id.replace('product_', '')] = parseInt(card.dataset.stock);
    });

    updateCartUI();
});
</script>
</body>
</html>
