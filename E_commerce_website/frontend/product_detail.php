<?php
include_once __DIR__ . '/config/conf.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details | My Perfume</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="./assets/css/product_detail.css">
</head>
<body>
    <div class="page">
        <?php include './components/navbar.php'; ?>

        <main class="container">
            <div id="product-container" class="product-detail">
                <div class="loading">Loading product details...</div>
            </div>
        </main>

        <?php include './components/footer.php'; ?>
    </div>

    <script>
        const ApiBaseUrl = '<?php echo rtrim($apiBaseUrl, "/"); ?>';
        const UploadsBaseUrl = '<?php echo rtrim($uploadsBaseUrl, "/"); ?>';
    </script>
    <script src="./assets/js/product_detail.js"></script>
</body>
</html>
