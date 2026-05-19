<?php
header('Content-Type: application/json; charset=utf-8');

require "../../connection/db.php";
require "../auth/check_auth.php";
require "../cart/helper_cart.php";

$user = getUser();

try {
    $pdo->beginTransaction();

    $cart = getActiveCart($pdo, $user['id']);
    
    $stmt = $pdo->prepare("
        SELECT ci.item_id, ci.quantity, i.name, i.quantity as stock 
        FROM cart_items ci
        JOIN items i ON ci.item_id = i.id
        WHERE ci.cart_id = :cart_id
    ");
    $stmt->execute(['cart_id' => $cart['id']]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($items)) {
        throw new Exception("Cart is empty");
    }

    foreach ($items as $item) {
        if ($item['quantity'] > $item['stock']) {
            throw new Exception("Not enough stock for item: " . $item['name']);
        }

        $updateStmt = $pdo->prepare("UPDATE items SET quantity = quantity - :qty WHERE id = :id");
        $updateStmt->execute(['qty' => $item['quantity'], 'id' => $item['item_id']]);
    }

    $stmt = $pdo->prepare("UPDATE carts SET status = 'ordered', created_at = NOW() WHERE id = :id");
    $stmt->execute(['id' => $cart['id']]);

    $pdo->commit();

    echo json_encode(["success" => true, "message" => "Order placed successfully"]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(400);
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
