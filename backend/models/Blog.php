<?php
/**
 * Blog Model
 * 
 * Handles all database operations for blogs
 */

require_once __DIR__ . '/../config/database.php';

class Blog {
    private $db;
    
    public function __construct() {
        $this->db = getDBConnection();
    }
    
    /**
     * Get all published blogs with pagination and filters
     * 
     * @param int $page Page number
     * @param int $perPage Items per page
     * @param string|null $categorySlug Category filter
     * @param bool|null $featured Featured filter
     * @return array
     */
    public function getPublishedBlogs($page = 1, $perPage = 10, $categorySlug = null, $featured = null) {
        $offset = ($page - 1) * $perPage;
        
        // Use direct query (works even if view doesn't exist)
        // Use COALESCE to prefer image_url, fallback to featured_image
        $sql = "SELECT 
                    b.id, b.title, b.slug, b.content, b.excerpt, 
                    COALESCE(b.image_url, b.featured_image) AS image_url,
                    b.featured_image,
                    b.is_featured, b.views_count, b.published_at, b.created_at, b.updated_at,
                    c.name AS category_name, c.slug AS category_slug, c.icon AS category_icon
                FROM blogs b
                LEFT JOIN categories c ON b.category_id = c.id
                WHERE b.is_published = TRUE";
        
        $params = [];
        
        if ($categorySlug !== null) {
            $sql .= " AND c.slug = :category_slug";
            $params[':category_slug'] = $categorySlug;
        }
        
        if ($featured !== null) {
            $sql .= " AND b.is_featured = :is_featured";
            $params[':is_featured'] = $featured ? 1 : 0;
        }
        
        $sql .= " ORDER BY b.published_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get total count of published blogs
     * 
     * @param string|null $categorySlug Category filter
     * @param bool|null $featured Featured filter
     * @return int
     */
    public function getPublishedBlogsCount($categorySlug = null, $featured = null) {
        // Use direct query (works even if view doesn't exist)
        $sql = "SELECT COUNT(*) as total FROM blogs b
                LEFT JOIN categories c ON b.category_id = c.id
                WHERE b.is_published = TRUE";
        
        $params = [];
        
        if ($categorySlug !== null) {
            $sql .= " AND c.slug = :category_slug";
            $params[':category_slug'] = $categorySlug;
        }
        
        if ($featured !== null) {
            $sql .= " AND b.is_featured = :is_featured";
            $params[':is_featured'] = $featured ? 1 : 0;
        }
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        
        $result = $stmt->fetch();
        return (int)$result['total'];
    }
    
    /**
     * Get single blog by slug
     * 
     * @param string $slug Blog slug
     * @return array|null
     */
    public function getBySlug($slug) {
        // Use direct query (works even if view doesn't exist)
        // Use COALESCE to prefer image_url, fallback to featured_image
        $sql = "SELECT 
                    b.id, b.title, b.slug, b.content, b.excerpt, 
                    COALESCE(b.image_url, b.featured_image) AS image_url,
                    b.featured_image,
                    b.is_featured, b.views_count, b.published_at, b.created_at, b.updated_at,
                    c.name AS category_name, c.slug AS category_slug, c.icon AS category_icon
                FROM blogs b
                LEFT JOIN categories c ON b.category_id = c.id
                WHERE b.slug = :slug AND b.is_published = TRUE
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':slug', $slug);
        $stmt->execute();
        
        return $stmt->fetch() ?: null;
    }
    
    /**
     * Get blog by ID (for admin operations)
     * 
     * @param int $id Blog ID
     * @return array|null
     */
    public function getById($id) {
        $sql = "SELECT b.*, c.name as category_name, c.slug as category_slug 
                FROM blogs b 
                LEFT JOIN categories c ON b.category_id = c.id 
                WHERE b.id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch() ?: null;
    }
    
    /**
     * Increment blog views using stored procedure
     * 
     * @param int $id Blog ID
     * @return bool
     */
    public function incrementViews($id) {
        try {
            $sql = "CALL sp_increment_blog_views(:id)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            // Fallback to direct update if stored procedure doesn't exist
            $sql = "UPDATE blogs SET views_count = views_count + 1 WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        }
    }
    
    /**
     * Check if slug exists
     * 
     * @param string $slug Blog slug
     * @param int|null $excludeId Blog ID to exclude (for updates)
     * @return bool
     */
    public function slugExists($slug, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM blogs WHERE slug = :slug";
        $params = [':slug' => $slug];
        
        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        
        $result = $stmt->fetch();
        return (int)$result['count'] > 0;
    }
    
    /**
     * Create new blog
     * 
     * @param array $data Blog data
     * @return int|false Blog ID on success, false on failure
     */
    public function create($data) {
        $sql = "INSERT INTO blogs (
            title, slug, content, excerpt, category_id,
            image_url, featured_image, is_featured, is_published,
            meta_title, meta_description, meta_keywords, published_at
        ) VALUES (
            :title, :slug, :content, :excerpt, :category_id,
            :image_url, :featured_image, :is_featured, :is_published,
            :meta_title, :meta_description, :meta_keywords, :published_at
        )";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindValue(':title', $data['title']);
        $stmt->bindValue(':slug', $data['slug']);
        $stmt->bindValue(':content', $data['content']);
        $stmt->bindValue(':excerpt', $data['excerpt'] ?? null);
        $stmt->bindValue(':category_id', $data['category_id'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':image_url', $data['image_url'] ?? null);
        $stmt->bindValue(':featured_image', $data['featured_image'] ?? null);
        $stmt->bindValue(':is_featured', $data['is_featured'] ?? false, PDO::PARAM_BOOL);
        $stmt->bindValue(':is_published', $data['is_published'] ?? false, PDO::PARAM_BOOL);
        $stmt->bindValue(':meta_title', $data['meta_title'] ?? null);
        $stmt->bindValue(':meta_description', $data['meta_description'] ?? null);
        $stmt->bindValue(':meta_keywords', $data['meta_keywords'] ?? null);
        
        // Set published_at if blog is published
        if (!empty($data['is_published']) && empty($data['published_at'])) {
            $stmt->bindValue(':published_at', date('Y-m-d H:i:s'));
        } else {
            $stmt->bindValue(':published_at', $data['published_at'] ?? null);
        }
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Update blog
     * 
     * @param int $id Blog ID
     * @param array $data Blog data
     * @return bool
     */
    public function update($id, $data) {
        $fields = [];
        $params = [':id' => $id];
        
        $allowedFields = [
            'title', 'slug', 'content', 'excerpt', 'category_id',
            'image_url', 'featured_image', 'is_featured', 'is_published',
            'meta_title', 'meta_description', 'meta_keywords', 'published_at'
        ];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        // Handle published_at logic
        if (isset($data['is_published']) && $data['is_published'] && !isset($data['published_at'])) {
            // Check if blog was previously unpublished
            $current = $this->getById($id);
            if ($current && empty($current['published_at'])) {
                $fields[] = "published_at = :published_at";
                $params[':published_at'] = date('Y-m-d H:i:s');
            }
        }
        
        $sql = "UPDATE blogs SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            if ($key === ':category_id') {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } elseif (in_array($key, [':is_featured', ':is_published'])) {
                $stmt->bindValue($key, $value, PDO::PARAM_BOOL);
            } else {
                $stmt->bindValue($key, $value);
            }
        }
        
        return $stmt->execute();
    }
    
    /**
     * Delete blog
     * 
     * @param int $id Blog ID
     * @return bool
     */
    public function delete($id) {
        $sql = "DELETE FROM blogs WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Get all blogs (for admin)
     * 
     * @param int $page Page number
     * @param int $perPage Items per page
     * @return array
     */
    public function getAll($page = 1, $perPage = 20) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT b.*, c.name as category_name, c.slug as category_slug 
                FROM blogs b 
                LEFT JOIN categories c ON b.category_id = c.id 
                ORDER BY b.created_at DESC 
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get total count of all blogs
     * 
     * @return int
     */
    public function getTotalCount() {
        $sql = "SELECT COUNT(*) as total FROM blogs";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return (int)$result['total'];
    }
}
