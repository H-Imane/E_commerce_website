<?php
/**
 * API - Récupération de toutes les catégories
 * Méthode: GET
 * Authentification: Non requise (public)
 * Retourne les catégories avec le nombre de produits associés
 */

header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/../../connection/db.php';

// Vérifie la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["success" => false, "error" => "Méthode GET uniquement"]);
    exit;
}

try {
    // Récupère toutes les catégories avec le nombre de produits
    $stmt = $pdo->prepare("
        SELECT c.id, c.name, COUNT(ic.item_id) as products_count
        FROM categories c
        LEFT JOIN item_categories ic ON c.id = ic.category_id
        GROUP BY c.id, c.name
        ORDER BY c.name ASC
    ");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retourne la liste des catégories
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => $categories,
        "total" => count($categories)
    ]);

} catch (Exception $e) {
    // Gestion des erreurs
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}