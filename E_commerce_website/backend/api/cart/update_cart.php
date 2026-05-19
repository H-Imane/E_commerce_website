<?php
header('Content-Type: application/json; charset=utf-8');// return json responses
// authentication and cart helpers
require "../auth/check_auth.php";
require "helper_cart.php";

$user = getUser();// get logged-in user
$data = json_decode(file_get_contents("php://input"), true);// read json request body


 // validate required fields
if (!isset($data['cart_item_id'], $data['quantity'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Missing fields"]);
    exit;
}

$cart_item_id = (int)$data['cart_item_id'];
$quantity = max(1, (int)$data['quantity']);// enforce minimum quantity >=1


// get item_id for the cart item
$stmt = $pdo->prepare("SELECT item_id FROM cart_items WHERE id = :cart_item_id");
$stmt->execute(['cart_item_id' => $cart_item_id]);
$row = $stmt->fetch();
if (!$row) {
    // cart item does not exist
    http_response_code(404);
    echo json_encode(["success" => false, "error" => "Cart item not found"]);
    exit;
}

$item_id = $row['item_id'];

// check stock availability
$check = checkStock($pdo, $item_id, $quantity);
if (!$check['ok']) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => $check['error']]);
    exit;
}

// update cart item quantity
$stmt = $pdo->prepare("UPDATE cart_items SET quantity = :quantity WHERE id = :cart_item_id");
$stmt->execute(['quantity' => $quantity, 'cart_item_id' => $cart_item_id]);
// confirm update
echo json_encode(["success" => true, "cart_item_id" => $cart_item_id, "quantity" => $quantity]);
