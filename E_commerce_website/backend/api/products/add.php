<?php
/**
 * API - Ajout d'un nouveau produit
 * Méthode: POST
 * Authentification: Admin uniquement
 * Gère l'upload d'image principale et d'images supplémentaires
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

// Récupère les données du formulaire (multipart/form-data)
$input = $_POST;

// Validation des champs obligatoires
if (empty($input['name']) || empty($input['price']) || empty($input['brand_id'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Nom, prix et marque sont requis"]);
    exit;
}

try {
    // Création du dossier d'upload si nécessaire
    $uploadDir = __DIR__ . '/../../uploads/products/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Upload de l'image principale
    $mainImageUrl = null;

    if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        
        // Validation du format et de la taille (max 2MB)
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp']) && $_FILES['image']['size'] <= 2 * 1024 * 1024) {
            // Nom temporaire en attendant l'ID du produit
            $tempName = 'temp_' . time() . '.' . $ext;
            $targetPath = $uploadDir . $tempName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $mainImageUrl = 'products/' . $tempName;
            } else {
                error_log("Failed to move uploaded file: " . $_FILES['image']['tmp_name'] . " to " . $targetPath);
            }
        } else {
            error_log("Invalid file extension or size: " . $ext . " - " . $_FILES['image']['size']);
        }
    } elseif (!empty($_FILES['image'])) {
        error_log("Upload error code: " . $_FILES['image']['error']);
    }

    // Insertion du produit dans la base de données
    $sql = "INSERT INTO items (name, description, price, image, brand_id, quantity, sex, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $input['name'],
        $input['description'] ?? null,
        $input['price'],
        $mainImageUrl,
        $input['brand_id'],
        $input['quantity'] ?? 0,
        $input['sex'] ?? 'unisex'
    ]);

    $productId = $pdo->lastInsertId();

    // Renommer l'image principale avec l'ID du produit
    if ($mainImageUrl) {
        $oldPath = __DIR__ . '/../../' . $mainImageUrl;
        $ext = pathinfo($oldPath, PATHINFO_EXTENSION);
        $newFilename = $productId . '_main.' . $ext;
        $newPath = $uploadDir . $newFilename;
        
        if (rename($oldPath, $newPath)) {
            $mainImageUrl = 'products/' . $newFilename;
            
            // Mettre à jour l'URL dans la base
            $stmt = $pdo->prepare("UPDATE items SET image = ? WHERE id = ?");
            $stmt->execute([$mainImageUrl, $productId]);
        }
    }

    // Upload des images supplémentaires
    $additionalImages = [];

    if (!empty($_FILES['images']) && is_array($_FILES['images']['name'])) {
        $index = 1;
        
        foreach ($_FILES['images']['name'] as $i => $name) {
            // Validation de chaque fichier
            if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) continue;
            if (!is_uploaded_file($_FILES['images']['tmp_name'][$i])) continue;

            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) continue;
            if ($_FILES['images']['size'][$i] > 2 * 1024 * 1024) continue;

            // Nommage: {product_id}_{index}.{ext}
            $filename = $productId . '_' . $index . '.' . $ext;
            $targetPath = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $targetPath)) {
                $additionalImages[] = 'products/' . $filename;
                $index++;
            }
        }
    }

    // Ajout des catégories (relation many-to-many)
    if (isset($input['categories'])) {
        $categories = [];
        
        // Support de plusieurs formats (string CSV ou array)
        if (is_string($input['categories'])) {
            $categories = array_filter(array_map('intval', explode(',', $input['categories'])));
        } elseif (is_array($input['categories'])) {
            $categories = array_map('intval', $input['categories']);
        }
        
        if (!empty($categories)) {
            $insert = $pdo->prepare("INSERT INTO item_categories (item_id, category_id) VALUES (?, ?)");
            foreach ($categories as $catId) {
                $insert->execute([$productId, $catId]);
            }
        }
    }

    // Réponse succès
    echo json_encode([
        "success" => true,
        "message" => "Produit ajouté avec succès",
        "product_id" => $productId,
        "main_image_url" => $mainImageUrl,
        "additional_images_count" => count($additionalImages),
        "additional_images" => $additionalImages
    ]);

} catch (Exception $e) {
    // Gestion des erreurs
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}