<?php
/**
 * API - Mise à jour d'une marque
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
$brandId = isset($_GET['id']) ? intval($_GET['id']) : null;
$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

if (!$brandId && isset($input['id'])) {
    $brandId = intval($input['id']);
}

// Validation: ID requis
if (!$brandId) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "ID de la marque requis"]);
    exit;
}

// Validation: nom requis
if (empty($input['name'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Le nouveau nom de la marque est requis"]);
    exit;
}

$name = trim($input['name']);

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

    // Vérifie si le nouveau nom n'est pas déjà pris par une autre marque
    $duplicateStmt = $pdo->prepare("SELECT id FROM brands WHERE name = ? AND id != ?");
    $duplicateStmt->execute([$name, $brandId]);
    
    if ($duplicateStmt->fetch()) {
        http_response_code(409);
        echo json_encode(["success" => false, "error" => "Ce nom de marque est déjà utilisé"]);
        exit;
    }

    // Met à jour la marque
    $stmt = $pdo->prepare("UPDATE brands SET name = ? WHERE id = ?");
    $stmt->execute([$name, $brandId]);

    // Retourne la marque mise à jour
    echo json_encode([
        "success" => true,
        "message" => "Marque mise à jour avec succès",
        "id" => $brandId,
        "old_name" => $brand['name'],
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