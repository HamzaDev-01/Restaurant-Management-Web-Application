<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Order - Spice Bites</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #ff6f61 0%, #ff9068 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            color: white;
            margin-bottom: 30px;
            font-size: 2.5em;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .search-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 30px;
        }

        .search-form {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .search-input {
            flex: 1;
            padding: 15px;
            border: 2px solid #ff6f61;
            border-radius: 10px;
            font-size: 1.1em;
        }

        .search-btn {
            padding: 15px 30px;
            background: linear-gradient(135deg, #ff6f61 0%, #ff9068 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .search-btn:hover {
            transform: translateY(-2px);
        }

        .order-details {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            display: none;
        }

        .order-header {
            border-bottom: 3px solid #ff6f61;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .order-id-display {
            font-size: 1.8em;
            color: #ff6f61;
            font-weight: bold;
        }

        .status-timeline {
            margin: 30px 0;
            position: relative;
        }

        .status-step {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            position: relative;
        }

        .status-step:before {
            content: '';
            position: absolute;
            left: 20px;
            top: 50px;
            width: 3px;
            height: calc(100% + 10px);
            background: #e0e0e0;
        }

        .status-step:last-child:before {
            display: none;
        }

        .status-step.active:before {
            background: #ff6f61;
        }

        .status-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 1.5em;
            color: white;
            z-index: 1;
        }

        .status-step.active .status-icon {
            background: #ff6f61;
        }

        .status-step.completed .status-icon {
            background: #28a745;
        }

        .status-content {
            flex: 1;
        }

        .status-title {
            font-size: 1.3em;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .status-time {
            color: #666;
            font-size: 0.9em;
        }

        .customer-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .info-row {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 10px;
            margin-bottom: 10px;
        }

        .info-label {
            font-weight: bold;
            color: #ff6f61;
        }

        .order-items {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .item-row:last-child {
            border-bottom: none;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            font-size: 1.3em;
            font-weight: bold;
            color: #ff6f61;
            border-top: 2px solid #ff6f61;
            margin-top: 10px;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #f5c6cb;
            display: none;
            margin-top: 20px;
        }

        .delivered-message {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            margin: 20px 0;
            font-size: 1.3em;
            font-weight: bold;
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
            display: none;
        }

        .delivered-message i {
            font-size: 3em;
            display: block;
            margin-bottom: 15px;
        }

        .home-link {
            text-align: center;
            margin-top: 20px;
        }

        .home-link a {
            color: white;
            text-decoration: none;
            font-size: 1.1em;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            display: inline-block;
            transition: background 0.3s;
        }

        .home-link a:hover {
            background: rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Track Your Order</h1>

        <div class="search-section">
            <h3 style="color: #ff6f61; margin-bottom: 15px;">Enter Your Order ID</h3>
            <div class="search-form">
                <input type="number" id="orderIdInput" class="search-input" placeholder="Enter Order ID (e.g., 123)" min="1">
                <button class="search-btn" onclick="trackOrder()">
                    <i class='bx bx-search-alt'></i> Track Order
                </button>
            </div>
            <div class="error-message" id="errorMessage"></div>
        </div>

        <div class="order-details" id="orderDetails">
            <div class="order-header">
                <div class="order-id-display" id="orderIdDisplay"></div>
            </div>

            <div class="delivered-message" id="deliveredMessage">
                <i class='bx bx-check-circle'></i>
                <div>Your order has been delivered successfully!</div>
                <div style="font-size: 0.8em; margin-top: 10px;">Thank you for ordering from Spice Bites!</div>
            </div>

            <div class="status-timeline" id="statusTimeline">
                <!-- Status steps will be dynamically added here -->
            </div>

            <h3 style="color: #ff6f61; margin-top: 30px;">Customer Information</h3>
            <div class="customer-info" id="customerInfo"></div>

            <h3 style="color: #ff6f61; margin-top: 30px;">Order Items</h3>
            <div class="order-items" id="orderItems"></div>
        </div>

        <div class="home-link">
            <a href="index.html">
                <i class='bx bx-home'></i> Back to Home
            </a>
        </div>
    </div>

    <script>
        function trackOrder() {
            const orderId = document.getElementById('orderIdInput').value;

            if (!orderId) {
                showError('Please enter an order ID');
                return;
            }

            fetch(`get_order_status.php?order_id=${orderId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayOrderDetails(data.order);
                        document.getElementById('errorMessage').style.display = 'none';
                        document.getElementById('orderDetails').style.display = 'block';
                    } else {
                        showError(data.message || 'Order not found');
                        document.getElementById('orderDetails').style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Error fetching order details');
                });
        }

        function displayOrderDetails(order) {
            // Display order ID
            document.getElementById('orderIdDisplay').textContent = `Order #${order.order_id}`;

            // Build status timeline
            const timeline = document.getElementById('statusTimeline');
            timeline.innerHTML = '';

            const statuses = [
                {
                    key: 'pending',
                    icon: 'bx-time',
                    title: 'Order Placed',
                    time: order.order_date,
                    active: true
                },
                {
                    key: 'confirmed',
                    icon: 'bx-check-circle',
                    title: 'Confirmed by Rider',
                    subtitle: order.rider_name ? `Rider: ${order.rider_name}` : null,
                    time: order.confirmation_date,
                    active: order.delivery_status === 'confirmed' || order.delivery_status === 'delivered'
                },
                {
                    key: 'delivered',
                    icon: 'bx-package',
                    title: 'Delivered',
                    time: order.delivery_date,
                    active: order.delivery_status === 'delivered'
                }
            ];

            statuses.forEach((status, index) => {
                const stepClass = status.active ? (index < statuses.findIndex(s => s.key === order.delivery_status) ? 'completed' : 'active') : '';

                const stepDiv = document.createElement('div');
                stepDiv.className = `status-step ${stepClass}`;

                let timeDisplay = '';
                if (status.time) {
                    const date = new Date(status.time);
                    timeDisplay = `<div class="status-time">${date.toLocaleDateString()} ${date.toLocaleTimeString()}</div>`;
                }

                let subtitle = status.subtitle ? `<div class="status-time">${status.subtitle}</div>` : '';

                stepDiv.innerHTML = `
                    <div class="status-icon">
                        <i class='bx ${status.icon}'></i>
                    </div>
                    <div class="status-content">
                        <div class="status-title">${status.title}</div>
                        ${subtitle}
                        ${timeDisplay}
                    </div>
                `;

                timeline.appendChild(stepDiv);
            });

            // Show delivered message if order is delivered
            const deliveredMsg = document.getElementById('deliveredMessage');
            if (order.delivery_status === 'delivered') {
                deliveredMsg.style.display = 'block';
            } else {
                deliveredMsg.style.display = 'none';
            }

            // Display customer info
            const customerInfo = document.getElementById('customerInfo');
            customerInfo.innerHTML = `
                <div class="info-row">
                    <div class="info-label">Customer Name:</div>
                    <div>${order.customer_name}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email:</div>
                    <div>${order.email}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Phone:</div>
                    <div>${order.phone_number}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Address:</div>
                    <div>${order.address}</div>
                </div>
            `;

            // Display order items
            const orderItemsDiv = document.getElementById('orderItems');
            let itemsHtml = '';
            let total = 0;

            order.items.forEach(item => {
                const itemTotal = parseFloat(item.price) * parseInt(item.quantity);
                total += itemTotal;

                itemsHtml += `
                    <div class="item-row">
                        <div>${item.quantity}x ${item.menu_item_name}</div>
                        <div>$${itemTotal.toFixed(2)}</div>
                    </div>
                `;
            });

            itemsHtml += `
                <div class="total-row">
                    <div>Total:</div>
                    <div>$${total.toFixed(2)}</div>
                </div>
            `;

            orderItemsDiv.innerHTML = itemsHtml;
        }

        function showError(message) {
            const errorDiv = document.getElementById('errorMessage');
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        }

        // Allow enter key to search
        document.getElementById('orderIdInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                trackOrder();
            }
        });
    </script>
</body>
</html>
