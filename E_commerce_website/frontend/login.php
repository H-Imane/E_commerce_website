<?php
include_once __DIR__ . '/config/conf.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Perfume | Sign In</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo $baseUrl; ?>/style.css">
  <link rel="stylesheet" href="<?php echo $baseUrl; ?>/assets/css/auth.css">
</head>
<body>
  <div class="auth-page">
    <div class="auth-container">
      <div class="auth-header">
        <h1 class="auth-brand">My Perfume</h1>
        <p class="auth-tagline">Welcome back to timeless elegance</p>
      </div>

      <h2 class="auth-form-title">Sign In</h2>
      
      <?php if (isset($_GET['error'])): ?>
        <div class="auth-message error"><?php echo htmlspecialchars($_GET['error']); ?></div>
      <?php endif; ?>
      
      <?php if (isset($_GET['success'])): ?>
        <div class="auth-message success"><?php echo htmlspecialchars($_GET['success']); ?></div>
      <?php endif; ?>

      <form class="auth-form" action="#" method="POST">
        <div class="form-group">
          <label for="email">Email Address</label>
          <input 
            type="email" 
            id="email" 
            name="email" 
            placeholder="your@email.com" 
            required
            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
          />
        </div>
        
        <div class="form-group">
          <label for="password">Password</label>
          <div class="password-input-wrapper">
            <input 
              type="password" 
              id="password" 
              name="password" 
              placeholder="Enter your password" 
              required
            />
            <button type="button" class="password-toggle" aria-label="Toggle password visibility">
              <svg class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
            </button>
          </div>
        </div>
        
        <div class="form-options">
          <label class="checkbox-label">
            <input type="checkbox" name="remember" />
            <span>Remember me</span>
          </label>
          <a href="#" class="forgot-password-link">Forgot password?</a>
        </div>
        
        <button type="button" id="authButton" class="auth-submit-btn">Sign In</button>
      </form>
      
      <div class="auth-separator">
        <span>or</span>
      </div>
      
      <p class="auth-switch-text">
        Don't have an account? <a href="<?php echo $baseUrl; ?>/user/signup.php" class="auth-switch-link">Create one</a>
      </p>
    </div>
  </div>
  <script ><?php echo "let ApiBaseUrl = '" . $apiBaseUrl . "';"; ?></script>
  <script><?php echo "const baseUrl = '" . $baseUrl . "';"; ?></script>
  <script src="<?php echo $baseUrl; ?>/assets/js/auth.js?v=<?php echo time(); ?>"></script>
</body>
</html>