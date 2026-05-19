
document.addEventListener('DOMContentLoaded', () => {
    fetchCart();
});

function fetchCart() {
    fetch(`${ApiBaseUrl}/cart/get_cart.php`, {
        credentials: 'include'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cartData = { ...data.cart, items: data.items };
                renderCart(cartData);
            } else {
                if (data.error === 'Authentication required') {
                    window.location.href = 'login.php';
                } else {
                    document.getElementById('cart-items').innerHTML = `<div class="error">${data.error}</div>`;
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('cart-items').innerHTML = '<div class="error">Failed to load cart</div>';
        });
}

function renderCart(cart) {
    const container = document.getElementById('cart-items');

    if (!cart.items || cart.items.length === 0) {
        container.innerHTML = '<div class="empty-cart">Your cart is empty</div>';
        document.getElementById('cart-total-amount').textContent = '$0.00';
        document.querySelector('button[type="submit"]').disabled = true;
        return;
    }

    let total = 0;
    let html = '';

    cart.items.forEach(item => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;

        html += `
            <div class="cart-item">
                <div class="item-info">
                    <div class="item-name">${item.name}</div>
                    <div class="item-price">$${parseFloat(item.price).toFixed(2)} x ${item.quantity}</div>
                </div>
                <div class="item-actions">
                    <div class="item-total">$${itemTotal.toFixed(2)}</div>
                    <button class="btn-remove" onclick="removeFromCart(${item.cart_item_id})">Remove</button>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
    document.getElementById('cart-total-amount').textContent = '$' + total.toFixed(2);
}

function removeFromCart(cartItemId) {
    if (!confirm('Remove this item from cart?')) return;

    fetch(`${ApiBaseUrl}/cart/remove_from_cart.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            cart_item_id: cartItemId
        }),
        credentials: 'include'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                fetchCart();
            } else {
                alert('Failed to remove item: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
}

function handleCheckout(e) {
    e.preventDefault();

    if (!confirm('Are you sure you want to place this order?')) return;

    fetch(`${ApiBaseUrl}/orders/create.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        credentials: 'include'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Order placed successfully!');
                window.location.href = 'index.php';
            } else {
                alert('Failed to place order: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
}
