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
                case "delete_order":
                    const delOrderId = data.payload.orderId;

                    console.log(`[REQUEST] Delete Order #${delOrderId}`);

                    // Optional: delete order items first (if FK not cascading)
                    db.query("DELETE FROM order_items WHERE order_id = ?", [delOrderId], (itemErr) => {
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

                            logActivity(0, "ORDER_DELETE", `Order #${delOrderId} deleted`);
                            broadcastLiveStats();
                        });
                    });
                break;
                case "update_order":
                    const updOrderId = data.payload.orderId;
                    const newDueTime = data.payload.dueTime;
                    const newAddress = data.payload.address;

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

                    db.query(updateQuery, [formattedDue, newAddress, updOrderId], (err) => {
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

                        logActivity(0, "ORDER_UPDATE", `Order #${updOrderId} updated`);
                        broadcastLiveStats();
                    });
                break;
                case "create_product":
                    const { name: prodName, price, stock, discount, image: prodImage, category } = data.payload;
                    db.query("INSERT INTO products (name, price, stock, discount, image_path, category) VALUES (?, ?, ?, ?, ?, ?)", [prodName, price, stock, discount, prodImage, category], (err, result) => {
                        if (err) {
                            console.error("Create Product Error:", err);
                            ws.send(JSON.stringify({ event: "error", message: "Failed to create product" }));
                            return;
                        }
                        const prodId = result.insertId;
                        console.log(`[ACTIVITY] Product "${prodName}" created with ID ${prodId}`);
                        broadcast(JSON.stringify({ event: "product_created", payload: { productId: prodId, name: prodName } }));
                        logActivity(0, "PRODUCT_CREATE", `Created product "${prodName}"`);
                    });
                break;

                case "update_product":
                    const { prod_id, name: updProdName, price: updPrice, stock: updStock, discount: updDiscount, image: updImage, category: updCategory } = data.payload;
                    db.query("UPDATE products SET name=?, price=?, stock=?, discount=?, image_path=?, category=? WHERE prod_id=?", [updProdName, updPrice, updStock, updDiscount, updImage, updCategory, prod_id], (err) => {
                        if (err) {
                            console.error("Update Product Error:", err);
                            ws.send(JSON.stringify({ event: "error", message: "Failed to update product" }));
                            return;
                        }
                        console.log(`[ACTIVITY] Product "${updProdName}" updated`);
                        broadcast(JSON.stringify({ event: "product_updated", payload: { productId: prod_id, name: updProdName } }));
                        logActivity(0, "PRODUCT_UPDATE", `Updated product "${updProdName}"`);
                        broadcastLiveStats(); // In case stock changed
                    });
                break;

                case "delete_product":
                    const { productId } = data.payload;
                    db.query("SELECT name FROM products WHERE prod_id=?", [productId], (err, res) => {
                        if (err) {
                            console.error("Select Product Error:", err);
                            return;
                        }
                        if (res.length === 0) {
                            ws.send(JSON.stringify({ event: "error", message: "Product not found" }));
                            return;
                        }
                        const prodName = res[0].name;
                        db.query("DELETE FROM products WHERE prod_id=?", [productId], (delErr) => {
                            if (delErr) {
                                console.error("Delete Product Error:", delErr);
                                ws.send(JSON.stringify({ event: "error", message: "Failed to delete product" }));
                                return;
                            }
                            console.log(`[ACTIVITY] Product "${prodName}" deleted`);
                            broadcast(JSON.stringify({ event: "product_deleted", payload: { productId: productId, name: prodName } }));
                            logActivity(0, "PRODUCT_DELETE", `Deleted product "${prodName}"`);
                            broadcastLiveStats();
                        });
                    });
                break;

                case "create_category":
                    const { name: categName, image: categImage } = data.payload;
                    db.query("INSERT INTO categories (name, image_path) VALUES (?, ?)", [categName, categImage], (err, result) => {
                        if (err) {
                            console.error("Create Category Error:", err);
                            ws.send(JSON.stringify({ event: "error", message: "Failed to create category" }));
                            return;
                        }
                        const categId = result.insertId;
                        console.log(`[ACTIVITY] Category "${categName}" created with ID ${categId}`);
                        broadcast(JSON.stringify({ event: "category_created", payload: { categoryId: categId, name: categName } }));
                        logActivity(0, "CATEGORY_CREATE", `Created category "${categName}"`);
                    });
                break;

                case "update_category":
                    const { categ_id, name: updCategName, image: updCategImage } = data.payload;
                    db.query("UPDATE categories SET name=?, image_path=? WHERE categ_id=?", [updCategName, updCategImage, categ_id], (err) => {
                        if (err) {
                            console.error("Update Category Error:", err);
                            ws.send(JSON.stringify({ event: "error", message: "Failed to update category" }));
                            return;
                        }
                        console.log(`[ACTIVITY] Category "${updCategName}" updated`);
                        broadcast(JSON.stringify({ event: "category_updated", payload: { categoryId: categ_id, name: updCategName } }));
                        logActivity(0, "CATEGORY_UPDATE", `Updated category "${updCategName}"`);
                    });
                break; 

                case "delete_category":
                    const { categoryId } = data.payload;
                    db.query("SELECT name FROM categories WHERE categ_id=?", [categoryId], (err, res) => {
                        if (err) {
                            console.error("Select Category Error:", err);
                            return;
                        }
                        if (res.length === 0) {
                            ws.send(JSON.stringify({ event: "error", message: "Category not found" }));
                            return;
                        }
                        const categName = res[0].name;
                        db.query("DELETE FROM categories WHERE categ_id=?", [categoryId], (delErr) => {
                            if (delErr) {
                                console.error("Delete Category Error:", delErr);
                                ws.send(JSON.stringify({ event: "error", message: "Failed to delete category" }));
                                return;
                            }
                            console.log(`[ACTIVITY] Category "${categName}" deleted`);
                            broadcast(JSON.stringify({ event: "category_deleted", payload: { categoryId: categoryId, name: categName } }));
                            logActivity(0, "CATEGORY_DELETE", `Deleted category "${categName}"`);
                        });
                    });
                break;
                case "update_staff_delivery":
                    const { order_id, status } = data.payload;
                    db.query("UPDATE orders SET status = ? WHERE order_id = ?", [status, order_id], (err) => {
                        if (err) {
                            console.error("Update Delivery Error:", err);
                            ws.send(JSON.stringify({ event: "error", message: "Failed to update delivery status" }));
                            return;
                        }
                        console.log(`[ACTIVITY] Order "${order_id}" status updated to "${status}"`);
                        broadcast(JSON.stringify({ event: "order_updated", payload: { orderId: order_id, status: status } }));
                        logActivity(0, "ORDER_UPDATE", `Updated order "${order_id}" status to "${status}"`);
                    });
                break;
            }
        });
    });


function broadcast(payload) { wss.clients.forEach(client => { if (client.readyState === WebSocket.OPEN) client.send(payload); }); }
function broadcastTopItems() { broadcast(JSON.stringify({ event: "update_best_sellers", top_item_ids: getTopThree() })); }
function sendTopItems(client) { client.send(JSON.stringify({ event: "update_best_sellers", top_item_ids: getTopThree() })); }
function getTopThree() { return Object.keys(itemClicks).sort((a, b) => itemClicks[b] - itemClicks[a]).slice(0, 3); }