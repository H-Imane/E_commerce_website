
// Product Management

let products = [];
let brands = [];
let categories = [];

document.addEventListener('DOMContentLoaded', () => {
    fetchProducts();
    fetchBrands();
    fetchCategories();
});

function fetchProducts() {
    fetch(ApiBaseUrl + '/products/get_products.php', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        },
        credentials: 'include'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                products = data.data;
                renderProductsTable();
            } else {
                console.error('Failed to fetch products:', data.error);
            }
        })
        .catch(error => console.error('Error fetching products:', error));
}

function fetchBrands() {
    fetch(ApiBaseUrl + '/brands/index.php', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        },
        credentials: 'include'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                brands = data.data;
                populateBrandSelect();
            }
        })
        .catch(error => console.error('Error fetching brands:', error));
}

function fetchCategories() {
    fetch(ApiBaseUrl + '/categories/index.php', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        },
        credentials: 'include'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                categories = data.data;
                populateCategorySelect();
            }
        })
        .catch(error => console.error('Error fetching categories:', error));
}

function renderProductsTable() {
    const tbody = document.querySelector('.products-table tbody');
    if (!tbody) return;

    tbody.innerHTML = '';

    if (products.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="empty-state">
                    No products found. Click "Add New Product" to get started.
                </td>
            </tr>
        `;
        return;
    }

    products.forEach(product => {
        const tr = document.createElement('tr');

        // Image handling
        let imageHtml = '<div class="product-img" style="background: #e2e8f0;"></div>';
        if (product.image) {
            // Check if image path is full URL or relative
            const imgSrc = product.image.startsWith('http') ? product.image : ApiBaseUrl + '/../uploads/' + product.image;
            imageHtml = `<img src="${imgSrc}" alt="${product.name}" class="product-img">`;
        }

        // Status badge
        let statusBadge = '<span class="badge badge-warning">Out of Stock</span>';
        if (product.quantity > 0) {
            statusBadge = '<span class="badge badge-success">In Stock</span>';
        }

        tr.innerHTML = `
            <td>${product.id}</td>
            <td>${imageHtml}</td>
            <td>${product.name}</td>
            <td>${product.description ? product.description.substring(0, 50) + '...' : ''}</td>
            <td>$${product.price}</td>
            <td>${product.brand || '-'}</td>
            <td>${product.quantity}</td>
            <td>${statusBadge}</td>
            <td>
                <div class="actions">
                    <button class="btn btn-secondary btn-small" onclick="openEditModal(${product.id})">Edit</button>
                    <button class="btn btn-danger btn-small" onclick="confirmDelete(${product.id})">Delete</button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function populateBrandSelect() {
    const select = document.getElementById('productBrand');
    if (!select) return;

    // Keep first option
    const firstOption = select.options[0];
    select.innerHTML = '';
    select.appendChild(firstOption);

    brands.forEach(brand => {
        const option = document.createElement('option');
        option.value = brand.id; // Assuming brand has id
        option.textContent = brand.name;
        select.appendChild(option);
    });
}

function populateCategorySelect() {
    const select = document.getElementById('productCategories');
    if (!select) return;

    select.innerHTML = '';
    categories.forEach(category => {
        const option = document.createElement('option');
        option.value = category.id; // Assuming category has id
        option.textContent = category.name;
        select.appendChild(option);
    });
}

// Modal functions
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add New Product';
    document.getElementById('productForm').reset();
    document.getElementById('productId').value = '';
    document.getElementById('mainImagePreview').innerHTML = '';
    document.getElementById('galleryPreview').innerHTML = '';

    document.getElementById('productModal').classList.add('show');
}

function openEditModal(productId) {
    const product = products.find(p => p.id == productId);
    if (!product) {
        console.error('Product not found:', productId);
        return;

    }

    document.getElementById('modalTitle').textContent = 'Edit Product';
    document.getElementById('productForm').reset();

    document.getElementById('productId').value = product.id;
    document.getElementById('productName').value = product.name;
    document.getElementById('productPrice').value = product.price;
    document.getElementById('productDescription').value = product.description;
    document.getElementById('productQuantity').value = product.quantity;
    document.getElementById('productSex').value = product.sex;

    // Set brand
    if (product.brand) {
        const brandObj = brands.find(b => b.name === product.brand);
        if (brandObj) {
            document.getElementById('productBrand').value = brandObj.id;
        }
    }

    // Set categories
    if (product.categories) {
        const catNames = product.categories.split(',');
        const select = document.getElementById('productCategories');
        Array.from(select.options).forEach(option => {
            if (catNames.includes(option.textContent)) {
                option.selected = true;
            }
        });
    }

    document.getElementById('mainImagePreview').innerHTML = '';
    if (product.image) {
        const imgSrc = product.image.startsWith('http') ? product.image : ApiBaseUrl + '/../uploads/' + product.image;
        document.getElementById('mainImagePreview').innerHTML = `<img src="${imgSrc}" class="preview-img">`;
    }

    document.getElementById('productModal').classList.add('show');
    console.log("opened");

}

function closeModal() {
    document.getElementById('productModal').classList.remove('show');
}

function confirmDelete(productId) {
    const product = products.find(p => p.id == productId);
    if (!product) return;

    document.getElementById('deleteProductId').value = product.id;
    document.getElementById('deleteProductName').textContent = product.name;
    document.getElementById('deleteModal').classList.add('show');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('show');
}

function deleteProduct() {
    const id = document.getElementById('deleteProductId').value;

    fetch(ApiBaseUrl + '/products/delete.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id: id }),
        credentials: 'include'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeDeleteModal();
                fetchProducts();
            } else {
                alert('Failed to delete product: ' + data.error);
            }
        })
        .catch(error => console.error('Error deleting product:', error));
}

function handleFormSubmit(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const id = formData.get('id');

    const url = id ? ApiBaseUrl + '/products/update.php' : ApiBaseUrl + '/products/add.php';

    fetch(url, {
        method: 'POST',
        body: formData, // FormData handles multipart/form-data automatically
        credentials: 'include'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal();
                fetchProducts();
            } else {
                alert('Failed to save product: ' + data.error);
            }
        })
        .catch(error => console.error('Error saving product:', error));
}

function previewMainImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('mainImagePreview').innerHTML = `<img src="${e.target.result}" class="preview-img">`;
        }
        reader.readAsDataURL(file);
    }
}

function previewGalleryImages(event) {
    const files = event.target.files;
    const previewContainer = document.getElementById('galleryPreview');
    previewContainer.innerHTML = '';

    if (files.length > 5) {
        alert('Max 5 images allowed');
        event.target.value = '';
        return;
    }

    Array.from(files).forEach(file => {
        const reader = new FileReader();
        reader.onload = function (e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'preview-img';
            previewContainer.appendChild(img);
        }
        reader.readAsDataURL(file);
    });
}