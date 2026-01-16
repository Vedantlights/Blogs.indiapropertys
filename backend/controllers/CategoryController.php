<?php
/**
 * Category Controller
 * 
 * Handles category-related API requests
 */

require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../helpers/Response.php';
require_once __DIR__ . '/../helpers/Validator.php';

class CategoryController {
    private $category;
    
    public function __construct() {
        $this->category = new Category();
    }
    
    /**
     * Get all active categories
     * GET /api/categories
     */
    public function getActive() {
        try {
            $categories = $this->category->getActive();
            Response::success($categories, 'Categories retrieved successfully');
        } catch (PDOException $e) {
            error_log("CategoryController::getActive Database Error: " . $e->getMessage());
            Response::error('Database error: ' . $e->getMessage(), 500);
        } catch (Exception $e) {
            error_log("CategoryController::getActive Error: " . $e->getMessage());
            Response::error('Failed to retrieve categories: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get all categories (Admin)
     * GET /api/admin/categories
     */
    public function getAll() {
        try {
            $categories = $this->category->getAll();
            Response::success($categories, 'Categories retrieved successfully');
        } catch (Exception $e) {
            error_log("CategoryController::getAll Error: " . $e->getMessage());
            Response::error('Failed to retrieve categories', 500);
        }
    }
    
    /**
     * Create new category (Admin)
     * POST /api/admin/categories
     */
    public function create() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                Response::error('Invalid JSON data', 400);
            }
            
            $validation = Validator::validateCategory($input, false);
            
            if (!$validation['valid']) {
                Response::error('Validation failed', 400, $validation['errors']);
            }
            
            $data = $validation['sanitized'];
            
            // Check slug uniqueness
            if ($this->category->slugExists($data['slug'])) {
                Response::error('Slug already exists', 409, ['slug' => 'This slug is already in use']);
            }
            
            $id = $this->category->create($data);
            
            if ($id === false) {
                Response::error('Failed to create category', 500);
            }
            
            $category = $this->category->getById($id);
            Response::success($category, 'Category created successfully', 201);
        } catch (Exception $e) {
            error_log("CategoryController::create Error: " . $e->getMessage());
            Response::error('Failed to create category', 500);
        }
    }
    
    /**
     * Update category (Admin)
     * PUT /api/admin/categories/{id}
     */
    public function update($id) {
        try {
            if (empty($id) || !is_numeric($id)) {
                Response::error('Invalid category ID', 400);
            }
            
            $category = $this->category->getById($id);
            
            if (!$category) {
                Response::error('Category not found', 404);
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                Response::error('Invalid JSON data', 400);
            }
            
            $validation = Validator::validateCategory($input, true);
            
            if (!$validation['valid']) {
                Response::error('Validation failed', 400, $validation['errors']);
            }
            
            $data = $validation['sanitized'];
            
            // Check slug uniqueness if slug is being updated
            if (isset($data['slug']) && $this->category->slugExists($data['slug'], $id)) {
                Response::error('Slug already exists', 409, ['slug' => 'This slug is already in use']);
            }
            
            $success = $this->category->update($id, $data);
            
            if (!$success) {
                Response::error('Failed to update category', 500);
            }
            
            $updatedCategory = $this->category->getById($id);
            Response::success($updatedCategory, 'Category updated successfully');
        } catch (Exception $e) {
            error_log("CategoryController::update Error: " . $e->getMessage());
            Response::error('Failed to update category', 500);
        }
    }
    
    /**
     * Delete category (Admin)
     * DELETE /api/admin/categories/{id}
     */
    public function delete($id) {
        try {
            if (empty($id) || !is_numeric($id)) {
                Response::error('Invalid category ID', 400);
            }
            
            $category = $this->category->getById($id);
            
            if (!$category) {
                Response::error('Category not found', 404);
            }
            
            $success = $this->category->delete($id);
            
            if (!$success) {
                Response::error('Failed to delete category', 500);
            }
            
            Response::success(null, 'Category deleted successfully');
        } catch (Exception $e) {
            error_log("CategoryController::delete Error: " . $e->getMessage());
            Response::error('Failed to delete category', 500);
        }
    }
}
