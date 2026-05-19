<?php
header('Content-Type: application/json; charset=utf-8');
require "../../connection/db.php";
require "../admin_middleware.php";

$user = requireAdminAPI();

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id']) || !isset($data['status'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Order ID and status are required"]);
    exit;
}

$allowed_statuses = ['pending', 'processing', 'shipped', 'delivered', 'canceled'];
if (!in_array($data['status'], $allowed_statuses)) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Invalid status"]);
    exit;
}

try {
    $sql = "UPDATE carts SET status = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$data['status'], $data['id']]);

    // Vérifier si la commande existe vraiment
$check = $pdo->prepare("SELECT id FROM carts WHERE id = ?");
$check->execute([$data['id']]);

if ($check->rowCount() > 0) {
    echo json_encode(["success" => true, "message" => "Order status updated successfully"]);
} else {
    http_response_code(404);
    echo json_encode(["success" => false, "error" => "Order not found"]);
}
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
