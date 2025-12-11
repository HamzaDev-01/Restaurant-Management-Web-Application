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

// Load cart on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCart();
});
