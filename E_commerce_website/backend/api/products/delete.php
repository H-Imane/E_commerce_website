<?php
/**
 * API - Suppression d'un produit
 * Méthodes: POST, DELETE
 * Authentification: Admin uniquement
 * Vérifie que le produit n'est pas dans un panier actif
 * Supprime le produit et toutes ses images
 */

header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/../../connection/db.php';
require __DIR__ . '/../auth/check_auth.php';

// Vérifie que l'utilisateur est admin
requireAdmin();

// Vérifie la méthode HTTP (POST ou DELETE)
if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'DELETE'])) {
    http_response_code(405);
    echo json_encode(["success" => false, "error" => "Méthode non autorisée"]);
    exit;
}

// Récupère les données selon la méthode HTTP
$input = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jsonInput = json_decode(file_get_contents('php://input'), true);
    $input = $jsonInput ?: $_POST;
} else {
    $input = json_decode(file_get_contents('php://input'), true);
}

// Validation: ID requis
if (!$input || empty($input['id'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "ID du produit requis"]);
    exit;
}

$productId = intval($input['id']);

try {
    // Vérifier si le produit existe
    $checkProduct = $pdo->prepare("SELECT id, name, image FROM items WHERE id = ?");
    $checkProduct->execute([$productId]);
    $product = $checkProduct->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        http_response_code(404);
        echo json_encode(["success" => false, "error" => "Produit non trouvé"]);
        exit;
    }

    // Vérifier si le produit est dans un panier actif
    $checkCart = $pdo->prepare("
        SELECT COUNT(*) AS count
        FROM cart_items ci
        JOIN carts c ON ci.cart_id = c.id
        WHERE ci.item_id = ? AND c.status = 'active'
    ");
    $checkCart->execute([$productId]);
    $cartCount = (int) $checkCart->fetchColumn();

    // Empêche la suppression si le produit est dans des paniers actifs
    if ($cartCount > 0) {
        http_response_code(400);
        echo json_encode([
            "success" => false, 
            "error" => "Impossible de supprimer : produit dans {$cartCount} panier(s) actif(s)"
        ]);
        exit;
    }

    // Supprimer les images physiques du produit
    $uploadDir = __DIR__ . '/../../uploads/products/';
    $deletedImages = [];

    if (is_dir($uploadDir)) {
        // Trouve toutes les images du produit (pattern: {id}_*.ext)
        $images = glob($uploadDir . $productId . '_*.{jpg,jpeg,png,webp}', GLOB_BRACE);
        foreach ($images as $imagePath) {
            if (is_file($imagePath)) {
                $filename = basename($imagePath);
                if (unlink($imagePath)) {
                    $deletedImages[] = $filename;
                }
            }
        }
    }

    // Supprimer le produit de la base de données
    // Les relations (catégories, paniers) sont supprimées automatiquement (CASCADE)
    $deleteStmt = $pdo->prepare("DELETE FROM items WHERE id = ?");
    $deleteStmt->execute([$productId]);

    // Vérifie que la suppression a réussi
    if ($deleteStmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "error" => "Aucun produit supprimé"]);
        exit;
    }

    // Réponse succès
    echo json_encode([
        "success" => true,
        "message" => "Produit \"{$product['name']}\" supprimé avec succès",
        "deleted_id" => $productId,
        "deleted_images_count" => count($deletedImages)
    ]);

} catch (Exception $e) {
    // Gestion des erreurs
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}