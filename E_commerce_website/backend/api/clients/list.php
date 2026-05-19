<?php
header('Content-Type: application/json; charset=utf-8');
require "../../connection/db.php";
require "../admin_middleware.php";

$user = requireAdminAPI();

try {
    $sql = "SELECT id, name, email, created_at FROM users WHERE role = 'user' ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "data" => $clients]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
