
// Category & Brand Management

document.addEventListener('DOMContentLoaded', () => {
    fetchCategories();
    fetchBrands();
});

// --- Categories ---

function fetchCategories() {
    fetch(ApiBaseUrl + '/categories/index.php', {
        method: 'GET',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderCategoriesTable(data.data);
            } else {
                console.error('Failed to fetch categories:', data.error);
            }
        })
        .catch(error => console.error('Error fetching categories:', error));
}

function renderCategoriesTable(categoriesData) {
    window.categories = categoriesData;

    const tbody = document.querySelector('.section-card:first-child .data-table tbody');
    if (!tbody) return;

    tbody.innerHTML = '';

    if (categoriesData.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="3" class="empty-state">
                    No categories found. Click "Add Category" to get started.
                </td>
            </tr>
        `;
        return;
    }

    categoriesData.forEach(category => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${category.id}</td>
            <td>${category.name}</td>
            <td>
                <div class="actions">
                    <button class="btn btn-secondary btn-small" 
                            onclick="openEditCategoryModal(${category.id})">
                        Edit
                    </button>
                    <button class="btn btn-danger btn-small" 
                            onclick="confirmDeleteCategory(${category.id})">
                        Delete
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function openAddCategoryModal() {
    document.getElementById('categoryModalTitle').textContent = 'Add New Category';
    document.getElementById('categoryForm').reset();
    document.getElementById('categoryId').value = '';
    document.getElementById('categoryModal').classList.add('show');
}

function openEditCategoryModal(categoryId) {
    const category = window.categories.find(c => c.id == categoryId);
    if (!category) return;

    document.getElementById('categoryModalTitle').textContent = 'Edit Category';
    document.getElementById('categoryForm').reset();
    document.getElementById('categoryId').value = category.id;
    document.getElementById('categoryName').value = category.name;
    document.getElementById('categoryModal').classList.add('show');
}

function closeCategoryModal() {
    document.getElementById('categoryModal').classList.remove('show');
}

function handleCategorySubmit(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const id = formData.get('id');
    const name = formData.get('name');

    const url = id ? ApiBaseUrl + '/categories/update.php' : ApiBaseUrl + '/categories/add.php';
    const data = { name: name };
    if (id) data.id = id;

    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data),
        credentials: 'include'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeCategoryModal();
                fetchCategories();
            } else {
                alert('Failed to save category: ' + data.error);
            }
        })
        .catch(error => console.error('Error saving category:', error));
}

function confirmDeleteCategory(categoryId) {
    const category = window.categories.find(c => c.id == categoryId);
    if (!category) return;

    document.getElementById('deleteCategoryId').value = category.id;
    document.getElementById('deleteCategoryName').textContent = category.name;
    document.getElementById('deleteCategoryModal').classList.add('show');
}

function closeDeleteCategoryModal() {
    document.getElementById('deleteCategoryModal').classList.remove('show');
}

function deleteCategory() {
    const id = document.getElementById('deleteCategoryId').value;

    fetch(ApiBaseUrl + '/categories/delete.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id }),
        credentials: 'include'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeDeleteCategoryModal();
                fetchCategories();
            } else {
                alert('Failed to delete category: ' + data.error);
            }
        })
        .catch(error => console.error('Error deleting category:', error));
}


// --- Brands ---

function fetchBrands() {
    fetch(ApiBaseUrl + '/brands/index.php', {
        method: 'GET',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderBrandsTable(data.data);
            } else {
                console.error('Failed to fetch brands:', data.error);
            }
        })
        .catch(error => console.error('Error fetching brands:', error));
}

function renderBrandsTable(brandsData) {
    window.brands = brandsData;

    const tbody = document.querySelector('.section-card:last-child .data-table tbody');
    if (!tbody) return;

    tbody.innerHTML = '';

    if (brandsData.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="3" class="empty-state">
                    No brands found. Click "Add Brand" to get started.
                </td>
            </tr>
        `;
        return;
    }

    brandsData.forEach(brand => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${brand.id}</td>
            <td>${brand.name}</td>
            <td>
                <div class="actions">
                    <button class="btn btn-secondary btn-small" 
                            onclick="openEditBrandModal(${brand.id})">
                        Edit
                    </button>
                    <button class="btn btn-danger btn-small" 
                            onclick="confirmDeleteBrand(${brand.id})">
                        Delete
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function openAddBrandModal() {
    document.getElementById('brandModalTitle').textContent = 'Add New Brand';
    document.getElementById('brandForm').reset();
    document.getElementById('brandId').value = '';
    document.getElementById('brandModal').classList.add('show');
}

function openEditBrandModal(brandId) {
    const brand = window.brands.find(b => b.id == brandId);
    if (!brand) return;

    document.getElementById('brandModalTitle').textContent = 'Edit Brand';
    document.getElementById('brandForm').reset();
    document.getElementById('brandId').value = brand.id;
    document.getElementById('brandName').value = brand.name;
    document.getElementById('brandModal').classList.add('show');
}

function closeBrandModal() {
    document.getElementById('brandModal').classList.remove('show');
}

function handleBrandSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const id = formData.get('id');
    const name = formData.get('name');

    const url = id ? ApiBaseUrl + '/brands/update.php' : ApiBaseUrl + '/brands/add.php';
    const data = { name: name };
    if (id) data.id = id;

    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data),
        credentials: 'include'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeBrandModal();
                fetchBrands();
            } else {
                alert('Failed to save brand: ' + data.error);
            }
        })
        .catch(error => console.error('Error saving brand:', error));
}

function confirmDeleteBrand(brandId) {
    const brand = window.brands.find(b => b.id == brandId);
    if (!brand) return;

    document.getElementById('deleteBrandId').value = brand.id;
    document.getElementById('deleteBrandName').textContent = brand.name;
    document.getElementById('deleteBrandModal').classList.add('show');
}

function closeDeleteBrandModal() {
    document.getElementById('deleteBrandModal').classList.remove('show');
}

function deleteBrand() {
    const id = document.getElementById('deleteBrandId').value;

    fetch(ApiBaseUrl + '/brands/delete.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id }),
        credentials: 'include'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeDeleteBrandModal();
                fetchBrands();
            } else {
                alert('Failed to delete brand: ' + data.error);
            }
        })
        .catch(error => console.error('Error deleting brand:', error));
}
