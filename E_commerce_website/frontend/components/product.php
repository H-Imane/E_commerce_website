<?php
// Load config for base URLs
include_once __DIR__ . '/../config/conf.php';

$title = $componentData['title'] ?? 'Default Title';
$content = $componentData['content'] ?? 'No content provided.';
$status = $componentData['status'] ?? 'inactive';
$rawImg = $componentData['img'] ?? null;

// Determine image URL:
if (!$rawImg) {
  $img = 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=700&q=80';
} else {
  // Absolute URL or protocol-relative
  if (preg_match('#^(https?:)?//#i', $rawImg) || strpos($rawImg, '/') === 0) {
    $img = $rawImg;
  } else {
    // Use configured uploads base URL if available
    $uploadsBase = rtrim($uploadsBaseUrl ?? '/E_commerce_website/projet/uploads/', '/');
    $img = $uploadsBase . '/' . ltrim($rawImg, '/');
  }
}
?>
<?php
$productId = $componentData['id'] ?? '#';
$detailLink = $productId !== '#' ? "./product_detail.php?id=" . $productId : '#';
?>
<article class="card">
  <a href="<?php echo htmlspecialchars($detailLink); ?>" style="text-decoration: none; color: inherit;">
    <div class="card__image">
      <img src="<?php echo htmlspecialchars($img); ?>" alt="Product Image">
    </div>
    <div class="card__body">
      <h3><?php echo htmlspecialchars($title); ?></h3>
      <p><?php echo htmlspecialchars($content); ?></p>
      <span class="link"><?php echo htmlspecialchars($status); ?></span>
    </div>
  </a>
</article>