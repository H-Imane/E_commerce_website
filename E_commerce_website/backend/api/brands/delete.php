<?php
/**
 * API - Suppression d'une marque
 * Méthodes: POST, DELETE
 * Authentification: Admin uniquement
 * Vérifie que la marque n'est pas utilisée par des produits
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
    echo json_encode(["success" => false, "error" => "Méthode non autorisée"]);
    exit;
}

// Récupère les données JSON ou POST
$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

// Validation: ID requis
if (empty($input['id'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "ID de la marque requis"]);
    exit;
}

$brandId = intval($input['id']);

try {
    // Vérifie si la marque existe
    $checkStmt = $pdo->prepare("SELECT id, name FROM brands WHERE id = ?");
    $checkStmt->execute([$brandId]);
    $brand = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$brand) {
        http_response_code(404);
        echo json_encode(["success" => false, "error" => "Marque non trouvée"]);
        exit;
    }

    // Vérifie si des produits utilisent cette marque
    $countStmt = $pdo->prepare("SELECT COUNT(*) as count FROM items WHERE brand_id = ?");
    $countStmt->execute([$brandId]);
    $productsCount = (int) $countStmt->fetchColumn();

    // Empêche la suppression si des produits utilisent cette marque
    if ($productsCount > 0) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Impossible de supprimer : {$productsCount} produit(s) utilisent cette marque"
        ]);
        exit;
    }

    // Supprime la marque
    $deleteStmt = $pdo->prepare("DELETE FROM brands WHERE id = ?");
    $deleteStmt->execute([$brandId]);

    // Vérifie que la suppression a réussi
    if ($deleteStmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "error" => "Aucune marque supprimée"]);
        exit;
    }

    // Retourne la confirmation de suppression
    echo json_encode([
        "success" => true,
        "message" => "Marque \"{$brand['name']}\" supprimée avec succès",
        "deleted_id" => $brandId,
        "deleted_name" => $brand['name']
    ]);

} catch (Exception $e) {
    // Gestion des erreurs
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}