<?php
header('Content-Type: application/json; charset=utf-8');// return json responses
require "../auth/check_auth.php";
require "helper_cart.php";

$user = getUser();
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['cart_item_id'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Missing cart_item_id"]);
    exit;
}

$cart_item_id = (int)$data['cart_item_id'];

$stmt = $pdo->prepare("DELETE FROM cart_items WHERE id = :cart_item_id");
$stmt->execute(['cart_item_id' => $cart_item_id]);
echo json_encode(["success" => true]);
