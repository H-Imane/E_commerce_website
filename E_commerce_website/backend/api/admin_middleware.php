<?php
// Admin API middleware - Include this at the top of admin API endpoints
// This file provides secure admin-only access control for API endpoints

require_once __DIR__ . "/../session/session.php";

function requireAdminAPI() {
    // Check if user session exists
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode([
            "success" => false, 
            "error" => "Authentication required"
        ]);
        exit;
    }
    
    // Verify user role from database (Secure)
    global $pdo;
    
    // If $pdo is not available, we cannot verify. 
    // This assumes db.php is included before this file.
    if (!isset($pdo)) {
        http_response_code(500);
        echo json_encode(["success" => false, "error" => "Database connection missing in middleware"]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$_SESSION['user']['id']]);
        $user = $stmt->fetch();

        if (!$user || $user['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode([
                "success" => false, 
                "error" => "Admin access required"
            ]);
            exit;
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "error" => "Auth verification failed"]);
        exit;
    }
    
    return $_SESSION['user'];
}

function getAdminAPIUser() {
    return requireAdminAPI();
}

// Optional: Verify admin status without terminating execution
function isAdminAPIUser() {
    if (!isset($_SESSION['user'])) return false;
    
    global $pdo;
    if (!isset($pdo)) return false;

    try {
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$_SESSION['user']['id']]);
        $user = $stmt->fetch();
        return $user && $user['role'] === 'admin';
    } catch (Exception $e) {
        return false;
    }
}
?>