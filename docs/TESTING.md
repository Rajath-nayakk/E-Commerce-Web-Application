# 🧪 NovaTech Gear — Comprehensive Manual & Automated Test Checklist

This document contains the step-by-step verification checklist for evaluating every user and administrative workflow in the **NovaTech Gear E-Commerce Application**.

---

## 📋 Test Matrix Overview

| Module | Test Flow | Expected Result | Status |
| :--- | :--- | :--- | :---: |
| **Auth** | User Registration | New customer record created; auto-logged in with welcome toast. | ✅ Passed |
| **Auth** | User Login & Session | Authenticates customer; regenerates session ID; merges cart. | ✅ Passed |
| **Auth** | Rate Limiting | 5 failed attempts locks login for 15 minutes with alert. | ✅ Passed |
| **Auth** | Forgot / Reset Password | Secure token generated; password updated; old token invalidated. | ✅ Passed |
| **Catalog** | Browsing & Search | Keyword search matches name & description; pagination calculates correctly. | ✅ Passed |
| **Catalog** | Category Filter | Filtering by department displays only active items in category. | ✅ Passed |
| **Catalog** | Product Detail | Shows images, price, stock status, specs, quantity selector. | ✅ Passed |
| **Cart** | Guest Cart | Add/update/remove items stored in session; subtotal calculated. | ✅ Passed |
| **Cart** | Cart Merge on Login | Guest session items seamlessly transferred to database on sign in. | ✅ Passed |
| **Checkout** | Cash on Delivery (COD) | Stock deducted in DB transaction; order created with 'pending' status. | ✅ Passed |
| **Checkout** | Mock Card Payment (Approved) | Valid card credentials simulate instant payment success. | ✅ Passed |
| **Checkout** | Mock Card Payment (Declined) | Card ending in `0000` triggers decline simulation with error alert. | ✅ Passed |
| **Checkout** | ACID Inventory Protection | Prevents checkout if product inventory is insufficient; rolls back cleanly. | ✅ Passed |
| **Tracking** | Customer Status Timeline | Visual timeline shows 4 stages (Pending ➔ Processing ➔ Shipped ➔ Delivered). | ✅ Passed |
| **Admin** | Dashboard Overview | Total revenue, orders, products, users, low stock warnings displayed. | ✅ Passed |
| **Admin** | Product Management | Create new product with image upload; edit specs; deactivate/delete. | ✅ Passed |
| **Admin** | Category Management | Add category with auto-generated slug; edit name; delete unused. | ✅ Passed |
| **Admin** | Order Status Updates | Admin advances status with tracking note; customer timeline reflects it. | ✅ Passed |
| **Admin** | Order Cancellation | Cancelling order replenishes product stock automatically. | ✅ Passed |
| **Admin** | User Directory | Lists registered customers with total orders and lifetime spend. | ✅ Passed |
| **APIs** | REST Endpoints (`/api`) | Products, Cart, Orders, Auth return valid JSON and status codes. | ✅ Passed |

---

## 🛠️ Step-by-Step Manual Browser Verification Walkthrough

### Test Case 1: Guest Browsing & Adding to Cart
1. Open `http://localhost:8000/` in an incognito window as a guest.
2. Confirm the hero banner, category pills, and 8 featured products render.
3. Click on **Catalog** in the top navigation.
4. In the search box, enter `Monitor` and press Search. Confirm only monitors appear.
5. Filter by category **Audio & Headphones**. Confirm audio products display.
6. Click on **AcousticPro Wireless ANC Headphones**.
7. Adjust quantity to `2` and click **Add to Cart**.
8. Notice the live green toast notification and the navbar cart badge updating to `2`.
9. Navigate to `/cart`. Verify the line item subtotal and grand total.

---

### Test Case 2: User Registration & Cart Merging
1. From the `/cart` page, click **Proceed to Checkout**.
2. Notice the application protects checkout by redirecting to `/login` with a message.
3. Click **Create an Account** (`/register`).
4. Enter:
   - Name: `Alex Tester`
   - Email: `alex.tester@store.com`
   - Password: `Password@123`
   - Confirm: `Password@123`
5. Click **Create Free Account**.
6. Confirm user is logged in automatically and redirected to the home page with a welcome toast.
7. Click the **Cart** icon.
8. **Verify Cart Merge**: Notice that the 2 units of **AcousticPro Headphones** added as a guest are now preserved in the database cart!

---

### Test Case 3: Checkout with Mock Card Payment Simulator
1. From `/cart`, click **Proceed to Checkout** (`/checkout`).
2. Recipient name should be pre-filled with `Alex Tester`.
3. Enter shipping address: `100 Innovation Way, Suite 400, Tech City, CA 94016`.
4. Enter phone: `+1 (555) 345-6789`.
5. Under Payment Method, select **Credit / Debit Card (Mock Gateway)**.
6. Test **Declined Simulation**:
   - Card Number: `4242 4242 4242 0000` (ends in 0000)
   - Expiry: `12/28`, CVV: `123`
   - Click **Place Order**.
   - Verify: Error alert appears: *"Payment Declined (Mock Simulator): Card ending in 0000 was rejected..."*
7. Test **Successful Payment**:
   - Change card number to `4242 4242 4242 4242`.
   - Click **Place Order**.
8. Confirm immediate redirect to `/orders/confirmation/{id}` with Order Confirmed badge, line items, and recipient address.

---

### Test Case 4: Order Tracking & Live Timeline Reflection
1. From the confirmation page, click **Track Order Timeline** (or navigate to `/orders`).
2. Verify:
   - Stage 1 **Order Placed** is marked active with timestamp.
   - Audit event history logs: *"Order placed successfully via CARD."*
3. Keep this tab open.

---

### Test Case 5: Admin Panel & Status Advancing
1. Open a new browser tab or window.
2. Sign in as Administrator (`/login`):
   - Email: `admin@store.com`
   - Password: `Admin@123`
   - *(Or click the 1-Click "Admin" demo button).*
3. Confirm redirect to `/admin` dashboard. Verify sales revenue has updated.
4. Click **Orders** in the admin sidebar (`/admin/orders`).
5. Click **Manage** on Alex Tester's recent order.
6. Change status to **Processing** and enter note: `Packaged in anti-static enclosure`. Click **Save & Transition Status**.
7. Change status to **Shipped** and enter note: `Handed to FedEx tracking #FDX-982187`. Click **Save & Transition Status**.
8. Switch back to the customer's tab and refresh `/orders/{id}`:
   - Confirm the progress bar fills to **Shipped**.
   - Confirm FedEx tracking note is visible in the customer's journey audit trail!

---

### Test Case 6: Product CRUD with Image Upload
1. In Admin panel, click **Products** (`/admin/products`) ➔ **Add Product**.
2. Fill:
   - Name: `NovaWave Ergonomic Mechanical Numpad`
   - Category: `Computer Accessories`
   - Price: `49.99`
   - Stock: `30`
   - Description: `Wireless mechanical numpad with programmable macro keys and hot-swappable switches.`
   - Image: Upload any test JPG/PNG/WEBP image (or test with invalid file to see file-validation security).
3. Click **Save Product**.
4. Confirm product appears in the admin product table and on the live customer storefront (`/products`)!

---

### Test Case 7: Order Cancellation & Inventory Restoration
1. As Admin, navigate to `/admin/orders/{id}`.
2. Select status **Cancelled** with note: `Customer requested cancellation before courier dispatch`.
3. Click **Save & Transition Status**.
4. Navigate to `/admin/products`.
5. Confirm inventory stock for the ordered items has been automatically replenished back into available stock!

---

## ⚙️ Automated Test Suite Verification

Run all test scripts in sequence via terminal:

```bash
# 1. Test Authentication, Hashing & Password Resets
php tests/test_auth.php

# 2. Test Catalog, Multi-Facet Filters & Search
php tests/test_catalog.php

# 3. Test Shopping Cart & Guest-to-User Merging
php tests/test_cart.php

# 4. Test Checkout, Transactions & Status Timeline
php tests/test_orders.php

# 5. Test Admin Dashboard, Products, Categories & Users
php tests/test_admin.php

# 6. Test REST JSON APIs
php tests/test_api.php
```

All 6 test suites execute with zero failures.
