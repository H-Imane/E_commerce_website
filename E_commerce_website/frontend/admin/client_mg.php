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
  <title>Client Management - Admin</title>
  <link rel="stylesheet" href="<?php echo $baseUrl . '/assets/css/admin_navbar.css'; ?>">
  <link rel="stylesheet" href="<?php echo $baseUrl . '/assets/css/order_mg.css'; ?>">
  <link rel="stylesheet" href="<?php echo $baseUrl . '/assets/css/client_mg.css'; ?>">
</head>
<body>

<?php include __DIR__ . '/../components/admin_navbar.php'; ?>

<div class="container">
  <div class="header">
    <div>
      <h1>Client Management</h1>
      <p class="subtitle">Manage customers, review details, and update account status.</p>
    </div>
    <div class="header-actions">
      <button class="btn btn-secondary" type="button" id="exportClientsBtn" onclick="exportClientsCSV()">Export (CSV)</button>
    </div>
  </div>

  <div class="toolbar">
    <div class="toolbar__left">
      <div class="search">
        <input id="clientSearch" type="search" placeholder="Search by name, email, phone, city...">
      </div>
      <div class="filter">
        <select id="statusFilter">
          <option value="all">All statuses</option>
          <option value="active">Active</option>
          <option value="blocked">Blocked</option>
        </select>
      </div>
    </div>
    <div class="toolbar__right">
      <div class="stats" id="clientsCount"></div>
    </div>
  </div>

  <div class="table-card">
    <table class="data-table" id="clientsTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>Client</th>
          <th>Contact</th>
          <th>City</th>
          <th>Orders</th>
          <th>Total spent</th>
          <th>Status</th>
          <th>Created</th>
          <th style="text-align:right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <!-- Populated by JS -->
      </tbody>
    </table>
  </div>
</div>

<div class="modal" id="clientModal" aria-hidden="true">
  <div class="modal__overlay" onclick="closeClientModal()"></div>
  <div class="modal__content" role="dialog" aria-modal="true" aria-labelledby="clientModalTitle">
    <div class="modal__header">
      <h2 id="clientModalTitle">Client details</h2>
      <button class="modal__close" type="button" onclick="closeClientModal()" aria-label="Close">×</button>
    </div>
    <div class="modal__body">
      <div class="detail-grid">
        <div class="detail">
          <div class="detail__label">ID</div>
          <div class="detail__value" id="mId">-</div>
        </div>
        <div class="detail">
          <div class="detail__label">Name</div>
          <div class="detail__value" id="mName">-</div>
        </div>
        <div class="detail">
          <div class="detail__label">Email</div>
          <div class="detail__value" id="mEmail">-</div>
        </div>
        <div class="detail">
          <div class="detail__label">Phone</div>
          <div class="detail__value" id="mPhone">-</div>
        </div>
        <div class="detail">
          <div class="detail__label">City</div>
          <div class="detail__value" id="mCity">-</div>
        </div>
        <div class="detail">
          <div class="detail__label">Status</div>
          <div class="detail__value" id="mStatus">-</div>
        </div>
        <div class="detail">
          <div class="detail__label">Orders</div>
          <div class="detail__value" id="mOrders">-</div>
        </div>
        <div class="detail">
          <div class="detail__label">Total spent</div>
          <div class="detail__value" id="mSpent">-</div>
        </div>
        <div class="detail">
          <div class="detail__label">Created</div>
          <div class="detail__value" id="mCreated">-</div>
        </div>
      </div>
    </div>
    <div class="modal__footer">
      <button class="btn btn-secondary" type="button" onclick="closeClientModal()">Close</button>
    </div>
  </div>
</div>

<script src="<?php echo $baseUrl . '/assets/js/admin_navbar.js'; ?>"></script>
<script><?php echo "let ApiBaseUrl = '" . $apiBaseUrl . "';"; ?></script>
<script><?php echo "const baseUrl = '" . $baseUrl . "';"; ?></script>
<script src="<?php echo $baseUrl . '/assets/js/client_mg.js'; ?>"></script>
</body>
</html>
