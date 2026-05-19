
// Client Management

document.addEventListener('DOMContentLoaded', () => {
  fetchClients();
});

function fetchClients() {
  fetch(ApiBaseUrl + '/clients/list.php', {
    method: 'GET',
    headers: { 'Content-Type': 'application/json' },
    credentials: 'include'
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        renderClientsTable(data.data);
        updateStats(data.data.length);
      } else {
        console.error('Failed to fetch clients:', data.error);
      }
    })
    .catch(error => console.error('Error fetching clients:', error));
}

function renderClientsTable(clientsData) {
  window.clients = clientsData;

  const tbody = document.getElementById('clientsTable').querySelector('tbody');
  if (!tbody) return;

  tbody.innerHTML = '';

  if (clientsData.length === 0) {
    tbody.innerHTML = `
            <tr>
                <td colspan="9" class="empty-state">No clients found.</td>
            </tr>
        `;
    return;
  }

  clientsData.forEach(client => {
    const tr = document.createElement('tr');
    // Note: Some fields like phone, city, orders_count, total_spent are not in the current API response
    // We will display placeholders or available data

    const initial = client.name ? client.name.charAt(0).toUpperCase() : '?';

    tr.innerHTML = `
            <td>#${client.id}</td>
            <td>
                <div class="client">
                    <div class="client__avatar" aria-hidden="true">${initial}</div>
                    <div class="client__meta">
                        <div class="client__name">${client.name}</div>
                        <div class="client__email">${client.email}</div>
                    </div>
                </div>
            </td>
            <td>
                <div class="contact">
                    <div class="contact__line">-</div>
                    <div class="contact__line contact__muted">${client.email}</div>
                </div>
            </td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>
                <span class="badge badge--success" data-badge>Active</span>
            </td>
            <td>${client.created_at}</td>
            <td style="text-align:right;">
                <div class="row-actions">
                    <button class="btn btn-small btn-secondary" type="button" onclick="viewClient(${client.id})">View</button>
                    <button class="btn btn-small btn-danger" type="button" onclick="confirmDeleteClient(${client.id})">Delete</button>
                </div>
            </td>
        `;
    tbody.appendChild(tr);
  });
}

function updateStats(count) {
  const statsEl = document.getElementById('clientsCount');
  if (statsEl) {
    statsEl.textContent = `${count} clients`;
  }
}

function viewClient(clientId) {
  const client = window.clients.find(c => c.id == clientId);
  if (!client) return;

  document.getElementById('mId').textContent = '#' + client.id;
  document.getElementById('mName').textContent = client.name;
  document.getElementById('mEmail').textContent = client.email;
  document.getElementById('mCreated').textContent = client.created_at;

  // Reset others
  document.getElementById('mPhone').textContent = '-';
  document.getElementById('mCity').textContent = '-';
  document.getElementById('mStatus').textContent = 'Active';
  document.getElementById('mOrders').textContent = '-';
  document.getElementById('mSpent').textContent = '-';

  document.getElementById('clientModal').classList.add('active');
  document.querySelector('.modal__overlay').classList.add('active');
}

function closeClientModal() {
  document.getElementById('clientModal').classList.remove('active');
  document.querySelector('.modal__overlay').classList.remove('active');
}

function exportClientsCSV() {
  if (!window.clients || window.clients.length === 0) {
    alert('No clients to export');
    return;
  }

  const headers = ['ID', 'Name', 'Email', 'Created At'];
  const rows = window.clients.map(c => [c.id, c.name, c.email, c.created_at]);

  let csvContent = "data:text/csv;charset=utf-8,"
    + headers.join(",") + "\n"
    + rows.map(e => e.join(",")).join("\n");

  const encodedUri = encodeURI(csvContent);
  const link = document.createElement("a");
  link.setAttribute("href", encodedUri);
  link.setAttribute("download", "clients_export.csv");
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

function confirmDeleteClient(clientId) {
  const client = window.clients.find(c => c.id == clientId);
  if (!client) return;

  if (confirm(`Are you sure you want to delete client "${client.name}"?`)) {
    deleteClient(client.id);
  }
}

function deleteClient(id) {
  fetch(ApiBaseUrl + '/clients/delete.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id: id }),
    credentials: 'include'
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        fetchClients();
      } else {
        alert('Failed to delete client: ' + data.error);
      }
    })
    .catch(error => console.error('Error deleting client:', error));
}
