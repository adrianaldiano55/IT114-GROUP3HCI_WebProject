const WebSocket = require('ws');
const mysql = require('mysql2');

const db = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '', 
    database: 'faydss'
});

let itemClicks = {};
let lastCategoryCount = 0; 
let lastUpdate = ""; 
let previousCategories = []; 
let previousStockMap = {}; 
let lastStatusMap = {}; // Tracks order status changes

function logActivity(userId, action, details) {
    const query = "INSERT INTO audit_logs (user_id, action, details, created_at) VALUES (?, ?, ?, NOW())";
    db.query(query, [userId, action, details], (err) => {
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
            const productName = product.name;

            if (previousStockMap[id] !== undefined && previousStockMap[id] !== currentStock) {
                let message = (currentStock === 0) ? `${productName} is now SOLD OUT!` : `Stock updated for ${productName}: ${currentStock} remaining.`;

                console.log(`[ACTIVITY] Stock Change: ${message}`);

                broadcast(JSON.stringify({ 
                    event: "activity_alert", 
                    message: message, 
                    prod_id: id, 
                    new_stock: currentStock 
                }));
                
                logActivity(0, "SYSTEM", message);
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
            const currentStatus = (order.status || 'pending').toLowerCase();

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
                    logActivity(0, "SYSTEM", statusMsg);
                }
            }
            lastStatusMap[id] = currentStatus;
        });
    });
}, 3000);

const wss = new WebSocket.Server({ port: 3000 });
console.log(">> [SYSTEM] WebSocket Server Running on Port 3000");

wss.on('connection', (ws) => {
    ws.on('message', (message) => {
        const data = JSON.parse(message);

        switch (data.event) {
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

                const orderQuery = `INSERT INTO orders (customer_id, staff_id, price_total, status, created_at, due_at, address) VALUES (?, NULL, ?, 'Pending', NOW(), ?, ?)`;
                const address = data.delivery_address || null;

                db.query(orderQuery, [data.customer_id, totalOrderPrice, formattedDueAt, address], (err, result) => {
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
                                            const stockMsg = `Stock for ${selRes[0].name} updated to ${newStock} via Order #${orderId}`;
                                            
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
                            const unitPrice = parseFloat(item.price);
                            const discount = parseFloat(item.discount || 0);
                            const subtotal = (unitPrice * (1 - (discount / 100))) * item.qty;
                            return [orderId, item.id, item.qty, unitPrice, discount, subtotal];
                        });

                        const itemsQuery = "INSERT INTO order_items (order_id, product, quantity, price, discount, subtotal) VALUES ?";
                        db.query(itemsQuery, [itemValues], (itemErr) => { if (itemErr) console.error(itemErr); });
                    }

                    broadcast(JSON.stringify({ 
                        event: "activity_alert", 
                        message: `Placed order #${orderId}` 
                    }));
                    
                    logActivity(data.customer_id, "ORDER", `Placed order #${orderId}`);
                    broadcast(JSON.stringify({ event: "order_confirmed", orderId: orderId }));
                    broadcastLiveStats();
                });
                break;
        }
    });
});

function broadcast(payload) { wss.clients.forEach(client => { if (client.readyState === WebSocket.OPEN) client.send(payload); }); }
function broadcastTopItems() { broadcast(JSON.stringify({ event: "update_best_sellers", top_item_ids: getTopThree() })); }
function sendTopItems(client) { client.send(JSON.stringify({ event: "update_best_sellers", top_item_ids: getTopThree() })); }
function getTopThree() { return Object.keys(itemClicks).sort((a, b) => itemClicks[b] - itemClicks[a]).slice(0, 3); }