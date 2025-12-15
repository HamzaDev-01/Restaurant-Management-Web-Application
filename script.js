// Sidebar functionality removed
let sidebar = document.querySelector(".sidebar");

const menuItems = document.querySelectorAll(".menu-item");
menuItems.forEach(item => {
    item.addEventListener("click", () => {
        // Hide all content sections
        const sections = document.querySelectorAll(".content-section");
        sections.forEach(section => {
            section.style.display = "none";
        });

        // Show the relevant section based on data-target attribute
        const target = item.getAttribute("data-target");
        const targetSection = document.getElementById(target);
        if (targetSection) {
            targetSection.style.display = "block";
        }
    });
});

// Show the default "home" section on page load
document.getElementById("home").style.display = "block";

// Update active menu styling
menuItems.forEach(item => {
    item.addEventListener("click", () => {
        menuItems.forEach(i => i.classList.remove("active"));
        item.classList.add("active");
    });
});

// Add a class to highlight the selected menu item
const styleTag = document.createElement("style");
styleTag.innerHTML = `
    .menu-item.active {
        background-color: rgba(255, 111, 97, 0.2);
        
    }
`;
document.head.appendChild(styleTag);

// Cart functionality
let cart = JSON.parse(localStorage.getItem('cart')) || [];
let customerName = localStorage.getItem('customerName') || '';

// Initialize cart display
function updateCart() {
    const cartItemsContainer = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');
    if (!cartItemsContainer) return;
    
    cartItemsContainer.innerHTML = '';
    let total = 0;

    cart.forEach((item, index) => {
        total += item.price * item.quantity;

        const itemDiv = document.createElement('div');
        itemDiv.style.display = 'flex';
        itemDiv.style.justifyContent = 'space-between';
        itemDiv.style.marginBottom = '10px';
        itemDiv.style.alignItems = 'center';
        itemDiv.style.padding = '10px';
        itemDiv.style.backgroundColor = '#f0f0f0';
        itemDiv.style.borderRadius = '5px';

        itemDiv.innerHTML = `
            <span style="font-weight: bold; color: #333;">${item.name} (x${item.quantity})</span>
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-weight: bold; color: #FF5733;">$${(item.price * item.quantity).toFixed(2)}</span>
                <button onclick="removeFromCart(${index})" style="background: #FF5733; color: white; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">Remove</button>
            </div>
        `;
        cartItemsContainer.appendChild(itemDiv);
    });

    if (cartTotal) {
        cartTotal.innerText = `Total: $${total.toFixed(2)}`;
    }
    localStorage.setItem('cart', JSON.stringify(cart));
}

// Add items to the cart with notification
function addToCart(id, name, price) {
    const quantityInput = document.getElementById(`quantity_${id}`);
    const quantity = parseInt(quantityInput.value);

    if (quantity < 1) return;

    const existingItem = cart.find(item => item.id === id);

    if (existingItem) {
        existingItem.quantity += quantity;
    } else {
        cart.push({ id, name, price, quantity });
    }

    // Show notification
    showNotification(`✓ ${name} added to cart!`);
    
    updateCart();
    
    // Reset quantity input
    quantityInput.value = 1;
}

// Remove items from the cart
function removeFromCart(index) {
    const itemName = cart[index].name;
    cart.splice(index, 1);
    showNotification(`✓ ${itemName} removed from cart!`);
    updateCart();
}

// Show notification popup
function showNotification(message) {
    // Remove existing notification if any
    const existingNotif = document.getElementById('notification');
    if (existingNotif) existingNotif.remove();
    
    const notification = document.createElement('div');
    notification.id = 'notification';
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.backgroundColor = '#4CAF50';
    notification.style.color = 'white';
    notification.style.padding = '15px 25px';
    notification.style.borderRadius = '5px';
    notification.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
    notification.style.fontSize = '16px';
    notification.style.fontWeight = 'bold';
    notification.style.zIndex = '9999';
    notification.style.animation = 'slideIn 0.3s ease-in-out';
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-in-out';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add animation styles
const animationStyle = document.createElement('style');
animationStyle.innerHTML = `
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
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(animationStyle);

// Save customer name
function saveCustomerName() {
    customerName = document.getElementById('customerName').value;
    localStorage.setItem('customerName', customerName);
}

// Save customer email
function saveCustomerEmail() {
    const customerEmail = document.getElementById('customerEmail').value;
    localStorage.setItem('customerEmail', customerEmail);
}

// Save customer address
function saveCustomerAddress() {
    const customerAddress = document.getElementById('customerAddress').value;
    localStorage.setItem('customerAddress', customerAddress);
}

// Save customer phone
function saveCustomerPhone() {
    const customerPhone = document.getElementById('customerPhone').value;
    localStorage.setItem('customerPhone', customerPhone);
}

// Handle form submission for order placement
function setupOrderFormHandler() {
    const orderForm = document.getElementById('orderForm');
    if (!orderForm) return;

    // Remove default form submission
    orderForm.addEventListener('submit', function(e) {
        e.preventDefault();

        if (cart.length === 0) {
            showNotification('Please add items to your cart before placing an order');
            return;
        }

        // Prepare form data
        const formData = new FormData(orderForm);

        // Add cart items to form data
        cart.forEach((item, index) => {
            formData.append(`cart[${index}][name]`, item.name);
            formData.append(`cart[${index}][price]`, item.price);
            formData.append(`cart[${index}][quantity]`, item.quantity);
        });

        // Submit the order
        fetch('save_order.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message with order tracking
                showOrderSuccessModal(data.order_id);

                // Clear cart and form
                cart = [];
                localStorage.removeItem('cart');
                localStorage.removeItem('customerName');
                localStorage.removeItem('customerEmail');
                localStorage.removeItem('customerAddress');
                localStorage.removeItem('customerPhone');

                document.getElementById('customerName').value = '';
                document.getElementById('customerEmail').value = '';
                document.getElementById('customerAddress').value = '';
                document.getElementById('customerPhone').value = '';

                updateCart();
            } else {
                showNotification('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error placing order. Please try again.');
        });
    });
}

// Show order success modal with tracking info
function showOrderSuccessModal(orderId) {
    // Create modal
    const modal = document.createElement('div');
    modal.style.position = 'fixed';
    modal.style.top = '0';
    modal.style.left = '0';
    modal.style.width = '100%';
    modal.style.height = '100vh';
    modal.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
    modal.style.display = 'flex';
    modal.style.justifyContent = 'center';
    modal.style.alignItems = 'center';
    modal.style.zIndex = '10000';

    const modalContent = document.createElement('div');
    modalContent.style.backgroundColor = 'white';
    modalContent.style.padding = '40px';
    modalContent.style.borderRadius = '15px';
    modalContent.style.textAlign = 'center';
    modalContent.style.maxWidth = '500px';
    modalContent.style.boxShadow = '0 10px 40px rgba(0, 0, 0, 0.3)';

    modalContent.innerHTML = `
        <div style="font-size: 4em; color: #4CAF50; margin-bottom: 20px;">✓</div>
        <h2 style="color: #ff6f61; margin-bottom: 15px;">Order Placed Successfully!</h2>
        <p style="font-size: 1.2em; margin-bottom: 10px;">Your Order ID is: <strong style="color: #ff6f61; font-size: 1.3em;">#${orderId}</strong></p>
        <p style="color: #666; margin-bottom: 30px;">Please save this ID to track your order</p>
        <a href="track_order.php?order_id=${orderId}" style="display: inline-block; background: linear-gradient(135deg, #ff6f61 0%, #ff9068 100%); color: white; padding: 15px 30px; border-radius: 10px; text-decoration: none; font-weight: bold; margin-bottom: 10px;">Track Your Order</a>
        <br>
        <button onclick="this.closest('div').parentElement.remove()" style="background: #ddd; color: #333; padding: 12px 25px; border: none; border-radius: 10px; cursor: pointer; margin-top: 15px; font-weight: bold;">Close</button>
    `;

    modal.appendChild(modalContent);
    document.body.appendChild(modal);

    // Close on click outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

// Track order functionality
function trackOrderNow() {
    const orderId = document.getElementById('trackOrderIdInput').value;

    if (!orderId) {
        showTrackError('Please enter an order ID');
        return;
    }

    fetch(`get_order_status.php?order_id=${orderId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayTrackOrderDetails(data.order);
                document.getElementById('trackErrorMessage').style.display = 'none';
                document.getElementById('trackOrderDetails').style.display = 'block';
            } else {
                showTrackError(data.message || 'Order not found');
                document.getElementById('trackOrderDetails').style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showTrackError('Error fetching order details');
        });
}

function showTrackError(message) {
    const errorDiv = document.getElementById('trackErrorMessage');
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
}

function displayTrackOrderDetails(order) {
    // Display order ID
    document.getElementById('trackOrderIdDisplay').textContent = `Order #${order.order_id}`;

    // Build status timeline with 5-second timer
    const timeline = document.getElementById('trackStatusTimeline');
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

    // Simulate 5-second progression if order is in progress
    let currentStatusIndex = statuses.findIndex(s => s.key === order.delivery_status);

    statuses.forEach((status, index) => {
        const stepClass = status.active ? (index < currentStatusIndex ? 'completed' : 'active') : '';

        const stepDiv = document.createElement('div');
        stepDiv.className = `status-step ${stepClass}`;
        stepDiv.style.display = 'flex';
        stepDiv.style.alignItems = 'center';
        stepDiv.style.marginBottom = '30px';
        stepDiv.style.position = 'relative';

        // Add connecting line
        if (index < statuses.length - 1) {
            const line = document.createElement('div');
            line.style.position = 'absolute';
            line.style.left = '20px';
            line.style.top = '50px';
            line.style.width = '3px';
            line.style.height = 'calc(100% + 10px)';
            line.style.background = status.active ? '#ff6f61' : '#e0e0e0';
            stepDiv.appendChild(line);
        }

        let timeDisplay = '';
        if (status.time) {
            const date = new Date(status.time);
            timeDisplay = `<div style="color: #666; font-size: 0.9em;">${date.toLocaleDateString()} ${date.toLocaleTimeString()}</div>`;
        }

        let subtitle = status.subtitle ? `<div style="color: #666; font-size: 0.9em;">${status.subtitle}</div>` : '';

        // Add timer for in-progress steps
        let timerDisplay = '';
        if (order.delivery_status !== 'delivered' && index === currentStatusIndex + 1) {
            timerDisplay = `<div id="timer-${status.key}" style="color: #ff6f61; font-size: 0.9em; font-weight: bold; margin-top: 5px;">Estimated time: <span class="countdown">5:00</span></div>`;
        }

        stepDiv.innerHTML = `
            <div style="width: 40px; height: 40px; border-radius: 50%; background: ${status.active ? (index < currentStatusIndex ? '#28a745' : '#ff6f61') : '#e0e0e0'}; display: flex; align-items: center; justify-content: center; margin-right: 20px; font-size: 1.5em; color: white; z-index: 1;">
                <i class='bx ${status.icon}'></i>
            </div>
            <div style="flex: 1;">
                <div style="font-size: 1.3em; font-weight: bold; color: #333; margin-bottom: 5px;">${status.title}</div>
                ${subtitle}
                ${timeDisplay}
                ${timerDisplay}
            </div>
        `;

        timeline.appendChild(stepDiv);
    });

    // Start 5-second countdown for next phase if not delivered
    if (order.delivery_status !== 'delivered' && currentStatusIndex < statuses.length - 1) {
        const nextStatus = statuses[currentStatusIndex + 1];
        startPhaseCountdown(nextStatus.key, 5, order.order_id);
    }

    // Show delivered message if order is delivered
    const deliveredMsg = document.getElementById('trackDeliveredMessage');
    if (order.delivery_status === 'delivered') {
        deliveredMsg.style.display = 'block';
    } else {
        deliveredMsg.style.display = 'none';
    }

    // Display customer info
    const customerInfo = document.getElementById('trackCustomerInfo');
    customerInfo.innerHTML = `
        <div style="display: grid; grid-template-columns: 150px 1fr; gap: 10px; margin-bottom: 10px;">
            <div style="font-weight: bold; color: #ff6f61;">Customer Name:</div>
            <div>${order.customer_name}</div>
        </div>
        <div style="display: grid; grid-template-columns: 150px 1fr; gap: 10px; margin-bottom: 10px;">
            <div style="font-weight: bold; color: #ff6f61;">Email:</div>
            <div>${order.email}</div>
        </div>
        <div style="display: grid; grid-template-columns: 150px 1fr; gap: 10px; margin-bottom: 10px;">
            <div style="font-weight: bold; color: #ff6f61;">Phone:</div>
            <div>${order.phone_number}</div>
        </div>
        <div style="display: grid; grid-template-columns: 150px 1fr; gap: 10px; margin-bottom: 10px;">
            <div style="font-weight: bold; color: #ff6f61;">Address:</div>
            <div>${order.address}</div>
        </div>
    `;

    // Display order items
    const orderItemsDiv = document.getElementById('trackOrderItems');
    let itemsHtml = '';
    let total = 0;

    order.items.forEach(item => {
        const itemTotal = parseFloat(item.price) * parseInt(item.quantity);
        total += itemTotal;

        itemsHtml += `
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ddd;">
                <div>${item.quantity}x ${item.menu_item_name}</div>
                <div>$${itemTotal.toFixed(2)}</div>
            </div>
        `;
    });

    itemsHtml += `
        <div style="display: flex; justify-content: space-between; padding: 15px 0; font-size: 1.3em; font-weight: bold; color: #ff6f61; border-top: 2px solid #ff6f61; margin-top: 10px;">
            <div>Total:</div>
            <div>$${total.toFixed(2)}</div>
        </div>
    `;

    orderItemsDiv.innerHTML = itemsHtml;
}

// Countdown timer for order phases (5 seconds = 300 seconds for demo, use 300 for 5 minutes)
function startPhaseCountdown(phaseKey, minutes, orderId) {
    const timerElement = document.querySelector(`#timer-${phaseKey} .countdown`);
    if (!timerElement) return;

    let totalSeconds = minutes * 60; // Convert minutes to seconds

    const countdownInterval = setInterval(() => {
        totalSeconds--;

        const mins = Math.floor(totalSeconds / 60);
        const secs = totalSeconds % 60;
        timerElement.textContent = `${mins}:${secs.toString().padStart(2, '0')}`;

        if (totalSeconds <= 0) {
            clearInterval(countdownInterval);
            timerElement.textContent = 'Processing...';
            // Auto-refresh order status after timer expires
            setTimeout(() => {
                if (document.getElementById('trackOrderIdInput').value == orderId) {
                    trackOrderNow();
                }
            }, 1000);
        }
    }, 1000);
}

// Allow enter key to track order
document.addEventListener('DOMContentLoaded', function() {
    const trackInput = document.getElementById('trackOrderIdInput');
    if (trackInput) {
        trackInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                trackOrderNow();
            }
        });
    }
});

// Load cart on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCart();
    setupOrderFormHandler();
});
