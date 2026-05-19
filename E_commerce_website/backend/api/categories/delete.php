<?php
/**
 * API - Suppression d'une catégorie
 * Méthodes: POST, DELETE
 * Authentification: Admin uniquement
 * Vérifie que la catégorie n'est pas utilisée par des produits
 */

header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/../../connection/db.php';
require __DIR__ . '/../auth/check_auth.php';

// Vérifie que l'utilisateur est admin
requireAdmin();

// Vérifie la méthode HTTP
$allowedMethods = ['POST', 'DELETE'];
if (!in_array($_SERVER['REQUEST_METHOD'], $allowedMethods)) {
    http_response_code(405);
    echo json_encode(["success" => false, "error" => "Méthode non autorisée. Utilisez POST ou DELETE."]);
    exit;
}

// Récupère les données JSON ou POST
$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

// Validation: ID requis
if (empty($input['id'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "ID de la catégorie requis"]);
    exit;
}

$categoryId = intval($input['id']);

try {
    // Vérifie si la catégorie existe
    $checkStmt = $pdo->prepare("SELECT id, name FROM categories WHERE id = ?");
    $checkStmt->execute([$categoryId]);
    $category = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$category) {
        http_response_code(404);
        echo json_encode(["success" => false, "error" => "Catégorie non trouvée"]);
        exit;
    }

    // Vérifie si des produits utilisent cette catégorie
    $countStmt = $pdo->prepare("SELECT COUNT(*) as count FROM item_categories WHERE category_id = ?");
    $countStmt->execute([$categoryId]);
    $productsCount = (int) $countStmt->fetchColumn();

    if ($productsCount > 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "error" => "Impossible de supprimer : " . $productsCount . " produit(s) utilisent cette catégorie"]);
        exit;
    }

    // Supprime la catégorie
    $deleteStmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $deleteStmt->execute([$categoryId]);

    // Vérifie que la suppression a réussi
    if ($deleteStmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "error" => "Aucune catégorie supprimée"]);
        exit;
    }

    // Retourne la confirmation de suppression
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "message" => "Catégorie \"" . $category['name'] . "\" supprimée avec succès",
        "deleted_id" => $categoryId,
        "deleted_name" => $category['name']
    ]);

} catch (Exception $e) {
    // Gestion des erreurs
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}