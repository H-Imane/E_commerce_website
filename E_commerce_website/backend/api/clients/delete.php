<?php
header('Content-Type: application/json; charset=utf-8');
require "../../connection/db.php";
require "../admin_middleware.php";

$user = requireAdminAPI();

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Client ID is required"]);
    exit;
}

try {
    $sql = "DELETE FROM users WHERE id = ? AND role = 'user'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$data['id']]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => true, "message" => "Client deleted successfully"]);
    } else {
        http_response_code(404);
        echo json_encode(["success" => false, "error" => "Client not found or is an admin"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
