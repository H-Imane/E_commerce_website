<?php
/**
 * API - Ajout d'une nouvelle catégorie
 * Méthode: POST
 * Authentification: Admin uniquement
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

// Récupère les données JSON ou POST
$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

// Validation: nom requis
if (empty($input['name'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Le nom de la catégorie est requis"]);
    exit;
}

$name = trim($input['name']);

try {
    // Vérifie si la catégorie existe déjà
    $checkStmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
    $checkStmt->execute([$name]);
    
    if ($checkStmt->fetch()) {
        http_response_code(409);
        echo json_encode(["success" => false, "error" => "Cette catégorie existe déjà"]);
        exit;
    }

    // Insère la nouvelle catégorie
    $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->execute([$name]);

    $categoryId = $pdo->lastInsertId();

    // Retourne la catégorie créée
    http_response_code(201);
    echo json_encode([
        "success" => true,
        "message" => "Catégorie ajoutée avec succès",
        "id" => $categoryId,
        "name" => $name
    ]);

} catch (Exception $e) {
    // Gestion des erreurs
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}