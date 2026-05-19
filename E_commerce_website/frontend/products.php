<?php
include_once __DIR__ . '/config/conf.php';

$items = [];
$apiError = null;
$apiUrl = rtrim($apiBaseUrl, '/') . '/products/get_products.php';
$json = false;

if (ini_get('allow_url_fopen')) {
  $ctx = stream_context_create(['http' => ['timeout' => 3]]);
  $json = @file_get_contents($apiUrl, false, $ctx);
}
if ($json === false && function_exists('curl_init')) {
  $ch = curl_init($apiUrl);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 3);
  $json = curl_exec($ch);
  $curlErr = curl_error($ch);
  curl_close($ch);
  if ($json === false) {
    $apiError = 'Failed to fetch products API: ' . $curlErr;
  }
}
if ($json === false) {
  if ($apiError === null) $apiError = 'Products API not reachable';
} else {
  $data = json_decode($json, true);
  if (json_last_error() === JSON_ERROR_NONE) {
    if (!empty($data["success"]) && isset($data["data"])) {
      $items = $data["data"];
    } else {
      $apiError = $data["error"] ?? 'API returned no items';
    }
  } else {
    $apiError = 'Invalid JSON from products API';
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <title>all products</title>
</head>
<body>
    
<?php include './components/navbar.php'; ?>
    <main>
      <section class="collections" id="collections">
        <h2 class="collections__title">Our Collections</h2>
        <div class="collections__grid" id="collectionsGrid">
        <div class="search-container" style="margin-bottom: 30px; display: flex; justify-content: center;">
            <input type="text" id="searchInput" placeholder="Search products..." style="padding: 12px 20px; width: 100%; max-width: 500px; border: 1px solid #ddd; border-radius: 30px; font-size: 16px; outline: none; transition: border-color 0.3s; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        </div>

        <div class="products" id="products-grid">
            <p style="text-align: center; width: 100%;">Loading products...</p>
        </div>

        <?php
        // Pre-process items for JS
        $jsItems = [];
        $uploadsBase = rtrim($uploadsBaseUrl ?? '/E_commerce_website/projet/uploads/', '/');
        
        foreach ($items as $product) {
            $rawImg = $product['image'] ?? null;
            $imgUrl = 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=700&q=80';
            
            if ($rawImg) {
                if (preg_match('#^(https?:)?//#i', $rawImg) || strpos($rawImg, '/') === 0) {
                    $imgUrl = $rawImg;
                } else {
                    $imgUrl = $uploadsBase . '/' . ltrim($rawImg, '/');
                }
            }

            $jsItems[] = [
                'id' => $product['id'] ?? null,
                'title' => $product['name'] ?? 'Untitled',
                'description' => $product['description'] ?? '',
                'price' => $product['price'] ?? 'N/A',
                'status' => (!empty($product['quantity']) && intval($product['quantity']) > 0) ? 'available' : 'out of stock',
                'img' => $imgUrl
            ];
        }
        ?>

        <script>
        (function() {
            const allProducts = <?php echo json_encode($jsItems); ?>;
            const searchInput = document.getElementById('searchInput');
            const productsGrid = document.getElementById('products-grid');

            const renderProducts = (products) => {
                productsGrid.innerHTML = '';
                
                if (products.length === 0) {
                    productsGrid.innerHTML = '<p class="no-products" style="text-align: center; width: 100%;">No products found.</p>';
                    return;
                }

                products.forEach(product => {
                    const article = document.createElement('article');
                    article.className = 'card';
                    
                    const detailLink = product.id ? `./product_detail.php?id=${product.id}` : '#';
                    const content = (product.description || '') + `\n\nPrice: ${product.price || 'N/A'} $`;
                    
                    article.innerHTML = `
                        <a href="${detailLink}" style="text-decoration: none; color: inherit;">
                            <div class="card__image">
                                <img src="${product.img}" alt="${product.title}" onerror="this.src='https://via.placeholder.com/300'">
                            </div>
                            <div class="card__body">
                                <h3>${product.title}</h3>
                                <p>${content}</p>
                                <span class="link">${product.status}</span>
                            </div>
                        </a>
                    `;
                    productsGrid.appendChild(article);
                });
            };

            // Initial render
            renderProducts(allProducts);

            // Search logic
            searchInput.addEventListener('keyup', (e) => {
                const term = e.target.value.toLowerCase().trim();
                
                if (!term) {
                    renderProducts(allProducts);
                    return;
                }

                const filtered = allProducts.filter(p => {
                    const title = (p.title || '').toLowerCase();
                    const desc = (p.description || '').toLowerCase();
                    return title.includes(term) || desc.includes(term);
                });

                renderProducts(filtered);
            });
        })();
        </script>
        </div>
        </div>
      </section>
    </main>
<?php include './components/footer.php'; ?>

<?php
include_once __DIR__ . '/config/conf.php';
$jsApi = rtrim($apiBaseUrl, '/');
$jsUploads = rtrim($uploadsBaseUrl, '/');
echo "<script>const API_BASE_URL = " . json_encode($jsApi) . "; const UPLOADS_BASE_URL = " . json_encode($jsUploads) . ";</script>";
?>

</body>
</html>