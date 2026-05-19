<?php
// Admin authentication check utility - SECURE DATABASE-ONLY VERIFICATION
session_start();

// Include database connection for role verification
require_once __DIR__ . '/../../backend/connection/db.php';

function checkAdminAuth() {
    // Check if user session exists
    if (!isset($_SESSION['user'])) {
        // Not logged in, redirect to login
        header('Location: ../login.php?error=Please login to access admin area');
        exit;
    }
    
    $userId = $_SESSION['user']['id'];
    
    // ALWAYS verify user role from database (secure - cannot be manipulated)
    try {
        global $pdo;
        $stmt = $pdo->prepare("SELECT id, name, email, role FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $dbUser = $stmt->fetch();
        
        if (!$dbUser) {
            // User deleted from database, destroy session
            session_destroy();
            header('Location: ../login.php?error=Session expired - user not found');
            exit;
        }
        
        if ($dbUser['role'] !== 'admin') {
            // Not an admin in database, redirect to user area
            header('Location: ../index.php?error=Access denied - Admin only');
            exit;
        }
        
        // User is verified admin in database, allow access
        return $dbUser;
        
    } catch (Exception $e) {
        // Database error, deny access for security
        error_log("Admin auth check DB error: " . $e->getMessage());
        header('Location: ../login.php?error=Database error - please try again');
        exit;
    }
}

function getAdminUser() {
    return checkAdminAuth();
}

// Function to check admin status without redirect (for API calls)
function isCurrentUserAdmin() {
    if (!isset($_SESSION['user'])) {
        return false;
    }
    
    // Check database for current role
    try {
        global $pdo;
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$_SESSION['user']['id']]);
        $user = $stmt->fetch();
        
        return $user && $user['role'] === 'admin';
    } catch (Exception $e) {
        return false;
    }
}
?>