// Admin authentication utilities for frontend JavaScript
// Include this script on admin pages to enforce role-based access

// Secure backend admin check
function checkAdminAccessSecure() {
  return new Promise((resolve, reject) => {
    if(!ApiBaseUrl) {
      reject("ApiBaseUrl is not defined");
      return;
    }
    
    let request = new XMLHttpRequest();
    request.open('GET', ApiBaseUrl + '/auth/admin_check.php?action=check_admin', true);
    request.withCredentials = true;
    request.setRequestHeader('Content-Type','application/json');
    request.onload = () => {
      const status = request.status;
      if (status >= 200 && status < 300) {
        try {
          const response = JSON.parse(request.responseText);
          if (response.success && response.isAdmin) {
            resolve(true);
          } else {
            reject('Admin access required');
          }
        } catch (e) {
          reject('Invalid response from server');
        }
      } else {
        reject('Admin access denied');
      }
    };
    request.onerror = () => reject('Network error');
    request.send();
  });
}

// Check if current user has admin access
function checkAdminAccess() {
  return checkAdminAccessSecure().then(() => {
    return true;
  }).catch((error) => {
    console.log('Admin access check failed:', error);
    // Not an admin or not logged in, redirect to login
    window.location.href = '../login.php?error=Admin access required';
    return false;
  });
}

// Logout function for admin users
function adminLogout() {
  if (typeof ApiBaseUrl === 'undefined') {
    console.error('ApiBaseUrl not defined');
    return;
  }
  
  fetch(ApiBaseUrl + '/auth/logout.php', {
    method: 'POST',
    credentials: 'include'
  })
  .then(() => {
    // Redirect to login page after logout
    window.location.href = '../login.php?success=Logged out successfully';
  })
  .catch(() => {
    // Even if logout API fails, redirect to login
    window.location.href = '../login.php';
  });
}

// Initialize admin page protection
function initAdminPageProtection() {
  if (typeof checkSession === 'function') {
    checkAdminAccess();
  } else {
    console.error('checkSession function not available. Make sure auth.js is loaded first.');
  }
}

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  initAdminPageProtection();
  
  // Attach logout functionality to logout buttons/links
  const logoutButtons = document.querySelectorAll('[data-admin-logout]');
  logoutButtons.forEach(button => {
    button.addEventListener('click', (e) => {
      e.preventDefault();
      if (confirm('Are you sure you want to logout?')) {
        adminLogout();
      }
    });
  });
});