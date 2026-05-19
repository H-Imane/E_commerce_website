<?php

header('Content-Type: application/json; charset=utf-8');// return json responses

// database connection and authentication
require "../../connection/db.php";

try {
    $sql = "
        SELECT 
            i.id,
            i.name,
            i.description,
            i.price,
            i.image,
            i.quantity,
            i.sex,
            b.name AS brand,
            GROUP_CONCAT(c.name) AS categories
        FROM items i
        LEFT JOIN brands b ON i.brand_id = b.id
        LEFT JOIN item_categories ic ON i.id = ic.item_id
        LEFT JOIN categories c ON ic.category_id = c.id
        GROUP BY i.id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        "success" => true,
        "data" => $items
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
