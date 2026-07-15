const WebSocket = require('ws');
const mysql = require('mysql2');
const fs = require('fs');
const path = require('path');
const PHPUnserialize = require('php-unserialize');

function loadEnvFile(filePath) {
    if (!fs.existsSync(filePath)) {
        return;
    }

    const contents = fs.readFileSync(filePath, 'utf8');
    for (const rawLine of contents.split(/\r?\n/)) {
        const line = rawLine.trim();
        if (!line || line.startsWith('#')) {
            continue;
        }

        const equals = line.indexOf('=');
        if (equals === -1) {
            continue;
        }

        const key = line.slice(0, equals).trim();
        let value = line.slice(equals + 1).trim();
        if ((value.startsWith('"') && value.endsWith('"')) || (value.startsWith("'") && value.endsWith("'"))) {
            value = value.slice(1, -1);
        }

        if (process.env[key] === undefined) {
            process.env[key] = value;
        }
    }
}

loadEnvFile(path.join(__dirname, '.env'));

for (const requiredEnv of ['DB_HOST', 'DB_USER', 'DB_DATABASE', 'ADMIN_USER', 'ADMIN_PASS']) {
    if (process.env[requiredEnv] === undefined) {
        throw new Error(`Missing required environment variable: ${requiredEnv}`);
    }
}

const db = mysql.createConnection({
    host: process.env.DB_HOST,
    user: process.env.DB_USER,
    password: process.env.DB_PASSWORD || '',
    database: process.env.DB_DATABASE
});

// ROLE BASED PERMISSIONS  
const ROLE_PERMISSIONS = {
    customer: [
    "menu_click",
    "create_order",
    "client_connected",
    "update_order"
    ],

    staff: [
    "menu_click",
    "client_connected",
    "update_staff_delivery",
    "update_order"
    ],

    admin: [
        "*"
    ]
};

let itemClicks = {};
let lastCategoryCount = 0; 
let lastUpdate = ""; 
let previousCategories = []; 
let previousStockMap = {}; 
let lastStatusMap = {}; // Tracks order status changes

// ROLE PERMISSIONS
function hasPermission(ws, action) {
    const role = ws.user?.role || "customer";
    const allowed = ROLE_PERMISSIONS[role] || [];

    return allowed.includes("*") || allowed.includes(action);
}

// CODE SANITIZATION  
function sanitizeInput(input) {
    if (typeof input !== 'string') return input;

    return input
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

// CSRF PROTECTION
function validateCSRF(ws, data) {

    // Require token
    if (!data.csrf_token) {
        ws.send(JSON.stringify({
            event: "security_error",
            message: "Missing CSRF token"
        }));
        return false;
    }

    // Require session cookie
    if (!ws.sessionId) {
        ws.send(JSON.stringify({
            event: "security_error",
            message: "Missing session"
        }));
        return false;
    }

    try {

        // Read PHP session file
        const sessionPath =
            `C:/xampp/tmp/sess_${ws.sessionId}`;

        if (!fs.existsSync(sessionPath)) {
            return false;
        }

        const raw = fs.readFileSync(sessionPath, 'utf8');
        const session = PHPUnserialize.unserializeSession(raw);

        ws.user = session.user || { id: null, role: "customer" };

        // Compare token
        if (
            !session.csrf_token ||
            session.csrf_token !== data.csrf_token
        ) {

            ws.send(JSON.stringify({
                event: "security_error",
                message: "CSRF validation failed"
            }));

            return false;
        }

        return true;

    } catch (err) {

        console.error("CSRF Validation Error:", err);

        ws.send(JSON.stringify({
            event: "security_error",
            message: "Security validation failed"
        }));

        return false;
    }
}

function logActivity(userId, action, details) {
    const query = "INSERT INTO audit_logs (user_id, action, details, created_at) VALUES (?, ?, ?, NOW())";
    db.execute(query, [userId, action, details], (err) => {
        if (err) console.error("Audit Log Error:", err);
    });
}

function broadcastLiveStats() {
    db.query("SELECT COUNT(*) as totalOrders FROM orders", (err, results) => {
        if (!err && results.length > 0) {
            broadcast(JSON.stringify({ 
                event: "live_stats", 
                total_orders: results[0].totalOrders 
            }));
        }
    });

    db.query("SELECT category, SUM(stock) as totalStock FROM products GROUP BY category", (err, results) => {
        if (!err) {
            const chartData = {};
            results.forEach(row => {
                chartData[row.category] = row.totalStock;
            });
            broadcast(JSON.stringify({ event: "stock_update", chartData: chartData }));
        }
    });
}

// Check for Database Changes every 3 seconds
setInterval(() => {
    // Check for Category changes
    db.query("SELECT * FROM categories", (err, currentCategories) => {
        if (err) return;
        const currentCount = currentCategories.length;
        const maxMod = currentCategories.reduce((max, c) => {
            const time = c.updated_at ? new Date(c.updated_at).getTime() : 0;
            return time > max ? time : max;
        }, 0).toString();

        if (previousCategories.length > 0) {
            if (currentCount !== previousCategories.length || (maxMod !== lastUpdate && lastUpdate !== "")) {
                broadcast(JSON.stringify({ event: "db_category_changed" }));
            }
        }
        previousCategories = JSON.parse(JSON.stringify(currentCategories));
        lastCategoryCount = currentCount;
        lastUpdate = maxMod;
    });

    // Check for Stock changes (Manual edits in DB or restocking)
    db.query("SELECT prod_id, name, stock FROM products", (err, results) => {
        if (err) return;

        results.forEach(product => {
            const id = product.prod_id;
            const currentStock = parseInt(product.stock);
            const productName = sanitizeInput(product.name);

            if (previousStockMap[id] !== undefined && previousStockMap[id] !== currentStock) {
                let message = (currentStock === 0) ? `${productName} is now SOLD OUT!` : `Stock updated for ${productName}: ${currentStock} remaining.`;

                console.log(`[ACTIVITY] Stock Change: ${message}`);

                broadcast(JSON.stringify({ 
                    event: "activity_alert", 
                    message: message, 
                    prod_id: id, 
                    new_stock: currentStock 
                }));
                
                logActivity(ws.user.id, "SYSTEM", message);
                broadcastLiveStats(); 
            }
            previousStockMap[id] = currentStock;
        });
    });

    // NEW: Check for Order Status changes
    db.query("SELECT order_id, status FROM orders", (err, results) => {
        if (err) return;

        results.forEach(order => {
            const id = order.order_id;
            const currentStatus = (order.status || 'processing').toLowerCase();

            if (lastStatusMap[id] !== undefined && lastStatusMap[id] !== currentStatus) {
                let statusMsg = "";

                if (currentStatus === 'processing') {
                    statusMsg = `Your order #${id} is now processing.`;
                } else if (currentStatus === 'completed') {
                    statusMsg = `You successfully claimed your order #${id}!`;
                } else if (currentStatus === 'cancelled') {
                    statusMsg = `Order #${id} has been cancelled.`;
                }

                if (statusMsg !== "") {
                    console.log(`[STATUS CHANGE] Order #${id}: ${currentStatus}`);
                    broadcast(JSON.stringify({ 
                        event: "activity_alert", 
                        message: statusMsg 
                    }));
                    logActivity(ws.user.id, "SYSTEM", statusMsg);
                }
            }
            lastStatusMap[id] = currentStatus;
        });
    });
}, 3000);

const wsPort = Number(process.env.WS_PORT || 3000);
const wss = new WebSocket.Server({ port: wsPort });
console.log(`>> [SYSTEM] WebSocket Server Running on Port ${wsPort}`);

wss.on('connection', (ws, req) => {
    const cookies = req.headers.cookie || '';
    const match = cookies.match(/PHPSESSID=([^;]+)/);

    if (match) {
        ws.sessionId = match[1];
    }

    // Load session immediately
    try {
        if (ws.sessionId) {
            const sessionPath = `C:/xampp/tmp/sess_${ws.sessionId}`;

            if (fs.existsSync(sessionPath)) {
                const raw = fs.readFileSync(sessionPath, 'utf8');
                const session = PHPUnserialize.unserializeSession(raw);

                ws.user = {
                    id: session.user_id || null,
                    role: session.usertype || "customer"
                };
            }
        }
    } catch (err) {
        console.error("Session load error:", err);
    }
    ws.on('message', (message) => {
        const data = JSON.parse(message);
        const action = data.event || data.type;

        // 1. RBAC FIRST
        if (!hasPermission(ws, action)) {
            ws.send(JSON.stringify({
                event: "security_error",
                message: `Access denied for role: ${ws.user?.role || "guest"}`
            }));
            return;
        }
        switch (data.event || data.type) {
            case "client_connected":
                console.log(`[SYSTEM] Client Connected`);
                db.query("SELECT details as message FROM audit_logs WHERE action != 'LOGIN' ORDER BY created_at DESC LIMIT 5", (err, logs) => {
                    if (!err) ws.send(JSON.stringify({ event: "initial_logs", logs: logs.reverse() }));
                });
                sendTopItems(ws);
                broadcastLiveStats();
                break;

            case "menu_click":
                console.log(`[ACTIVITY] User clicked menu item ID: ${data.item_id}`);
                itemClicks[data.item_id] = (itemClicks[data.item_id] || 0) + 1;
                broadcastTopItems();
                break;

            case "create_order":
                const totalOrderPrice = data.items.reduce((sum, item) => {
                    const discountedPrice = item.price * (1 - (item.discount / 100));
                    return sum + (discountedPrice * item.qty);
                }, 0);

                let formattedDueAt = data.due_time || data.due_date; 
                if (formattedDueAt && formattedDueAt.length <= 10) formattedDueAt = `${formattedDueAt} 00:00:00`;

                const orderQuery = `INSERT INTO orders (customer_id, staff_id, price_total, status, created_at, due_at, address) VALUES (?, NULL, ?, 'processing', NOW(), ?, ?)`;
                const address = data.delivery_address
                ? sanitizeInput(data.delivery_address)
                : null;

                db.execute(orderQuery, [data.customer_id, totalOrderPrice, formattedDueAt, address], (err, result) => {
                    if (err) return console.error(">> [SQL ERROR]:", err.sqlMessage);
                    const orderId = result.insertId;

                    console.log(`[ACTIVITY] New Order Created: #${orderId} by Customer ${data.customer_id}`);

                    if (data.items && data.items.length > 0) {
                        data.items.forEach(item => {
                            db.query("UPDATE products SET stock = stock - ? WHERE prod_id = ?", [item.qty, item.id], (updErr) => {
                                if (!updErr) {
                                    db.query("SELECT stock, name FROM products WHERE prod_id = ?", [item.id], (selErr, selRes) => {
                                        if (!selErr && selRes.length > 0) {
                                            const newStock = parseInt(selRes[0].stock);
                                            const cleanProductName = sanitizeInput(selRes[0].name);
                                            const stockMsg = `Stock for ${cleanProductName} updated to ${newStock} via Order #${orderId}`;
                                            
                                            broadcast(JSON.stringify({
                                                event: "activity_alert",
                                                prod_id: item.id,
                                                new_stock: newStock,
                                                message: stockMsg
                                            }));
                                            previousStockMap[item.id] = newStock;
                                        }
                                    });
                                }
                            });
                        });

                        const itemValues = data.items.map(item => {
                            const unitPrice = Number(item.price) || 0;
                            const discount = parseFloat(item.discount || 0);
                            const subtotal = (unitPrice * (1 - (discount / 100))) * item.qty;
                            return [orderId, item.id, item.qty, unitPrice, discount, subtotal];
                        });

                        const itemsQuery = "INSERT INTO order_items (order_id, product, quantity, price, discount, subtotal) VALUES ?";
                        db.execute(itemsQuery, [itemValues], (itemErr) => { if (itemErr) console.error(itemErr); });
                    }

                    broadcast(JSON.stringify({ 
                        event: "activity_alert", 
                        message: `Placed order #${orderId}` 
                    }));
                    
                    logActivity(ws.user.id, "ORDER", `Placed order #${orderId}`);
                    broadcast(JSON.stringify({ event: "order_confirmed", orderId: orderId }));
                    broadcastLiveStats();
                });
                break;
                case "delete_order":
                    const delOrderId = data.payload.orderId;

                    console.log(`[REQUEST] Delete Order #${delOrderId}`);

                    // Optional: delete order items first (if FK not cascading)
                    db.execute("DELETE FROM order_items WHERE order_id = ?", [delOrderId], (itemErr) => {
                        if (itemErr) console.error(itemErr);

                        db.query("DELETE FROM orders WHERE order_id = ?", [delOrderId], (err) => {
                            if (err) {
                                console.error("Delete Error:", err);
                                ws.send(JSON.stringify({
                                    type: "error",
                                    message: "Failed to delete order"
                                }));
                                return;
                            }

                            console.log(`[ACTIVITY] Order #${delOrderId} deleted`);

                            broadcast(JSON.stringify({
                                event: "order_deleted",
                                orderId: delOrderId
                            }));

                            logActivity(ws.user.id, "ORDER_DELETE", `Order #${delOrderId} deleted`);
                            broadcastLiveStats();
                        });
                    });
                break;
                case "update_order":
                    const updOrderId = data.payload.orderId;
                    const newDueTime = data.payload.dueTime;
                    const newAddress = sanitizeInput(data.payload.address);

                     console.log(`[REQUEST] Update Order #${updOrderId}`);

                    // Convert time to MySQL DATETIME format
                    let formattedDue = null;
                    if (newDueTime) {
                        formattedDue = `${new Date().toISOString().split('T')[0]} ${newDueTime}:00`;
                    }

                    const updateQuery = `
                       UPDATE orders 
                        SET due_at = ?, address = ?
                        WHERE order_id = ?
                    `;

                    db.execute(updateQuery, [formattedDue, newAddress, updOrderId], (err) => {
                        if (err) {
                            console.error("Update Error:", err);
                            ws.send(JSON.stringify({
                                type: "error",
                                message: "Failed to update order"
                            }));
                            return;
                        }
                        console.log(`[ACTIVITY] Order #${updOrderId} updated`);

                        broadcast(JSON.stringify({
                            event: "order_updated",
                            payload: {
                                orderId: updOrderId,
                                dueTime: newDueTime,
                                address: newAddress
                            }
                        }));

                        logActivity(ws.user.id, "ORDER_UPDATE", `Order #${updOrderId} updated`);
                        broadcastLiveStats();
                    });
                break;
                case "create_product":
                    const {
                                name: rawProdName,
                                price,
                                stock,
                                discount,
                                image: rawProdImage,
                                category: rawCategory
                            } = data.payload;

                    const prodName = sanitizeInput(rawProdName);
                    const prodImage = sanitizeInput(rawProdImage);
                    const category = sanitizeInput(rawCategory);
                    db.execute("INSERT INTO products (name, price, stock, discount, image_path, category) VALUES (?, ?, ?, ?, ?, ?)", [prodName, price, stock, discount, prodImage, category], (err, result) => {
                        if (err) {
                            console.error("Create Product Error:", err);
                            ws.send(JSON.stringify({ event: "error", message: "Failed to create product" }));
                            return;
                        }
                        const prodId = result.insertId;
                        console.log(`[ACTIVITY] Product "${prodName}" created with ID ${prodId}`);
                        broadcast(JSON.stringify({ event: "product_created", payload: { productId: prodId, name: prodName } }));
                        logActivity(ws.user.id, "PRODUCT_CREATE", `Created product "${prodName}"`);
                    });
                break;

                case "update_product":
                    const {
                            prod_id,
                            name: rawUpdProdName,
                            price: updPrice,
                            stock: updStock,
                            discount: updDiscount,
                            image: rawUpdImage,
                            category: rawUpdCategory
                        } = data.payload;
                    const updProdName = sanitizeInput(rawUpdProdName);
                    const updImage = sanitizeInput(rawUpdImage);
                    const updCategory = sanitizeInput(rawUpdCategory);
                    db.execute("UPDATE products SET name=?, price=?, stock=?, discount=?, image_path=?, category=? WHERE prod_id=?", [updProdName, updPrice, updStock, updDiscount, updImage, updCategory, prod_id], (err) => {
                        if (err) {
                            console.error("Update Product Error:", err);
                            ws.send(JSON.stringify({ event: "error", message: "Failed to update product" }));
                            return;
                        }
                        console.log(`[ACTIVITY] Product "${updProdName}" updated`);
                        broadcast(JSON.stringify({ event: "product_updated", payload: { productId: prod_id, name: updProdName } }));
                        logActivity(ws.user.id, "PRODUCT_UPDATE", `Updated product "${updProdName}"`);
                        broadcastLiveStats(); // In case stock changed
                    });
                break;

                case "delete_product":
                    const { productId } = data.payload;
                    db.execute("SELECT name FROM products WHERE prod_id=?", [productId], (err, res) => {
                        if (err) {
                            console.error("Select Product Error:", err);
                            return;
                        }
                        if (res.length === 0) {
                            ws.send(JSON.stringify({ event: "error", message: "Product not found" }));
                            return;
                        }
                        const prodName = res[0].name;
                        db.execute("DELETE FROM products WHERE prod_id=?", [productId], (delErr) => {
                            if (delErr) {
                                console.error("Delete Product Error:", delErr);
                                ws.send(JSON.stringify({ event: "error", message: "Failed to delete product" }));
                                return;
                            }
                            console.log(`[ACTIVITY] Product "${prodName}" deleted`);
                            broadcast(JSON.stringify({ event: "product_deleted", payload: { productId: productId, name: prodName } }));
                            logActivity(ws.user.id, "PRODUCT_DELETE", `Deleted product "${prodName}"`);
                            broadcastLiveStats();
                        });
                    });
                break;

                case "create_category":
                    const {
                            name: rawCategName,
                            image: rawCategImage
                        } = data.payload;

                    const categName = sanitizeInput(rawCategName);
                    const categImage = sanitizeInput(rawCategImage);
                    db.execute("INSERT INTO categories (name, image_path) VALUES (?, ?)", [categName, categImage], (err, result) => {
                        if (err) {
                            console.error("Create Category Error:", err);
                            ws.send(JSON.stringify({ event: "error", message: "Failed to create category" }));
                            return;
                        }
                        const categId = result.insertId;
                        console.log(`[ACTIVITY] Category "${categName}" created with ID ${categId}`);
                        broadcast(JSON.stringify({ event: "category_created", payload: { categoryId: categId, name: categName } }));
                        logActivity(ws.user.id, "CATEGORY_CREATE", `Created category "${categName}"`);
                    });
                break;

                case "update_category":
                    const {
                            categ_id,
                            name: rawUpdCategName,
                            image: rawUpdCategImage
                        } = data.payload;
                    const updCategName = sanitizeInput(rawUpdCategName);
                    const updCategImage = sanitizeInput(rawUpdCategImage);
                    db.execute("UPDATE categories SET name=?, image_path=? WHERE categ_id=?", [updCategName, updCategImage, categ_id], (err) => {
                        if (err) {
                            console.error("Update Category Error:", err);
                            ws.send(JSON.stringify({ event: "error", message: "Failed to update category" }));
                            return;
                        }
                        console.log(`[ACTIVITY] Category "${updCategName}" updated`);
                        broadcast(JSON.stringify({ event: "category_updated", payload: { categoryId: categ_id, name: updCategName } }));
                        logActivity(ws.user.id, "CATEGORY_UPDATE", `Updated category "${updCategName}"`);
                    });
                break; 

                case "delete_category":
                    const { categoryId } = data.payload;
                    db.execute("SELECT name FROM categories WHERE categ_id=?", [categoryId], (err, res) => {
                        if (err) {
                            console.error("Select Category Error:", err);
                            return;
                        }
                        if (res.length === 0) {
                            ws.send(JSON.stringify({ event: "error", message: "Category not found" }));
                            return;
                        }
                        const categName = res[0].name;
                        db.execute("DELETE FROM categories WHERE categ_id=?", [categoryId], (delErr) => {
                            if (delErr) {
                                console.error("Delete Category Error:", delErr);
                                ws.send(JSON.stringify({ event: "error", message: "Failed to delete category" }));
                                return;
                            }
                            console.log(`[ACTIVITY] Category "${categName}" deleted`);
                            broadcast(JSON.stringify({ event: "category_deleted", payload: { categoryId: categoryId, name: categName } }));
                            logActivity(ws.user.id, "CATEGORY_DELETE", `Deleted category "${categName}"`);
                        });
                    });
                break;
                case "update_staff_delivery":
                    const order_id = data.payload.order_id;
                    const status = sanitizeInput(data.payload.status);
                    db.execute("UPDATE orders SET status = ? WHERE order_id = ?", [status, order_id], (err) => {
                        if (err) {
                            console.error("Update Delivery Error:", err);
                            ws.send(JSON.stringify({ event: "error", message: "Failed to update delivery status" }));
                            return;
                        }
                        console.log(`[ACTIVITY] Order "${order_id}" status updated to "${status}"`);
                        broadcast(JSON.stringify({ event: "order_updated", payload: { orderId: order_id, status: status } }));
                        logActivity(ws.user.id, "ORDER_UPDATE", `Updated order "${order_id}" status to "${status}"`);
                    });
                break;
            }
        });
    });


function broadcast(payload) { wss.clients.forEach(client => { if (client.readyState === WebSocket.OPEN) client.send(payload); }); }
function broadcastTopItems() { broadcast(JSON.stringify({ event: "update_best_sellers", top_item_ids: getTopThree() })); }
function sendTopItems(client) { client.send(JSON.stringify({ event: "update_best_sellers", top_item_ids: getTopThree() })); }
function getTopThree() { return Object.keys(itemClicks).sort((a, b) => itemClicks[b] - itemClicks[a]).slice(0, 3); }