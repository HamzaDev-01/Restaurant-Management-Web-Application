# Order Tracking Features - Complete Guide

## ✨ New Features Added

### 1. **Track Order Button in User Navigation**
- Added "Track Order" menu item in the main sidebar
- Icon: Package icon (📦)
- Users can now easily access order tracking without leaving the main site

### 2. **Embedded Order Tracking Page**
- Full order tracking interface integrated into [index.html](index.html)
- No need to navigate to a separate page
- Seamless user experience

### 3. **5-Second Timer Between Order Phases**
The system now includes an intelligent countdown timer that:
- Shows "Estimated time: 5:00" for the next phase
- Counts down in real-time (5 minutes = 5:00 → 4:59 → 4:58...)
- Auto-refreshes the order status when timer reaches 0
- Updates the order display automatically

**How it works:**
- **Pending → Confirmed**: Shows 5-minute countdown until rider confirmation
- **Confirmed → Delivered**: Shows 5-minute countdown until delivery
- **Delivered**: Shows success message with celebration

### 4. **Admin Dashboard Enhancements**
Two new buttons added to the admin dashboard:

#### a) Rider Dashboard Button
- Purple button in the top-right corner
- Opens rider dashboard in a new tab
- Quick access for managing deliveries

#### b) Track Order Button
- Green button in the welcome section
- Opens tracking page in a new tab
- Admins can quickly check any order status

## 🎯 How to Use

### For Customers:
1. Click "Track Order" in the sidebar
2. Enter your Order ID (received after checkout)
3. See your order status with live countdown timer
4. Watch the phases progress:
   - ⏰ **Order Placed** (immediate)
   - ✅ **Confirmed by Rider** (5-minute timer)
   - 📦 **Delivered** (5-minute timer)

### For Admins:
1. Login to admin dashboard
2. Click "Rider Dashboard" (top-right) to manage deliveries
3. Click "Track Order" (welcome section) to check order status
4. Monitor all orders in the order history table

### For Riders:
1. Access [rider_dashboard.php](rider_dashboard.php)
2. View all pending orders
3. Enter your name and click "Confirm Pickup"
4. After delivery, click "Mark as Delivered"

## ⏱️ Timer Functionality

### Configuration
The timer is set to **5 minutes** (300 seconds) per phase:

```javascript
// In script.js line 425
startPhaseCountdown(nextStatus.key, 5, order.order_id);
```

### To Change Timer Duration:
Edit the `startPhaseCountdown` function call:
- `5` = 5 minutes
- Change to `10` for 10 minutes
- Change to `1` for 1 minute (for testing)

### Timer Features:
- **Real-time countdown**: Updates every second
- **Auto-refresh**: Automatically checks order status when timer expires
- **Visual feedback**: Shows "Processing..." when timer completes
- **Smart refresh**: Only refreshes if you're still viewing the same order

## 📱 User Interface Updates

### Sidebar Menu (index.html)
```
Home
Contact Us
Order
🆕 Track Order ← NEW!
About Us
```

### Admin Dashboard
```
Admin Dashboard     [Rider Dashboard] [Logout]

Welcome Section:
- Quick Stats
- [Track Order] ← NEW!
```

## 🔧 Technical Details

### Files Modified:
1. **[index.html](index.html)**
   - Added "Track Order" menu item (line 48-54)
   - Added track order section (line 306-342)
   - Added buttons to admin dashboard (line 398-415)

2. **[script.js](script.js)**
   - Added `trackOrderNow()` function (line 303-327)
   - Added `displayTrackOrderDetails()` function (line 335-482)
   - Added `startPhaseCountdown()` timer function (line 485-509)
   - Auto-refresh on timer expiry (line 502-506)

### Timer Implementation:
```javascript
function startPhaseCountdown(phaseKey, minutes, orderId) {
    let totalSeconds = minutes * 60;

    const countdownInterval = setInterval(() => {
        totalSeconds--;
        // Update display: MM:SS

        if (totalSeconds <= 0) {
            clearInterval(countdownInterval);
            // Auto-refresh order status
            trackOrderNow();
        }
    }, 1000);
}
```

## 🎨 Visual Features

### Status Timeline:
- **Gray Circle** (⚫) - Not started
- **Orange Circle** (🟠) - Current/In Progress
- **Green Circle** (🟢) - Completed
- **Connecting Lines** - Show progress flow

### Timer Display:
- **Color**: Orange (#ff6f61)
- **Format**: "Estimated time: M:SS"
- **Bold text** for visibility
- **Auto-updating** every second

### Success Message (Delivered):
- **Green gradient background**
- **Large checkmark icon** ✓
- **Celebration message**
- **Thank you note**

## 🧪 Testing the Features

### Test Scenario 1: Place and Track Order
1. Go to "Order" section
2. Add items to cart
3. Fill customer details
4. Click "Place Order"
5. Note the Order ID from the popup
6. Click "Track Order" in sidebar
7. Enter the Order ID
8. Watch the 5-minute countdown timer

### Test Scenario 2: Rider Confirmation
1. Open [rider_dashboard.php](rider_dashboard.php)
2. See the pending order
3. Enter rider name
4. Click "Confirm Pickup"
5. Go back to tracking page
6. See status update with timer for delivery

### Test Scenario 3: Admin Quick Access
1. Login to admin dashboard
2. Click "Rider Dashboard" button
3. New tab opens with rider interface
4. Click "Track Order" button
5. New tab opens with tracking interface

## 📊 Performance

- **Timer Accuracy**: Updates every 1 second
- **Auto-refresh**: Only when timer expires
- **Resource Efficient**: Clears intervals properly
- **No Memory Leaks**: Proper cleanup on page navigation

## 🎯 Next Steps (Optional Enhancements)

1. **Push Notifications**: Alert customers when status changes
2. **SMS Updates**: Send text messages for status updates
3. **Real-time Updates**: Use WebSockets for instant updates
4. **Map Tracking**: Show rider location on a map
5. **Estimated Delivery Time**: Calculate based on distance
6. **Order Rating**: Allow customers to rate delivery

---

**Note**: The 5-minute timer is configurable. For production, you might want to set realistic times:
- Confirmation: 10-15 minutes
- Delivery: 30-45 minutes

Change the minutes parameter in `startPhaseCountdown()` to adjust!
