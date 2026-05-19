
// Dashboard

document.addEventListener('DOMContentLoaded', () => {
    fetchDashboardData();
});

function fetchDashboardData() {
    // Fetch all data in parallel
    Promise.all([
        fetch(ApiBaseUrl + '/products/get_products.php', { credentials: 'include' }).then(r => r.json()),
        fetch(ApiBaseUrl + '/orders/list.php', { credentials: 'include' }).then(r => r.json()),
        fetch(ApiBaseUrl + '/clients/list.php', { credentials: 'include' }).then(r => r.json())
    ])
        .then(([productsData, ordersData, clientsData]) => {
            updateKPIs(productsData, ordersData, clientsData);
            updateRecentOrders(ordersData);
        })
        .catch(error => console.error('Error fetching dashboard data:', error));
}

function updateKPIs(productsData, ordersData, clientsData) {
    // Total Products
    if (productsData.success) {
        document.querySelector('.kpi:nth-child(1) .kpi__value').textContent = productsData.data.length;
        // Delta is hardcoded or needs historical data which we don't have easily
        document.querySelector('.kpi:nth-child(1) .kpi__delta strong').textContent = 'Current';
    }

    // Orders Today & Revenue
    if (ordersData.success) {
        const orders = ordersData.data;
        const today = new Date().toISOString().split('T')[0];
        const currentMonth = new Date().getMonth();

        const ordersToday = orders.filter(o => o.created_at.startsWith(today)).length;
        document.querySelector('.kpi:nth-child(2) .kpi__value').textContent = ordersToday;
        document.querySelector('.kpi:nth-child(2) .kpi__delta strong').textContent = 'Today';

        const revenueMonth = orders
            .filter(o => new Date(o.created_at).getMonth() === currentMonth && o.status !== 'canceled')
            .reduce((sum, o) => sum + parseFloat(o.total), 0);

        document.querySelector('.kpi:nth-child(3) .kpi__value').textContent = '$' + revenueMonth.toFixed(2);
        document.querySelector('.kpi:nth-child(3) .kpi__delta strong').textContent = 'This Month';
    }

    // Clients
    if (clientsData.success) {
        document.querySelector('.kpi:nth-child(4) .kpi__value').textContent = clientsData.data.length;
        document.querySelector('.kpi:nth-child(4) .kpi__delta strong').textContent = 'Total';
    }
}

function updateRecentOrders(ordersData) {
    if (!ordersData.success) return;

    const tbody = document.querySelector('.data-table tbody');
    if (!tbody) return;

    tbody.innerHTML = '';

    const recentOrders = ordersData.data.slice(0, 5); // Top 5

    if (recentOrders.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="empty-state">No recent orders.</td>
            </tr>
        `;
        return;
    }

    recentOrders.forEach(o => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="mono">#${o.id}</td>
            <td>
                <div class="customer__name">${o.customer || 'Guest'}</div>
            </td>
            <td>${o.items_count}</td>
            <td>$${parseFloat(o.total).toFixed(2)}</td>
            <td>
                <span class="badge badge--${o.status}">
                    ${o.status.charAt(0).toUpperCase() + o.status.slice(1)}
                </span>
            </td>
            <td>${o.created_at}</td>
        `;
        tbody.appendChild(tr);
    });
}
