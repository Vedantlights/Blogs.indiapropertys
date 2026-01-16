<?php
/**
 * Blog Controller
 * 
 * Handles blog-related API requests
 */

require_once __DIR__ . '/../models/Blog.php';
require_once __DIR__ . '/../helpers/Response.php';
require_once __DIR__ . '/../helpers/Validator.php';

class BlogController {
    private $blog;
    
    public function __construct() {
        $this->blog = new Blog();
    }
    
    /**
     * Get all published blogs
     * GET /api/blogs
     */
    public function getPublishedBlogs() {
        try {
            $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $perPage = isset($_GET['per_page']) ? max(1, min(100, (int)$_GET['per_page'])) : 10;
            $categorySlug = $_GET['category'] ?? null;
            $featured = isset($_GET['featured']) ? filter_var($_GET['featured'], FILTER_VALIDATE_BOOLEAN) : null;
            
            $blogs = $this->blog->getPublishedBlogs($page, $perPage, $categorySlug, $featured);
            $total = $this->blog->getPublishedBlogsCount($categorySlug, $featured);
            
            Response::paginated($blogs, $page, $perPage, $total, 'Blogs retrieved successfully');
        } catch (PDOException $e) {
            error_log("BlogController::getPublishedBlogs Database Error: " . $e->getMessage());
            Response::error('Database error: ' . $e->getMessage(), 500);
        } catch (Exception $e) {
            error_log("BlogController::getPublishedBlogs Error: " . $e->getMessage());
            Response::error('Failed to retrieve blogs: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get single blog by slug
     * GET /api/blogs/{slug}
     */
    public function getBySlug($slug) {
        try {
            if (empty($slug)) {
                Response::error('Slug is required', 400);
            }
            
            $blog = $this->blog->getBySlug($slug);
            
            if (!$blog) {
                Response::error('Blog not found', 404);
            }
            
            // Increment views count
            $this->blog->incrementViews($blog['id']);
            
            // Update views_count in response
            $blog['views_count'] = $blog['views_count'] + 1;
            
            Response::success($blog, 'Blog retrieved successfully');
        } catch (Exception $e) {
            error_log("BlogController::getBySlug Error: " . $e->getMessage());
            Response::error('Failed to retrieve blog', 500);
        }
    }
    
    /**
     * Create new blog (Admin)
     * POST /api/admin/blogs
     */
    public function create() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                Response::error('Invalid JSON data', 400);
            }
            
            $validation = Validator::validateBlog($input, false);
            
            if (!$validation['valid']) {
                Response::error('Validation failed', 400, $validation['errors']);
            }
            
            $data = $validation['sanitized'];
            
            // Check slug uniqueness
            if ($this->blog->slugExists($data['slug'])) {
                Response::error('Slug already exists', 409, ['slug' => 'This slug is already in use']);
            }
            
            $id = $this->blog->create($data);
            
            if ($id === false) {
                Response::error('Failed to create blog', 500);
            }
            
            $blog = $this->blog->getById($id);
            Response::success($blog, 'Blog created successfully', 201);
        } catch (Exception $e) {
            error_log("BlogController::create Error: " . $e->getMessage());
            Response::error('Failed to create blog', 500);
        }
    }
    
    /**
     * Update blog (Admin)
     * PUT /api/admin/blogs/{id}
     */
    public function update($id) {
        try {
            if (empty($id) || !is_numeric($id)) {
                Response::error('Invalid blog ID', 400);
            }
            
            $blog = $this->blog->getById($id);
            
            if (!$blog) {
                Response::error('Blog not found', 404);
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                Response::error('Invalid JSON data', 400);
            }
            
            $validation = Validator::validateBlog($input, true);
            
            if (!$validation['valid']) {
                Response::error('Validation failed', 400, $validation['errors']);
            }
            
            $data = $validation['sanitized'];
            
            // Check slug uniqueness if slug is being updated
            if (isset($data['slug']) && $this->blog->slugExists($data['slug'], $id)) {
                Response::error('Slug already exists', 409, ['slug' => 'This slug is already in use']);
            }
            
            $success = $this->blog->update($id, $data);
            
            if (!$success) {
                Response::error('Failed to update blog', 500);
            }
            
            $updatedBlog = $this->blog->getById($id);
            Response::success($updatedBlog, 'Blog updated successfully');
        } catch (Exception $e) {
            error_log("BlogController::update Error: " . $e->getMessage());
            Response::error('Failed to update blog', 500);
        }
    }
    
    /**
     * Delete blog (Admin)
     * DELETE /api/admin/blogs/{id}
     */
    public function delete($id) {
        try {
            if (empty($id) || !is_numeric($id)) {
                Response::error('Invalid blog ID', 400);
            }
            
            $blog = $this->blog->getById($id);
            
            if (!$blog) {
                Response::error('Blog not found', 404);
            }
            
            $success = $this->blog->delete($id);
            
            if (!$success) {
                Response::error('Failed to delete blog', 500);
            }
            
            Response::success(null, 'Blog deleted successfully');
        } catch (Exception $e) {
            error_log("BlogController::delete Error: " . $e->getMessage());
            Response::error('Failed to delete blog', 500);
        }
    }
    
    /**
     * Get all blogs (Admin)
     * GET /api/admin/blogs
     */
    public function getAll() {
        try {
            $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $perPage = isset($_GET['per_page']) ? max(1, min(100, (int)$_GET['per_page'])) : 20;
            
            $blogs = $this->blog->getAll($page, $perPage);
            $total = $this->blog->getTotalCount();
            
            Response::paginated($blogs, $page, $perPage, $total, 'Blogs retrieved successfully');
        } catch (Exception $e) {
            error_log("BlogController::getAll Error: " . $e->getMessage());
            Response::error('Failed to retrieve blogs', 500);
        }
    }
}
