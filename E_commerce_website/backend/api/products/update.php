<?php
/**
 * API - Mise à jour d'un produit existant
 * Méthode: POST
 * Authentification: Admin uniquement
 * Paramètres: id (GET ou POST)
 * Permet de mettre à jour les informations, images et catégories
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

// Récupère l'ID du produit depuis l'URL ou le body
$productId = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : null);

// Validation: ID requis
if (!$productId) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "ID du produit requis"]);
    exit;
}

try {
    // Vérifier que le produit existe
    $stmt = $pdo->prepare("SELECT id, image FROM items WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if (!$product) {
        http_response_code(404);
        echo json_encode(["success" => false, "error" => "Produit non trouvé"]);
        exit;
    }

    // Récupère les données du formulaire
    $input = $_POST;
    $uploadDir = __DIR__ . '/../../uploads/products/';
    
    // Création du dossier d'upload si nécessaire
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Mise à jour des champs textuels
    $allowed = ['name', 'description', 'price', 'brand_id', 'quantity', 'sex'];
    $fields = [];
    $params = [];

    foreach ($allowed as $field) {
        if (isset($input[$field]) && $input[$field] !== '') {
            $fields[] = "$field = ?";
            $params[] = $input[$field];
        }
    }

    // Exécute la mise à jour si des champs sont modifiés
    if (!empty($fields)) {
        $params[] = $productId;
        $sql = "UPDATE items SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    }

    // Mise à jour de l'image principale
    $newMainImageUrl = null;

    if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        
        // Validation du format et de la taille (max 2MB)
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp']) && $_FILES['image']['size'] <= 2 * 1024 * 1024) {
            // Supprimer l'ancienne image si elle existe
            if ($product['image']) {
                $oldImagePath = __DIR__ . '/../../' . $product['image'];
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            
            // Sauvegarder la nouvelle image
            $filename = $productId . '_main.' . $ext;
            $targetPath = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $newMainImageUrl = 'products/' . $filename;
                $stmt = $pdo->prepare("UPDATE items SET image = ? WHERE id = ?");
                $stmt->execute([$newMainImageUrl, $productId]);
            }
        }
    }

    // Ajout d'images supplémentaires (sans supprimer les existantes)
    $additionalImages = [];

    if (!empty($_FILES['images']) && is_array($_FILES['images']['name'])) {
        // Compte les images existantes pour l'index
        $existingImages = glob($uploadDir . $productId . '_*.{jpg,jpeg,png,webp}', GLOB_BRACE);
        $existingImages = array_filter($existingImages, function($img) use ($productId) {
            return strpos($img, $productId . '_main.') === false;
        });
        
        $index = count($existingImages) + 1;
        
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

    // Mise à jour des catégories (remplace toutes les catégories existantes)
    $categoriesUpdated = false;

    if (isset($input['categories'])) {
        $categories = [];
        
        // Support de plusieurs formats (string CSV ou array)
        if (is_string($input['categories']) && trim($input['categories']) !== '') {
            $categories = array_filter(array_map('intval', explode(',', $input['categories'])));
        } elseif (is_array($input['categories'])) {
            $categories = array_map('intval', $input['categories']);
        }
        
        if (!empty($categories)) {
            // Supprime toutes les anciennes associations
            $pdo->prepare("DELETE FROM item_categories WHERE item_id = ?")->execute([$productId]);
            
            // Ajoute les nouvelles associations
            $insert = $pdo->prepare("INSERT INTO item_categories (item_id, category_id) VALUES (?, ?)");
            foreach ($categories as $catId) {
                $insert->execute([$productId, $catId]);
            }
            
            $categoriesUpdated = true;
        }
    }

    // Réponse succès
    echo json_encode([
        "success" => true,
        "message" => "Produit mis à jour avec succès",
        "product_id" => $productId,
        "fields_updated" => !empty($fields),
        "main_image_updated" => $newMainImageUrl !== null,
        "additional_images_uploaded" => count($additionalImages),
        "categories_updated" => $categoriesUpdated
    ]);

} catch (Exception $e) {
    // Gestion des erreurs
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}