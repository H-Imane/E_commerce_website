<?php
require_once __DIR__ . '/auth_check.php';
include_once __DIR__ . '/../config/conf.php';

// Check admin authentication and get user data
$adminUser = getAdminUser();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - Admin</title>
    <link rel="stylesheet" href="<?php echo $baseUrl . '/assets/css/admin_navbar.css'; ?>">
    <link rel="stylesheet" href="<?php echo $baseUrl . '/assets/css/order_mg.css'; ?>">
</head>
<body>

<?php include __DIR__ . '/../components/admin_navbar.php'; ?>

<div class="container">
    <div class="header">
        <div>
            <h1>Order Management</h1>
            <p class="subtitle">Manage customer orders and update their status.</p>
        </div>
        <div class="header-actions">
            <button class="btn btn-secondary" type="button" id="exportBtn" onclick="exportOrdersCSV()">Export (CSV)</button>
        </div>
    </div>

    <div class="toolbar">
        <div class="toolbar__left">
            <div class="search">
                <input type="text" id="searchInput" placeholder="Search by order ID, customer, email...">
            </div>
            <div class="filter">
                <select id="statusFilter">
                    <option value="">All statuses</option>
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="shipped">Shipped</option>
                    <option value="delivered">Delivered</option>
                    <option value="canceled">Canceled</option>
                </select>
            </div>
        </div>
        <div class="toolbar__right">
            <span class="count" id="ordersCount"></span>
        </div>
    </div>

    <div class="table-card">
        <table class="data-table" id="ordersTable">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Populated by JS -->
            </tbody>
        </table>
    </div>
</div>

<div class="modal" id="orderModal" aria-hidden="true">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="orderModalTitle">Order Details</h2>
            <button class="close-btn" type="button" aria-label="Close" id="closeOrderModal" onclick="closeOrderModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="grid">
                <div class="panel">
                    <h3>Customer</h3>
                    <div class="kv"><span>Name</span><strong id="mCustomer"></strong></div>
                    <div class="kv"><span>Email</span><strong id="mEmail"></strong></div>
                    <div class="kv"><span>Phone</span><strong id="mPhone"></strong></div>
                    <div class="kv"><span>Address</span><strong id="mAddress"></strong></div>
                </div>
                <div class="panel">
                    <h3>Order</h3>
                    <div class="kv"><span>Order ID</span><strong class="mono" id="mOrderId"></strong></div>
                    <div class="kv"><span>Created</span><strong class="mono" id="mCreated"></strong></div>
                    <div class="kv"><span>Status</span><strong id="mStatus"></strong></div>
                    <div class="kv"><span>Total</span><strong class="mono" id="mTotal"></strong></div>
                </div>
            </div>

            <div class="items">
                <h3>Items</h3>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Unit price</th>
                            <th>Line total</th>
                        </tr>
                    </thead>
                    <tbody id="mItems"></tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <div class="status-update">
                <select id="mStatusSelect">
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="shipped">Shipped</option>
                    <option value="delivered">Delivered</option>
                    <option value="canceled">Canceled</option>
                </select>
                <button class="btn btn-primary" type="button" id="saveStatusBtn" onclick="updateOrderStatus()">Save status</button>
            </div>
            <button class="btn btn-secondary" type="button" id="closeOrderModal2" onclick="closeOrderModal()">Close</button>
        </div>
    </div>
</div>

<script><?php echo "let ApiBaseUrl = '" . $apiBaseUrl . "';"; ?></script>
<script><?php echo "const baseUrl = '" . $baseUrl . "';"; ?></script>
<script src="<?php echo $baseUrl . '/assets/js/order_mg.js'; ?>"></script>
<script src="<?php echo $baseUrl . '/assets/js/admin_navbar.js'; ?>"></script>
</body>
</html>
