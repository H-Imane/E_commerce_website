<?php
header('Content-Type: application/json; charset=utf-8');// return json responses

require "../auth/check_auth.php";
require "helper_cart.php";

$user = getUser();
$cart = getActiveCart($pdo, $user['id']);

$stmt = $pdo->prepare("
    SELECT ci.id as cart_item_id, ci.quantity, i.id as item_id, i.name, i.price, i.image
    FROM cart_items ci
    JOIN items i ON ci.item_id = i.id
    WHERE ci.cart_id = :cart_id
");

$stmt->execute(['cart_id' => $cart['id']]);
$items = $stmt->fetchAll();

echo json_encode(["success" => true, "cart" => $cart, "items" => $items]);
