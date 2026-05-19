<?php
require "../../connection/db.php";

function getActiveCart($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM carts WHERE user_id = :user_id AND status = 'active'");
    $stmt->execute(['user_id' => $user_id]);
    $cart = $stmt->fetch();

    if (!$cart) {
        $stmt = $pdo->prepare("INSERT INTO carts (user_id, status) VALUES (:user_id, 'active')");
        $stmt->execute(['user_id' => $user_id]);
        $cart_id = $pdo->lastInsertId();
        $cart = ['id' => $cart_id, 'user_id' => $user_id, 'status' => 'active'];
    }

    return $cart;
}

function checkStock($pdo, $item_id, $quantity) {
    $stmt = $pdo->prepare("SELECT quantity FROM items WHERE id = :item_id");
    $stmt->execute(['item_id' => $item_id]);
    $item = $stmt->fetch();

    if (!$item) {
        return ["ok" => false, "error" => "Item not found"];
    }

    if ($quantity > $item['quantity']) {
        return ["ok" => false, "error" => "Not enough stock"];
    }

    return ["ok" => true];
}
