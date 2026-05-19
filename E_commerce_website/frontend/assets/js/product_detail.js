
document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('id');

    if (!productId) {
        document.getElementById('product-container').innerHTML = '<div class="error">Product not specified</div>';
        return;
    }

    fetchProduct(productId);
});

function fetchProduct(id) {
    fetch(`${ApiBaseUrl}/products/get_product.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderProduct(data.data);
            } else {
                document.getElementById('product-container').innerHTML = `<div class="error">${data.error || 'Product not found'}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('product-container').innerHTML = '<div class="error">Failed to load product</div>';
        });
}

function renderProduct(product) {
    const container = document.getElementById('product-container');

    let imgSrc = 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=700&q=80';
    if (product.image) {
        imgSrc = product.image.startsWith('http') ? product.image : `${UploadsBaseUrl}/${product.image}`;
    }

    const isAvailable = product.quantity > 0;
    const statusClass = isAvailable ? 'available' : 'out-of-stock';
    const statusText = isAvailable ? 'In Stock' : 'Out of Stock';
    const btnDisabled = !isAvailable ? 'disabled' : '';

    container.innerHTML = `
        <div class="product-image">
            <img id="main-image" src="${imgSrc}" alt="${product.name}">
            ${product.gallery_images && product.gallery_images.length > 0 ? `
                <div class="product-gallery">
                    ${product.gallery_images.map(img => `
                        <img src="${UploadsBaseUrl}/${img}" 
                             class="gallery-thumb" 
                             onclick="changeMainImage('${UploadsBaseUrl}/${img}')"
                             alt="Product view">
                    `).join('')}
                </div>
            ` : ''}
        </div>
        <div class="product-info">
            <h1 class="product-title">${product.name}</h1>
            <div class="product-price">$${parseFloat(product.price).toFixed(2)}</div>
            <div class="product-status ${statusClass}">${statusText}</div>
            
            <div class="product-description">
                ${product.description || 'No description available.'}
            </div>

            <div class="product-meta">
                ${product.brand ? `<div class="meta-item"><strong>Brand:</strong> ${product.brand}</div>` : ''}
                ${product.sex ? `<div class="meta-item"><strong>Gender:</strong> ${product.sex}</div>` : ''}
            </div>

            <div class="add-to-cart-section">
                <div class="quantity-selector">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" value="1" min="1" max="${product.quantity}" ${btnDisabled}>
                </div>
                <button class="btn btn-primary" onclick="addToCart(${product.id})" ${btnDisabled}>
                    Add to Cart
                </button>
            </div>
        </div>
    `;
}

function addToCart(productId) {
    const quantity = document.getElementById('quantity').value;
    console.log('Adding to cart:', { productId, quantity });

    fetch(`${ApiBaseUrl}/cart/add_to_cart.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            item_id: productId,
            quantity: quantity
        }),
        credentials: 'include'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Product added to cart!');
            } else {
                if (data.error === 'You must log in' || data.error === 'Authentication required') {
                    window.location.href = 'login.php?redirect=' + encodeURIComponent(window.location.href);
                } else {
                    alert('Failed to add to cart: ' + data.error);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
}
