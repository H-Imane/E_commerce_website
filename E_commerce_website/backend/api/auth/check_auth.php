<?php
require "../../session/session.php";
require "../../connection/db.php";

// block access if user is not logged in
function requireLogin() {
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode(["success" => false, "error" => "You must log in"]);
        exit;
    }
}

// allow only admins - CHECK DATABASE EACH TIME
function requireAdmin() {
    requireLogin();
    
    // Get user role from database (secure)
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$_SESSION['user']['id']]);
        $user = $stmt->fetch();
        
        if (!$user || $user['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(["success" => false, "error" => "Admin only"]);
            exit;
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "error" => "Server error"]);
        exit;
    }
}

// return logged-in user with current role from DB
function getUser() {
    requireLogin();
    
    // Get fresh user data from database
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT id, name, email, role FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$_SESSION['user']['id']]);
        $user = $stmt->fetch();
        
        if (!$user) {
            // User deleted, destroy session
            session_destroy();
            http_response_code(401);
            echo json_encode(["success" => false, "error" => "User not found"]);
            exit;
        }
        
        return $user;
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "error" => "Server error"]);
        exit;
    }
}
