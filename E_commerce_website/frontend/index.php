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
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Perfume | Montres de collection</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./style.css">
</head>
<body>
  <div class="page">
<?php include './components/navbar.php'; ?>

    <main>
      <section class="hero">
  <div class="hero__cta">
    <p class="hero__eyebrow">Eau de Parfum</p>
    <h1 class="hero__title">L'élégance<br>dans chaque note.</h1>
    <hr class="hero__divider">
    <p class="hero__subtitle">Des parfums d'exception créés pour sublimer votre personnalité et laisser une empreinte inoubliable.</p>
    <a class="btn" href="#collections">Découvrir la collection →</a>
  </div>
  <div class="hero__image-wrap">
    <img src="./resources/banner.png" alt="My Perfume">
  </div>
</section>

      <section id="collections" class="collections">
    <div class="section-header">
          <h2>Collections</h2>
          <div class="pill-list">
            <button class="pill pill--active" data-filter="all">All</button>
            <button class="pill" data-filter="Men">Men</button>
            <button class="pill" data-filter="Women">Women</button>
            <button class="pill" data-filter="Unisex">Unisex</button>
            <button class="pill" data-filter="Best Sellers">Best Sellers</button>
            <button class="pill" data-filter="New collection">New collection</button>
          </div>
        </div>

        <div class="products" id="products-grid">
          <p style="padding: 20px; text-align: center;">Loading products...</p>
        </div>

        <?php 
        // Pre-process items to resolve image URLs and structure for JS
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
                'img' => $imgUrl,
                'sex' => $product['sex'] ?? '',
                'categories' => $product['categories'] ?? ''
            ];
        }
        ?>
        <script>
          // Store products in global scope
          window.allProducts = <?php echo json_encode($jsItems); ?>;

          // Inline script logic to ensure execution
          (function() {
              const productsGrid = document.getElementById('products-grid');
              const pills = document.querySelectorAll('.pill');

              // Render function
              const renderProducts = (productsToRender) => {
                  productsGrid.innerHTML = '';

                  if (!productsToRender || productsToRender.length === 0) {
                      productsGrid.innerHTML = '<p class="no-products">No products found matching this filter.</p>';
                      return;
                  }

                  productsToRender.forEach(product => {
                      const article = document.createElement('article');
                      article.className = 'card';
                      
                      const detailLink = product.id ? `./product_detail.php?id=${product.id}` : '#';
                      const content = (product.description || '') + `\n\nPrice: ${product.price || 'N/A'} $`;
                      // Handle potentially empty images
                      const imgUrl = product.img || 'https://via.placeholder.com/300';
                      
                      article.innerHTML = `
                          <a href="${detailLink}" style="text-decoration: none; color: inherit;">
                              <div class="card__image">
                                  <img src="${imgUrl}" alt="${product.title}" onerror="this.src='https://via.placeholder.com/300'">
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

              // Filter function
              const filterProducts = (filter) => {
                  if (!window.allProducts) return;

                  let filtered = [];
                  if (filter === 'all') {
                      filtered = window.allProducts;
                  } else {
                      const filterLower = filter.toLowerCase();
                      filtered = window.allProducts.filter(p => {
                          const sex = (p.sex || '').toLowerCase();
                          const categories = (p.categories || '').toLowerCase();
                          return sex === filterLower || categories.includes(filterLower);
                      });
                  }
                  renderProducts(filtered);
              };

              // Event listeners
              pills.forEach((pill) => {
                  pill.addEventListener('click', () => {
                      pills.forEach((p) => p.classList.remove('pill--active'));
                      pill.classList.add('pill--active');
                      
                      const filter = pill.getAttribute('data-filter');
                      if (filter) filterProducts(filter);
                  });
              });

              // Initial Render
              if (window.allProducts && window.allProducts.length > 0) {
                  filterProducts('all');
              } else {
                  productsGrid.innerHTML = '<p>No products available.</p>';
              }

              // Mobile menu logic
              const burger = document.querySelector('.burger');
              const menu = document.getElementById('mobileMenu');
              const setOpen = (open) => {
                  if (!burger || !menu) return;
                  menu.hidden = !open;
                  burger.setAttribute('aria-expanded', String(open));
              };

              if (burger && menu) {
                  setOpen(false);
                  burger.addEventListener('click', (e) => {
                      e.stopPropagation();
                      setOpen(menu.hidden);
                  });
                  menu.addEventListener('click', (e) => {
                      if (e.target.closest('a')) setOpen(false);
                  });
                  document.addEventListener('click', (e) => {
                      if (!menu.contains(e.target) && !burger.contains(e.target)) setOpen(false);
                  });
              }
          })();
        </script>

        <div class="actions actions--center">
          <a href="./products.php" class="btn btn--ghost">See all</a>
        </div>
      </section>

    </main>
    <?php include './components/footer.php'; ?>
    </div>
</body>
</html>
