<?php
// Admin Category & Brand Management
// Centralized CRUD interface for categories and brands
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
    <title>Category & Brand Management - Admin</title>
    <link rel="stylesheet" href="<?php echo $baseUrl . '/assets/css/admin_navbar.css'; ?>">
    <link rel="stylesheet" href="<?php echo $baseUrl . '/assets/css/catbrand_mg.css'; ?>">
</head>
<body>

<?php include __DIR__ . '/../components/admin_navbar.php'; ?>

<div class="container">
    <div class="header">
        <h1>Category & Brand Management</h1>
    </div>

    <div class="management-grid">
        <!-- Categories Section -->
        <div class="section-card">
            <div class="section-header">
                <h2>Categories</h2>
                <button class="btn btn-primary btn-small" onclick="openAddCategoryModal()">
                    + Add Category
                </button>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Populated by JS -->
                </tbody>
            </table>
        </div>

        <!-- Brands Section -->
        <div class="section-card">
            <div class="section-header">
                <h2>Brands</h2>
                <button class="btn btn-primary btn-small" onclick="openAddBrandModal()">
                    + Add Brand
                </button>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Populated by JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Category Add/Edit Modal -->
<div id="categoryModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="categoryModalTitle">Add New Category</h2>
            <button class="close-btn" onclick="closeCategoryModal()">&times;</button>
        </div>
        <form id="categoryForm" onsubmit="handleCategorySubmit(event)">
            <div class="modal-body">
                <input type="hidden" id="categoryId" name="id">
                
                <div class="form-group">
                    <label for="categoryName">Category Name *</label>
                    <input type="text" id="categoryName" name="name" required>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeCategoryModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Category</button>
            </div>
        </form>
    </div>
</div>

<!-- Category Delete Confirmation Modal -->
<div id="deleteCategoryModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Confirm Delete Category</h2>
            <button class="close-btn" onclick="closeDeleteCategoryModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete the category "<strong id="deleteCategoryName"></strong>"?</p>
            <p style="color: #dc2626; margin-top: 1rem;">This action cannot be undone and may affect products in this category.</p>
            <input type="hidden" id="deleteCategoryId">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteCategoryModal()">Cancel</button>
            <button type="button" class="btn btn-danger" onclick="deleteCategory()">Delete</button>
        </div>
    </div>
</div>

<!-- Brand Add/Edit Modal -->
<div id="brandModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="brandModalTitle">Add New Brand</h2>
            <button class="close-btn" onclick="closeBrandModal()">&times;</button>
        </div>
        <form id="brandForm" onsubmit="handleBrandSubmit(event)">
            <div class="modal-body">
                <input type="hidden" id="brandId" name="id">
                
                <div class="form-group">
                    <label for="brandName">Brand Name *</label>
                    <input type="text" id="brandName" name="name" required>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeBrandModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Brand</button>
            </div>
        </form>
    </div>
</div>

<!-- Brand Delete Confirmation Modal -->
<div id="deleteBrandModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Confirm Delete Brand</h2>
            <button class="close-btn" onclick="closeDeleteBrandModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete the brand "<strong id="deleteBrandName"></strong>"?</p>
            <p style="color: #dc2626; margin-top: 1rem;">This action cannot be undone and may affect products from this brand.</p>
            <input type="hidden" id="deleteBrandId">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteBrandModal()">Cancel</button>
            <button type="button" class="btn btn-danger" onclick="deleteBrand()">Delete</button>
        </div>
    </div>
</div>

<!-- Load JS Components -->
<script src="<?php echo $baseUrl . '/assets/js/components/modal.js'; ?>"></script>
<script><?php echo "let ApiBaseUrl = '" . $apiBaseUrl . "';"; ?></script>
<script><?php echo "const baseUrl = '" . $baseUrl . "';"; ?></script>
<script src="<?php echo $baseUrl . '/assets/js/catbrand_mg.js'; ?>"></script>
<script src="<?php echo $baseUrl . '/assets/js/admin_navbar.js'; ?>"></script>

</body>
</html>
