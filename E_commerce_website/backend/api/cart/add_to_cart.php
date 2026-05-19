<?php
header('Content-Type: application/json; charset=utf-8');// return json responses

require "../auth/check_auth.php";
require "helper_cart.php";

$user = getUser();
$data = json_decode(file_get_contents("php://input"), true);

error_log("Add to cart request: " . print_r($data, true));
error_log("User session: " . print_r($_SESSION, true));

if (!isset($data['item_id'], $data['quantity'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Missing fields"]);
    exit;
}

$item_id = (int)$data['item_id'];
$quantity = max(1, (int)$data['quantity']);

$check = checkStock($pdo, $item_id, $quantity);
if (!$check['ok']) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => $check['error']]);
    exit;
}

try {
    $cart = getActiveCart($pdo, $user['id']);

    $stmt = $pdo->prepare("
        INSERT INTO cart_items (cart_id, item_id, quantity)
        VALUES (:cart_id, :item_id, :quantity)
        ON DUPLICATE KEY UPDATE quantity = LEAST(quantity + :quantity, (SELECT quantity FROM items WHERE id = :item_id))
    ");
    $stmt->execute([
        'cart_id' => $cart['id'],
        'item_id' => $item_id,
        'quantity' => $quantity
    ]);

    echo json_encode(["success" => true, "cart_id" => $cart['id']]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
