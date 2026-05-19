<?php
/**
 * API - Récupération de toutes les marques
 * Méthode: GET
 * Authentification: Non requise (public)
 * Retourne les marques avec le nombre de produits associés
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
    // Récupère toutes les marques avec le nombre de produits
    $stmt = $pdo->prepare("
        SELECT b.id, b.name, COUNT(i.id) as products_count
        FROM brands b
        LEFT JOIN items i ON b.id = i.brand_id
        GROUP BY b.id, b.name
        ORDER BY b.name ASC
    ");
    $stmt->execute();
    $brands = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retourne la liste des marques
    echo json_encode([
        "success" => true,
        "data" => $brands,
        "total" => count($brands)
    ]);

} catch (Exception $e) {
    // Gestion des erreurs
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}