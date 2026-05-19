// Auth Form Validation & Interactions

// Check if user is already logged in
function checkSession() {
  return new Promise((resolve, reject) => {
    if (!ApiBaseUrl) {
      reject("ApiBaseUrl is not defined");
      return;
    }

    let request = new XMLHttpRequest();
    request.open('GET', ApiBaseUrl + '/auth/session_check.php', true);
    request.withCredentials = true;
    request.setRequestHeader('Content-Type', 'application/json');
    request.onload = () => {
      const status = request.status;
      if (status >= 200 && status < 300) {
        try {
          resolve(JSON.parse(request.responseText));
        } catch (e) {
          resolve({ success: false, loggedIn: false });
        }
      } else {
        resolve({ success: false, loggedIn: false });
      }
    };
    request.onerror = () => resolve({ success: false, loggedIn: false });
    request.send();
  });
}

// Redirect based on user role
// Redirect based on user role
function redirectUser(user, isAdmin = false) {
  // Check explicit isAdmin flag OR implicit role for backward compatibility
  // The isAdmin parameter should ideally be a boolean from the backend.
  if ((user && user.role === 'admin') || isAdmin) {
    window.location.href = baseUrl + '/admin/dashboard.php';
  } else {
    window.location.href = baseUrl + '/index.php';
  }
}

document.addEventListener('DOMContentLoaded', () => {
  // Check session on page load and redirect if already logged in
  checkSession().then((response) => {
    console.log('checkSession response:', response);
    if (response.success && response.loggedIn) {
      // User is already logged in, redirect them away from login/signup pages
      const currentPage = window.location.pathname;
      console.log('Current page:', currentPage);

      if (currentPage.includes('login.php') || currentPage.includes('signup.php')) {
        redirectUser(response.user, response.isAdmin);
        return;
      }
    }
  });

  // Password visibility toggle
  const passwordToggles = document.querySelectorAll('.password-toggle');

  passwordToggles.forEach((toggle) => {
    toggle.addEventListener('click', () => {
      const input = toggle.parentElement.querySelector('input');
      if (input) {
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
      }
    });
  });

  // Form validation
  const authForms = document.querySelectorAll('.auth-form');
  function authCall(data) {
    return new Promise((resolve, reject) => {
      console.log("ApiBaseUrl:", ApiBaseUrl);
      console.log("stated request data :", data);
      if (!ApiBaseUrl) {
        reject("ApiBaseUrl is not defined");
        return;
      }

      let request = new XMLHttpRequest();
      request.open('POST', ApiBaseUrl + '/auth/login.php', true);
      request.withCredentials = true;
      request.setRequestHeader('Content-Type', 'application/json');
      request.onload = () => {
        const status = request.status;
        const text = request.responseText || '';
        if (status >= 200 && status < 300) {
          try {
            resolve(JSON.parse(text));
          } catch (e) {
            // If backend returns non-JSON, resolve raw text
            resolve(text);
          }
        } else {
          // include status/text for debugging
          try {
            const errorData = JSON.parse(text);
            reject({ status, text: errorData });
          } catch (e) {
            reject({ status, text: { error: text } });
          }
        }
      };

      request.onerror = () => reject(new Error('Network error'));
      request.send(JSON.stringify(data));
    });
  }

  function signupCall(data) {
    return new Promise((resolve, reject) => {
      console.log("ApiBaseUrl:", ApiBaseUrl);
      console.log("signup request data :", data);
      if (!ApiBaseUrl) {
        reject("ApiBaseUrl is not defined");
        return;
      }

      let request = new XMLHttpRequest();
      request.open('POST', ApiBaseUrl + '/auth/register.php', true);
      request.withCredentials = true;
      request.setRequestHeader('Content-Type', 'application/json');
      request.onload = () => {
        const status = request.status;
        const text = JSON.parse(request.responseText) || '';
        if (status >= 200 && status < 300) {
          try {
            resolve(JSON.parse(text));
          } catch (e) {
            // If backend returns non-JSON, resolve raw text
            resolve(text);
          }
        } else {
          // include status/text for debugging
          try {
            const errorData = JSON.parse(text);
            reject({ status, text: errorData });
          } catch (e) {
            reject({ status, text: { error: text } });
          }
        }
      };

      request.onerror = () => reject(new Error('Network error'));
      request.send(JSON.stringify(data));
    });
  }

  document.getElementById('authButton')?.addEventListener('click', (e) => {
    e.preventDefault();
    const form = document.querySelector('.auth-form');
    if (!form || !validateForm(form)) {
      return false;
    }

    // Get form data
    const emailEl = form.querySelector('input[type="email"]');
    const passwordEl = form.querySelector('input[type="password"]');
    const data = {
      email: emailEl ? emailEl.value.trim() : '',
      password: passwordEl ? passwordEl.value : ''
    };

    authCall(data).then((response) => {
      if (response.success && response.user) {
        showMessage('Login successful!', 'success');
        // Redirect based on user role after successful login
        setTimeout(() => {
          redirectUser(response.user);
        }, 1000);
      } else {
        showMessage('Login failed: Invalid response', 'error');
      }
    }).catch((error) => {
      console.log("error response:", error);
      const errorMsg = error.text && error.text.error ? error.text.error : 'Login failed';
      showMessage(`Login failed: ${errorMsg}`, 'error');
    });
  });

  // Signup button handler
  const signupBtn = document.querySelector('#signupButton');
  if (signupBtn) {
    signupBtn.addEventListener('click', (e) => {
      e.preventDefault();
      const form = document.querySelector('.auth-form');
      if (!form || !validateForm(form)) {
        return false;
      }

      // Get form data
      const nameEl = form.querySelector('input[name="name"]');
      const emailEl = form.querySelector('input[type="email"]');
      const passwordEl = form.querySelector('input[name="password"]');
      const confirmPasswordEl = form.querySelector('input[name="confirm_password"]');

      const data = {
        name: nameEl ? nameEl.value.trim() : '',
        email: emailEl ? emailEl.value.trim() : '',
        password: passwordEl ? passwordEl.value : ''
      };

      signupCall(data).then((response) => {
        if (response.success) {
          showMessage('Registration successful!', 'success');
          // Redirect to login page after successful registration
          setTimeout(() => {
            window.location.href = '../login.php';
          }, 2000);
        } else {
          showMessage('Registration failed: Invalid response', 'error');
        }
      }).catch((error) => {
        console.log("signup error response:", error);
        const errorMsg = error.text && error.text.error ? error.text.error : 'Registration failed';
        showMessage(`Registration failed: ${errorMsg}`, 'error');
      });
    });
  }
  authForms.forEach((form) => {

    // Real-time validation
    const inputs = form.querySelectorAll('input[required]');
    inputs.forEach((input) => {
      input.addEventListener('blur', () => {
        validateField(input);
      });

      input.addEventListener('input', () => {
        clearFieldError(input);
      });
    });
  });
});

// Validate individual field
function validateField(field) {
  const value = field.value.trim();
  const fieldName = field.name || field.id;
  let isValid = true;
  let errorMessage = '';

  // Required field check
  if (field.hasAttribute('required') && !value) {
    isValid = false;
    errorMessage = 'This field is required';
  }

  // Email validation
  if (field.type === 'email' && value) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(value)) {
      isValid = false;
      errorMessage = 'Please enter a valid email address';
    }
  }

  // Password validation
  if (field.type === 'password' && value) {
    if (fieldName.includes('password') && !fieldName.includes('confirm')) {
      if (value.length < 6) {
        isValid = false;
        errorMessage = 'Password must be at least 6 characters';
      }
    }
  }

  // Confirm password validation
  if (fieldName.includes('confirm_password') || fieldName.includes('confirm-password')) {
    const passwordField = field.form.querySelector('input[type="password"]:not([name*="confirm"])');
    if (passwordField && value !== passwordField.value) {
      isValid = false;
      errorMessage = 'Passwords do not match';
    }
  }

  // Display error
  if (!isValid) {
    showFieldError(field, errorMessage);
  } else {
    clearFieldError(field);
  }

  return isValid;
}

// Validate entire form
function validateForm(form) {
  const inputs = form.querySelectorAll('input[required]');
  let isFormValid = true;

  inputs.forEach((input) => {
    if (!validateField(input)) {
      isFormValid = false;
    }
  });

  // Special check for password confirmation
  const confirmPasswordField = form.querySelector('input[name*="confirm"]');
  if (confirmPasswordField) {
    if (!validateField(confirmPasswordField)) {
      isFormValid = false;
    }
  }

  return isFormValid;
}

// Show field error
function showFieldError(field, message) {
  field.classList.add('error');

  // Remove existing error message
  const existingError = field.parentElement.querySelector('.form-error');
  if (existingError) {
    existingError.remove();
  }

  // Create and show error message
  const errorElement = document.createElement('div');
  errorElement.className = 'form-error show';
  errorElement.textContent = message;
  field.parentElement.appendChild(errorElement);
}

// Clear field error
function clearFieldError(field) {
  field.classList.remove('error');
  const errorElement = field.parentElement.querySelector('.form-error');
  if (errorElement) {
    errorElement.remove();
  }
}


// Show message (success/error)
function showMessage(message, type = 'success') {
  // Remove existing messages
  const existingMessages = document.querySelectorAll('.auth-message');
  existingMessages.forEach((msg) => msg.remove());

  // Create message element
  const messageElement = document.createElement('div');
  messageElement.className = `auth-message ${type}`;
  messageElement.textContent = message;

  // Insert at the top of auth container
  const authContainer = document.querySelector('.auth-container');
  if (authContainer) {
    authContainer.insertBefore(messageElement, authContainer.firstChild);

    // Auto-remove after 5 seconds
    setTimeout(() => {
      messageElement.remove();
    }, 5000);
  }
}

