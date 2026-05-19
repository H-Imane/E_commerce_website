<?php
include_once __DIR__ . '/../config/conf.php';

$current = basename($_SERVER['PHP_SELF'] ?? '');

$links = [
    ['file' => 'dashboard.php', 'label' => 'Dashboard'],
    ['file' => 'product_mg.php', 'label' => 'Products'],
    ['file' => 'catbrand_mg.php', 'label' => 'Categories & Brands'],
    ['file' => 'order_mg.php', 'label' => 'Orders'],
    ['file' => 'client_mg.php', 'label' => 'Clients'],
];
?>
<nav class="admin-nav">
  <div class="admin-nav__inner">
    <a class="admin-nav__brand" href="<?php echo htmlspecialchars($baseUrl . '/admin/dashboard.php'); ?>">My Perfume <span>Admin</span></a>

    <div class="admin-nav__links" id="adminNavLinks">
      <?php foreach ($links as $link):
        $href = $baseUrl . '/admin/' . $link['file'];
        $active = ($current === $link['file']);
      ?>
        <a class="admin-nav__link <?php echo $active ? 'is-active' : ''; ?>" href="<?php echo htmlspecialchars($href); ?>">
          <?php echo htmlspecialchars($link['label']); ?>
        </a>
      <?php endforeach; ?>
    </div>

    <div class="admin-nav__actions">
      <a href="<?php echo htmlspecialchars($baseUrl . '/login.php'); ?>" class="admin-nav__icon-btn" aria-label="Sign in">
        <svg class="admin-nav__icon" viewBox="0 0 24 24" aria-hidden="true">
          <path d="M12 12.5a4 4 0 1 0-4-4 4 4 0 0 0 4 4m-6.5 6.25a.75.75 0 0 1 .44-.68A11 11 0 0 1 12 17a11 11 0 0 1 6.06 1.07.75.75 0 0 1 .44.68v1.2a.75.75 0 0 1-.75.75H6.25a.75.75 0 0 1-.75-.75z" fill="currentColor"/>
        </svg>
      </a>
      <a href="<?php echo htmlspecialchars($baseUrl . '/logout.php'); ?>" class="admin-nav__link">logout</a>
    </div>

    <button class="admin-nav__toggle" type="button" aria-label="Menu" aria-controls="adminNavLinks" aria-expanded="false">
      <span></span>
      <span></span>
      <span></span>
    </button>
  </div>
</nav>
