<?php
header('Content-Type: application/json; charset=utf-8');
require "../../connection/db.php";
require "../admin_middleware.php";

$user = requireAdminAPI();

try {
    // Get orders with user details and total items/price
    // We filter out 'active' carts, so we only get actual orders
    // status != 'active'
    
    $sql = "
        SELECT 
            c.id,
            u.name as customer,
            u.email,
            c.status,
            c.created_at,
            COUNT(ci.id) as items_count,
            SUM(ci.quantity * i.price) as total
        FROM carts c
        LEFT JOIN users u ON c.user_id = u.id
        LEFT JOIN cart_items ci ON c.id = ci.cart_id
        LEFT JOIN items i ON ci.item_id = i.id
        WHERE c.status IN ('ordered', 'pending', 'processing', 'shipped', 'delivered', 'canceled')
        GROUP BY c.id
        ORDER BY c.created_at DESC
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // For each order, fetch items details if needed, but for the list view, the above is enough.
    // However, the frontend might expect 'items' array for the modal details.
    // Let's fetch items for each order or do it in a separate call?
    // The frontend code `order_mg.php` has `data-order` attribute with full order details including items.
    // So I should fetch items for each order.
    
    foreach ($orders as &$order) {
        $sqlItems = "
            SELECT 
                i.name,
                ci.quantity as qty,
                i.price as unit_price
            FROM cart_items ci
            JOIN items i ON ci.item_id = i.id
            WHERE ci.cart_id = ?
        ";
        $stmtItems = $pdo->prepare($sqlItems);
        $stmtItems->execute([$order['id']]);
        $order['items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
        
        // Ensure numeric types for frontend
        $order['total'] = (float)$order['total'];
        $order['items_count'] = (int)$order['items_count'];
    }

    echo json_encode(["success" => true, "data" => $orders]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
