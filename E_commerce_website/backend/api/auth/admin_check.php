<?php
header('Content-Type: application/json; charset=utf-8');
require "../../session/session.php";
require "../../connection/db.php";

// Check if user is admin - secure backend verification from DATABASE
function isAdmin() {
    if (!isset($_SESSION['user'])) {
        return false;
    }
    
    // ALWAYS check database for role (secure)
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$_SESSION['user']['id']]);
        $user = $stmt->fetch();
        
        return $user && $user['role'] === 'admin';
    } catch (Exception $e) {
        return false;
    }
}

function getSessionUser() {
    if (!isset($_SESSION['user'])) {
        return null;
    }
    
    // Get fresh user data from database
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT id, name, email, role FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$_SESSION['user']['id']]);
        return $stmt->fetch();
    } catch (Exception $e) {
        return null;
    }
}

// Handle different request types
$action = $_GET['action'] ?? 'check';

switch($action) {
    case 'check_admin':
        if (isAdmin()) {
            echo json_encode([
                "success" => true, 
                "isAdmin" => true,
                "user" => getSessionUser()
            ]);
        } else {
            http_response_code(403);
            echo json_encode([
                "success" => false, 
                "isAdmin" => false,
                "error" => "Admin access required"
            ]);
        }
        break;
        
    case 'check':
    default:
        if (isset($_SESSION['user'])) {
            $currentUser = getSessionUser();
            if ($currentUser) {
                echo json_encode([
                    "success" => true, 
                    "loggedIn" => true,
                    "user" => [
                        "id" => $currentUser['id'],
                        "name" => $currentUser['name'],
                        "email" => $currentUser['email']
                    ],
                    "isAdmin" => ($currentUser['role'] === 'admin')
                ]);
            } else {
                // User not found in DB, destroy session
                session_destroy();
                echo json_encode([
                    "success" => true, 
                    "loggedIn" => false,
                    "isAdmin" => false
                ]);
            }
        } else {
            echo json_encode([
                "success" => true, 
                "loggedIn" => false,
                "isAdmin" => false
            ]);
        }
        break;
}