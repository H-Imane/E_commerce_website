<?php
/**
 * API - Mise à jour d'une catégorie
 * Méthode: POST
 * Authentification: Admin uniquement
 * Paramètres: id (GET ou body), name (body)
 */

header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/../../connection/db.php';
require __DIR__ . '/../auth/check_auth.php';

// Vérifie que l'utilisateur est admin
requireAdmin();

// Vérifie la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "error" => "Méthode POST uniquement"]);
    exit;
}

// Récupère l'ID depuis l'URL ou le body
$categoryId = isset($_GET['id']) ? intval($_GET['id']) : null;
$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

if (!$categoryId && isset($input['id'])) {
    $categoryId = intval($input['id']);
}

// Validation: ID requis
if (!$categoryId) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "ID de la catégorie requis"]);
    exit;
}

// Validation: nom requis
if (empty($input['name'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Le nouveau nom de la catégorie est requis"]);
    exit;
}

$name = trim($input['name']);

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

    // Vérifie si le nouveau nom n'est pas déjà pris par une autre catégorie
    $duplicateStmt = $pdo->prepare("SELECT id FROM categories WHERE name = ? AND id != ?");
    $duplicateStmt->execute([$name, $categoryId]);
    
    if ($duplicateStmt->fetch()) {
        http_response_code(409);
        echo json_encode(["success" => false, "error" => "Ce nom de catégorie est déjà utilisé"]);
        exit;
    }

    // Met à jour la catégorie
    $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
    $stmt->execute([$name, $categoryId]);

    // Retourne la catégorie mise à jour
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "message" => "Catégorie mise à jour avec succès",
        "id" => $categoryId,
        "old_name" => $category['name'],
        "new_name" => $name
    ]);

} catch (Exception $e) {
    // Gestion des erreurs
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}