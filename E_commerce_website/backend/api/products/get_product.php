<?php
header('Content-Type: application/json; charset=utf-8');// return json responses

// database connection and authentication
require "../../connection/db.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "error" => "Invalid or missing product ID"
    ]);
    exit;
}
$id = $_GET['id'];

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
        WHERE i.id = :id
        GROUP BY i.id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);

    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "error" => "Product not found"
        ]);
        exit;
    }

    $galleryImages = [];
    $uploadDir = __DIR__ . '/../../uploads/products/';
    
    $files = glob($uploadDir . $id . '_*.*');
    foreach ($files as $file) {
        $basename = basename($file);
        if (strpos($basename, '_main.') === false) {
             $galleryImages[] = 'products/' . $basename;
        }
    }

    echo json_encode([
        "success" => true,
        "data" => array_merge($item, ['gallery_images' => $galleryImages])
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
