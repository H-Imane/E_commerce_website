<?php
include_once __DIR__ . '/config/conf.php';
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php?redirect=checkout.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | My Perfume</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="./assets/css/checkout.css">
</head>
<body>
    <div class="page">
        <?php include './components/navbar.php'; ?>

        <main class="container">
            <div class="checkout-container">
                <h1>Checkout</h1>
                
                <div class="checkout-grid">
                    <div class="cart-summary">
                        <h2>Your Cart</h2>
                        <div id="cart-items" class="cart-items">
                            <div class="loading">Loading cart...</div>
                        </div>
                        <div class="cart-total">
                            <span>Total:</span>
                            <span id="cart-total-amount">$0.00</span>
                        </div>
                    </div>

                    <div class="checkout-form">
                        <h2>Shipping Information</h2>
                        <form id="checkout-form" onsubmit="handleCheckout(event)">
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea id="address" required placeholder="Enter your shipping address"></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-block">Confirm Order</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>

        <?php include './components/footer.php'; ?>
    </div>

    <script>
        const ApiBaseUrl = '<?php echo rtrim($apiBaseUrl, "/"); ?>';
    </script>
    <script src="./assets/js/checkout.js"></script>
</body>
</html>
