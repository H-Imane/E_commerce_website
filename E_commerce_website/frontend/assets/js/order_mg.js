
// Order Management

let currentOrderId = null;

document.addEventListener('DOMContentLoaded', () => {
  fetchOrders();
});

function fetchOrders() {
  fetch(ApiBaseUrl + '/orders/list.php', {
    method: 'GET',
    headers: { 'Content-Type': 'application/json' },
    credentials: 'include'
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        renderOrdersTable(data.data);
        updateStats(data.data.length);
      } else {
        console.error('Failed to fetch orders:', data.error);
      }
    })
    .catch(error => console.error('Error fetching orders:', error));
}

function renderOrdersTable(ordersData) {
  window.orders = ordersData;

  const tbody = document.getElementById('ordersTable').querySelector('tbody');
  if (!tbody) return;

  tbody.innerHTML = '';

  if (ordersData.length === 0) {
    tbody.innerHTML = `
            <tr>
                <td colspan="7" class="empty-state">No orders found.</td>
            </tr>
        `;
    return;
  }

  ordersData.forEach(order => {
    const tr = document.createElement('tr');
    tr.className = 'order-row';

    const statusClass = `badge--${order.status}`;

    tr.innerHTML = `
            <td class="mono">#${order.id}</td>
            <td>
                <div class="customer">
                    <div class="customer__name">${order.customer || 'Guest'}</div>
                    <div class="customer__meta">${order.email || '-'}</div>
                </div>
            </td>
            <td>${order.items_count}</td>
            <td class="mono">$${parseFloat(order.total).toFixed(2)}</td>
            <td>
                <span class="badge ${statusClass}">
                    ${order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                </span>
            </td>
            <td class="mono">${order.created_at}</td>
            <td>
                <div class="actions">
                    <button class="btn btn-secondary btn-small" type="button" onclick="openOrderModal(${order.id})">View</button>
                    <button class="btn btn-primary btn-small" type="button" onclick="openOrderModal(${order.id})">Update status</button>
                </div>
            </td>
        `;
    tbody.appendChild(tr);
  });
}

function updateStats(count) {
  const statsEl = document.getElementById('ordersCount');
  if (statsEl) {
    statsEl.textContent = `${count} orders`;
  }
}

function openOrderModal(orderId) {
  const order = window.orders.find(o => o.id == orderId);
  if (!order) return;

  currentOrderId = order.id;

  document.getElementById('mOrderId').textContent = '#' + order.id;
  document.getElementById('mCustomer').textContent = order.customer || 'Guest';
  document.getElementById('mEmail').textContent = order.email || '-';
  document.getElementById('mCreated').textContent = order.created_at;
  document.getElementById('mStatus').textContent = order.status;
  document.getElementById('mTotal').textContent = '$' + parseFloat(order.total).toFixed(2);

  // Reset others
  document.getElementById('mPhone').textContent = '-';
  document.getElementById('mAddress').textContent = '-';

  // Populate items
  const itemsTbody = document.getElementById('mItems');
  itemsTbody.innerHTML = '';

  if (order.items && order.items.length > 0) {
    order.items.forEach(item => {
      const tr = document.createElement('tr');
      const lineTotal = item.qty * item.unit_price;
      tr.innerHTML = `
                <td>${item.name}</td>
                <td>${item.qty}</td>
                <td>$${parseFloat(item.unit_price).toFixed(2)}</td>
                <td>$${lineTotal.toFixed(2)}</td>
            `;
      itemsTbody.appendChild(tr);
    });
  } else {
    itemsTbody.innerHTML = '<tr><td colspan="4">No items details available</td></tr>';
  }

  // Set current status in select
  const statusSelect = document.getElementById('mStatusSelect');
  if (statusSelect) {
    statusSelect.value = order.status;
  }

  document.getElementById('orderModal').classList.add('active');
  // Assuming modal CSS handles visibility with .active class or aria-hidden
  document.getElementById('orderModal').setAttribute('aria-hidden', 'false');
}

function closeOrderModal() {
  document.getElementById('orderModal').classList.remove('active');
  document.getElementById('orderModal').setAttribute('aria-hidden', 'true');
  currentOrderId = null;
}

function updateOrderStatus() {
  if (!currentOrderId) return;

  const status = document.getElementById('mStatusSelect').value;

  fetch(ApiBaseUrl + '/orders/update_status.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id: currentOrderId, status: status }),
    credentials: 'include'
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        closeOrderModal();
        fetchOrders();
      } else {
        alert('Failed to update status: ' + data.error);
      }
    })
    .catch(error => console.error('Error updating status:', error));
}

function exportOrdersCSV() {
  if (!window.orders || window.orders.length === 0) {
    alert('No orders to export');
    return;
  }

  const headers = ['ID', 'Customer', 'Email', 'Total', 'Status', 'Created At'];
  const rows = window.orders.map(o => [o.id, o.customer, o.email, o.total, o.status, o.created_at]);

  let csvContent = "data:text/csv;charset=utf-8,"
    + headers.join(",") + "\n"
    + rows.map(e => e.join(",")).join("\n");

  const encodedUri = encodeURI(csvContent);
  const link = document.createElement("a");
  link.setAttribute("href", encodedUri);
  link.setAttribute("download", "orders_export.csv");
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}
