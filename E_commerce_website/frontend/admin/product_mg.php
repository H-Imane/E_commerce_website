<?php
// Admin Product Management - CRUD interface for products with image uploads
// This view provides add/edit/delete for products with main image + gallery (up to 5 images)
require_once __DIR__ . '/auth_check.php';

// Check admin authentication and get user data
$adminUser = getAdminUser();

// Load config for API/uploads base URLs
include_once __DIR__ . '/../config/conf.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - Admin</title>
    <link rel="stylesheet" href="<?php echo $baseUrl . '/assets/css/admin_navbar.css'; ?>">
    <link rel="stylesheet" href=<?php echo $baseUrl . '/assets/css/product_mg.css'; ?>>
</head>
<body>

<?php include __DIR__ . '/../components/admin_navbar.php'; ?>

<div class="container">
    <div class="header">
        <h1>Product Management</h1>
        <button class="btn btn-primary" onclick="openAddModal()">
            + Add New Product
        </button>
    </div>

    <div class="products-table">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Brand</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Populated by JS -->
            </tbody>
        </table>
    </div>
</div>

<script src="<?php echo $baseUrl . '/assets/js/admin_navbar.js'; ?>"></script>

<!-- Add/Edit Product Modal -->
<div id="productModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Add New Product</h2>
            <button class="close-btn" onclick="closeModal()">&times;</button>
        </div>
        <form id="productForm" enctype="multipart/form-data" onsubmit="handleFormSubmit(event)">
            <div class="modal-body">
                <input type="hidden" id="productId" name="id">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="productName">Product Name *</label>
                        <input type="text" id="productName" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="productPrice">Price *</label>
                        <input type="number" id="productPrice" name="price" step="0.01" min="0" required>
                    </div>

                    <div class="form-group full-width">
                        <label for="productDescription">Description</label>
                        <textarea id="productDescription" name="description"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="productBrand">Brand</label>
                        <select id="productBrand" name="brand_id">
                            <option value="">-- Select Brand --</option>
                            <!-- Populated by JS -->
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="productQuantity">Stock Quantity *</label>
                        <input type="number" id="productQuantity" name="quantity" min="0" value="0" required>
                    </div>

                    <div class="form-group">
                        <label for="productSex">Gender</label>
                        <select id="productSex" name="sex">
                            <option value="unisex">Unisex</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="productCategories">Categories</label>
                        <select id="productCategories" name="categories[]" multiple size="5">
                            <!-- Populated by JS -->
                        </select>
                        <small style="color: #64748b;">Hold Ctrl/Cmd to select multiple</small>
                    </div>

                    <!-- Main Image Upload -->
                    <div class="form-group full-width">
                        <label>Main Product Image *</label>
                        <div class="upload-section">
                            <input type="file" id="mainImage" name="image" accept="image/*" onchange="previewMainImage(event)">
                            <label for="mainImage" class="upload-label">
                                📷 Click to upload main image
                            </label>
                            <p style="margin-top: 0.5rem; color: #64748b; font-size: 0.875rem;">
                                Recommended: 800x800px, Max 5MB
                            </p>
                            <div id="mainImagePreview" class="preview-container"></div>
                        </div>
                    </div>

                    <!-- Gallery Images Upload -->
                    <div class="form-group full-width">
                        <label>Product Gallery (Max 5 images)</label>
                        <div class="upload-section">
                            <input type="file" id="galleryImages" name="images[]" accept="image/*" multiple onchange="previewGalleryImages(event)">
                            <label for="galleryImages" class="upload-label">
                                🖼️ Click to upload gallery images
                            </label>
                            <p style="margin-top: 0.5rem; color: #64748b; font-size: 0.875rem;">
                                Select up to 5 images for the product gallery
                            </p>
                            <div id="galleryPreview" class="preview-container"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Product</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h2>Confirm Delete</h2>
            <button class="close-btn" onclick="closeDeleteModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete "<strong id="deleteProductName"></strong>"?</p>
            <p style="color: #dc2626; margin-top: 1rem;">This action cannot be undone.</p>
            <input type="hidden" id="deleteProductId">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
            <button type="button" class="btn btn-danger" onclick="deleteProduct()">Delete</button>
        </div>
    </div>
</div>

<script><?php echo "let ApiBaseUrl = '" . $apiBaseUrl . "';"; ?></script>
<script><?php echo "const baseUrl = '" . $baseUrl . "';"; ?></script>
<script src="<?php echo $baseUrl . '/assets/js/product_mg.js'; ?>"></script>

</body>
</html>
