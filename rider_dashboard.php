<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rider Dashboard - Delivery Management</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            color: white;
            margin-bottom: 30px;
            font-size: 2.5em;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .orders-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 20px;
        }

        .section-title {
            color: #667eea;
            font-size: 1.8em;
            margin-bottom: 20px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }

        .order-card {
            background: #f8f9fa;
            border-left: 5px solid #667eea;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .order-card:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .order-card.confirmed {
            border-left-color: #ffa500;
        }

        .order-card.delivered {
            border-left-color: #28a745;
            opacity: 0.7;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .order-id {
            font-size: 1.3em;
            font-weight: bold;
            color: #667eea;
        }

        .status-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9em;
            text-transform: uppercase;
        }

        .status-pending {
            background: #ffc107;
            color: #000;
        }

        .status-confirmed {
            background: #ff9800;
            color: white;
        }

        .status-delivered {
            background: #28a745;
            color: white;
        }

        .order-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .detail-item {
            background: white;
            padding: 10px;
            border-radius: 5px;
        }

        .detail-label {
            font-weight: bold;
            color: #667eea;
            font-size: 0.9em;
            margin-bottom: 5px;
        }

        .detail-value {
            color: #333;
        }

        .order-items {
            background: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .order-items h4 {
            color: #667eea;
            margin-bottom: 10px;
        }

        .item-row {
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1em;
            font-weight: bold;
            transition: all 0.3s;
            text-transform: uppercase;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .btn-confirm {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-deliver {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .rider-input {
            padding: 10px;
            border: 2px solid #667eea;
            border-radius: 5px;
            font-size: 1em;
            width: 200px;
        }

        .no-orders {
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 1.2em;
        }

        .success-message {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            display: none;
            z-index: 1000;
            animation: slideIn 0.5s;
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .refresh-btn {
            background: white;
            color: #667eea;
            border: 2px solid white;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛵 Rider Delivery Dashboard</h1>

        <button class="btn refresh-btn" onclick="loadOrders()">🔄 Refresh Orders</button>

        <div class="orders-section">
            <h2 class="section-title">📦 Pending Orders</h2>
            <div id="pending-orders"></div>
        </div>

        <div class="orders-section">
            <h2 class="section-title">🚚 Confirmed Orders (Out for Delivery)</h2>
            <div id="confirmed-orders"></div>
        </div>

        <div class="orders-section">
            <h2 class="section-title">✅ Delivered Orders</h2>
            <div id="delivered-orders"></div>
        </div>
    </div>

    <div class="success-message" id="successMessage"></div>

    <script>
        function loadOrders() {
            fetch('fetch_orders_for_rider.php')
                .then(response => response.json())
                .then(data => {
                    displayOrders(data);
                })
                .catch(error => {
                    console.error('Error fetching orders:', error);
                });
        }

        function displayOrders(orders) {
            const pendingContainer = document.getElementById('pending-orders');
            const confirmedContainer = document.getElementById('confirmed-orders');
            const deliveredContainer = document.getElementById('delivered-orders');

            pendingContainer.innerHTML = '';
            confirmedContainer.innerHTML = '';
            deliveredContainer.innerHTML = '';

            if (orders.length === 0) {
                pendingContainer.innerHTML = '<div class="no-orders">No orders available</div>';
                return;
            }

            // Group orders by order_id
            const groupedOrders = {};
            orders.forEach(order => {
                if (!groupedOrders[order.order_id]) {
                    groupedOrders[order.order_id] = {
                        ...order,
                        items: []
                    };
                }
                groupedOrders[order.order_id].items.push({
                    name: order.menu_item_name,
                    price: order.item_price,
                    quantity: order.quantity
                });
            });

            // Display grouped orders
            Object.values(groupedOrders).forEach(order => {
                const orderCard = createOrderCard(order);

                if (order.delivery_status === 'pending') {
                    pendingContainer.appendChild(orderCard);
                } else if (order.delivery_status === 'confirmed') {
                    confirmedContainer.appendChild(orderCard);
                } else if (order.delivery_status === 'delivered') {
                    deliveredContainer.appendChild(orderCard);
                }
            });

            // Add "no orders" messages if sections are empty
            if (pendingContainer.innerHTML === '') {
                pendingContainer.innerHTML = '<div class="no-orders">No pending orders</div>';
            }
            if (confirmedContainer.innerHTML === '') {
                confirmedContainer.innerHTML = '<div class="no-orders">No confirmed orders</div>';
            }
            if (deliveredContainer.innerHTML === '') {
                deliveredContainer.innerHTML = '<div class="no-orders">No delivered orders yet</div>';
            }
        }

        function createOrderCard(order) {
            const card = document.createElement('div');
            card.className = `order-card ${order.delivery_status}`;

            let statusBadge = `<span class="status-badge status-${order.delivery_status}">${order.delivery_status}</span>`;

            let itemsHtml = order.items.map(item =>
                `<div class="item-row">${item.quantity}x ${item.name} - $${item.price}</div>`
            ).join('');

            let actionButtons = '';
            if (order.delivery_status === 'pending') {
                actionButtons = `
                    <input type="text" class="rider-input" id="rider-name-${order.order_id}" placeholder="Enter your name" />
                    <button class="btn btn-confirm" onclick="confirmOrder(${order.order_id})">Confirm Pickup</button>
                `;
            } else if (order.delivery_status === 'confirmed') {
                actionButtons = `
                    <button class="btn btn-deliver" onclick="markAsDelivered(${order.order_id})">Mark as Delivered</button>
                `;
            }

            card.innerHTML = `
                <div class="order-header">
                    <div class="order-id">Order #${order.order_id}</div>
                    ${statusBadge}
                </div>

                <div class="order-details">
                    <div class="detail-item">
                        <div class="detail-label">Customer Name</div>
                        <div class="detail-value">${order.customer_name}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Phone</div>
                        <div class="detail-value">${order.phone_number}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Delivery Address</div>
                        <div class="detail-value">${order.address}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Total Amount</div>
                        <div class="detail-value">$${parseFloat(order.total_price).toFixed(2)}</div>
                    </div>
                    ${order.rider_name ? `
                    <div class="detail-item">
                        <div class="detail-label">Rider</div>
                        <div class="detail-value">${order.rider_name}</div>
                    </div>
                    ` : ''}
                </div>

                <div class="order-items">
                    <h4>Order Items:</h4>
                    ${itemsHtml}
                </div>

                <div class="action-buttons">
                    ${actionButtons}
                </div>
            `;

            return card;
        }

        function confirmOrder(orderId) {
            const riderName = document.getElementById(`rider-name-${orderId}`).value.trim();

            if (!riderName) {
                alert('Please enter your name before confirming the order');
                return;
            }

            fetch('update_order_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `order_id=${orderId}&action=confirm&rider_name=${encodeURIComponent(riderName)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(`Order #${orderId} confirmed! You can now deliver it.`);
                    loadOrders();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to confirm order');
            });
        }

        function markAsDelivered(orderId) {
            if (!confirm('Are you sure you want to mark this order as delivered?')) {
                return;
            }

            fetch('update_order_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `order_id=${orderId}&action=deliver`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(`🎉 Order #${orderId} has been delivered successfully!`);
                    loadOrders();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to mark order as delivered');
            });
        }

        function showMessage(message) {
            const messageDiv = document.getElementById('successMessage');
            messageDiv.textContent = message;
            messageDiv.style.display = 'block';

            setTimeout(() => {
                messageDiv.style.display = 'none';
            }, 4000);
        }

        // Load orders on page load
        window.onload = function() {
            loadOrders();
            // Auto-refresh every 30 seconds
            setInterval(loadOrders, 30000);
        };
    </script>
</body>
</html>
