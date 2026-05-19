<?php 
include_once __DIR__ . '/../config/conf.php';
?>
<div class="topbar">
      <button class="icon-btn burger" type="button" aria-label="Menu" aria-controls="mobileMenu" aria-expanded="false">
        <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
          <path d="M4 7h16M4 12h16M4 17h16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
      </button>
      <div class="logo">My Perfume</div>
      <nav class="nav">
        <a href="<?php echo $baseUrl . '/index.php'; ?>" class="nav__link">Home</a>
        <a href="<?php echo $baseUrl . '/products.php'; ?>" class="nav__link">Shop</a>
        <a href="<?php echo $baseUrl . '/about.php'; ?>" class="nav__link">About Us</a>
      </nav>
      <div class="actions">
        <a href="<?php echo $baseUrl . '/checkout.php'; ?>" class="icon-btn" aria-label="Cart">
          <svg class="icon icon-cart" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M7 8.5A5 5 0 0 1 12 4a5 5 0 0 1 5 4.5h1.75a.75.75 0 0 1 .75.82l-.96 9.5A2.75 2.75 0 0 1 15.8 21H8.2a2.75 2.75 0 0 1-2.74-2.18l-.96-9.5a.75.75 0 0 1 .75-.82zm6.5 0A3.5 3.5 0 0 0 12 5.5 3.5 3.5 0 0 0 9.5 8.5z" fill="currentColor"/>
          </svg>
        </a>
        <a href="<?php echo $baseUrl . '/login.php'; ?>" class="icon-btn" aria-label="User">
          <svg class="icon icon-user" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M12 12.5a4 4 0 1 0-4-4 4 4 0 0 0 4 4m-6.5 6.25a.75.75 0 0 1 .44-.68A11 11 0 0 1 12 17a11 11 0 0 1 6.06 1.07.75.75 0 0 1 .44.68v1.2a.75.75 0 0 1-.75.75H6.25a.75.75 0 0 1-.75-.75z" fill="currentColor"/>
          </svg>
        </a>
        <a href="<?php echo $baseUrl . '/logout.php'; ?>" class="nav__link" aria-label="Logout">logout</a>
      </div>
</div>

<div class="mobile-menu" id="mobileMenu" hidden>
  <a href="<?php echo $baseUrl . '/index.php'; ?>" class="mobile-menu__link">Home</a>
  <a href="<?php echo $baseUrl . '/products.php'; ?>" class="mobile-menu__link">Shop</a>
  <a href="<?php echo $baseUrl . '/about.php'; ?>" class="mobile-menu__link">About Us</a>
  <a href="<?php echo $baseUrl . '/logout.php'; ?>" class="mobile-menu__link">logout</a>
</div>