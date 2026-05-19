<?php
require_once __DIR__ . '/auth_check.php';
include_once __DIR__ . '/../config/conf.php';

// Check admin authentication and get user data
$adminUser = getAdminUser();

$formatMoney = function ($v) {
  return number_format((float)$v, 2) . ' $';
};
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Admin</title>
  <link rel="stylesheet" href="<?php echo $baseUrl . '/assets/css/admin_navbar.css'; ?>">
  <link rel="stylesheet" href="<?php echo $baseUrl . '/assets/css/order_mg.css'; ?>">
  <link rel="stylesheet" href="<?php echo $baseUrl . '/assets/css/dashboard.css'; ?>">
</head>
<body>

<?php include __DIR__ . '/../components/admin_navbar.php'; ?>

<div class="container">
  <div class="header">
    <div>
      <h1>Dashboard</h1>
      <p class="subtitle">Quick overview of your store activity.</p>
    </div>
  </div>

  <div class="dashboard-grid">
      <div class="kpi">
        <div class="kpi__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </div>
        <div>
          <div class="kpi__label">Total products</div>
          <div class="kpi__value">-</div>
          <div class="kpi__delta"><strong>-</strong></div>
        </div>
      </div>
      <div class="kpi">
        <div class="kpi__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24"><path d="M7 6h10l1 14H6L7 6Zm2-2h6v2H9V4Z" fill="currentColor"/></svg>
        </div>
        <div>
          <div class="kpi__label">Orders today</div>
          <div class="kpi__value">-</div>
          <div class="kpi__delta"><strong>-</strong></div>
        </div>
      </div>
      <div class="kpi">
        <div class="kpi__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 10 10A10.01 10.01 0 0 0 12 2Zm1 17.93V20h-2v-.07A8.03 8.03 0 0 1 4.07 13H4v-2h.07A8.03 8.03 0 0 1 11 4.07V4h2v.07A8.03 8.03 0 0 1 19.93 11H20v2h-.07A8.03 8.03 0 0 1 13 19.93Z" fill="currentColor"/></svg>
        </div>
        <div>
          <div class="kpi__label">Revenue (month)</div>
          <div class="kpi__value">-</div>
          <div class="kpi__delta"><strong>-</strong></div>
        </div>
      </div>
      <div class="kpi">
        <div class="kpi__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24"><path d="M12 12.5a4 4 0 1 0-4-4 4 4 0 0 0 4 4m-6.5 6.25a.75.75 0 0 1 .44-.68A11 11 0 0 1 12 17a11 11 0 0 1 6.06 1.07.75.75 0 0 1 .44.68v1.2a.75.75 0 0 1-.75.75H6.25a.75.75 0 0 1-.75-.75z" fill="currentColor"/></svg>
        </div>
        <div>
          <div class="kpi__label">Clients</div>
          <div class="kpi__value">-</div>
          <div class="kpi__delta"><strong>-</strong></div>
        </div>
      </div>
  </div>

  <div class="dashboard-panels">
    <div class="panel-card">
      <div class="panel-card__header">
        <h2>Recent orders</h2>
        <a class="btn btn-small btn-secondary" href="<?php echo htmlspecialchars($baseUrl . '/admin/order_mg.php'); ?>">Open Orders</a>
      </div>
      <div class="table-card">
        <table class="data-table">
          <thead>
            <tr>
              <th>Order</th>
              <th>Customer</th>
              <th>Items</th>
              <th>Total</th>
              <th>Status</th>
              <th>Created</th>
            </tr>
          </thead>
          <tbody>
            <!-- Populated by JS -->
          </tbody>
        </table>
      </div>
    </div>

    <div class="panel-card">
      <div class="panel-card__header">
        <h2>Quick actions</h2>
        <span></span>
      </div>
      <div class="quick-actions">
        <a href="<?php echo htmlspecialchars($baseUrl . '/admin/product_mg.php'); ?>">Products <span>Manage catalog</span></a>
        <a href="<?php echo htmlspecialchars($baseUrl . '/admin/catbrand_mg.php'); ?>">Categories & Brands <span>Organize</span></a>
        <a href="<?php echo htmlspecialchars($baseUrl . '/admin/order_mg.php'); ?>">Orders <span>Update status</span></a>
        <a href="<?php echo htmlspecialchars($baseUrl . '/admin/client_mg.php'); ?>">Clients <span>Manage customers</span></a>
      </div>
    </div>
  </div>
</div>

<script><?php echo "let ApiBaseUrl = '" . $apiBaseUrl . "';"; ?></script>
<script><?php echo "const baseUrl = '" . $baseUrl . "';"; ?></script>

<script src="<?php echo $baseUrl . '/assets/js/dashboard.js'; ?>"></script>
<script src="<?php echo $baseUrl . '/assets/js/admin_navbar.js'; ?>"></script>
</body>
</html>
