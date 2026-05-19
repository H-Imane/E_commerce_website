<?php
header('Content-Type: application/json; charset=utf-8');
require "../../session/session.php";
require "../../connection/db.php";

// Check if user is logged in and get fresh role from database
if (isset($_SESSION['user'])) {
    try {
        // Get current user role from database (secure)
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$_SESSION['user']['id']]);
        $user = $stmt->fetch();
        
        if (!$user) {
            // User deleted, destroy session
            session_destroy();
            echo json_encode([
                "success" => true, 
                "loggedIn" => false,
                "isAdmin" => false
            ]);
        } else {
            echo json_encode([
                "success" => true, 
                "loggedIn" => true,
                "user" => $_SESSION['user'], // basic info only (no role)
                "isAdmin" => ($user['role'] === 'admin')
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            "success" => false, 
            "error" => "Database error"
        ]);
    }
} else {
    echo json_encode([
        "success" => true, 
        "loggedIn" => false,
        "isAdmin" => false
    ]);
}