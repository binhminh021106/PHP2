<?php

class ShopModel extends Model
{
    /**
     * Lấy danh sách sản phẩm với bộ lọc, tìm kiếm, phân trang và sắp xếp
     */
    public function getFilteredProducts($filters, $limit, $offset)
    {
        $conn = $this->connect();
        
        $sql = "SELECT p.*, c.name as category_name, b.name as brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.deleted_at IS NULL AND p.status = 'active'";

        $params = [];

        // 1. Tìm kiếm theo tên
        if (!empty($filters['search'])) {
            $sql .= " AND p.name LIKE :search";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        // 2. Lọc theo danh mục
        if (!empty($filters['category'])) {
            $sql .= " AND p.category_id = :category";
            $params['category'] = $filters['category'];
        }

        // 3. Lọc theo giá (Lấy giá khuyến mãi nếu có, không có thì lấy giá gốc)
        if (!empty($filters['min_price'])) {
            $sql .= " AND IF(p.price_sale > 0, p.price_sale, p.price_regular) >= :min_price";
            $params['min_price'] = $filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $sql .= " AND IF(p.price_sale > 0, p.price_sale, p.price_regular) <= :max_price";
            $params['max_price'] = $filters['max_price'];
        }

        // 4. Sắp xếp
        $sort = $filters['sort'] ?? 'newest';
        switch ($sort) {
            case 'price_asc':
                $sql .= " ORDER BY IF(p.price_sale > 0, p.price_sale, p.price_regular) ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY IF(p.price_sale > 0, p.price_sale, p.price_regular) DESC";
                break;
            case 'name_asc':
                $sql .= " ORDER BY p.name ASC";
                break;
            case 'name_desc':
                $sql .= " ORDER BY p.name DESC";
                break;
            default: // newest
                $sql .= " ORDER BY p.id DESC";
                break;
        }

        // 5. Phân trang
        $sql .= " LIMIT :limit OFFSET :offset";

        $stmt = $conn->prepare($sql);
        
        // Bind params
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Đếm tổng số sản phẩm để làm phân trang
     */
    public function getTotalProducts($filters)
    {
        $conn = $this->connect();
        
        $sql = "SELECT COUNT(p.id) as total 
                FROM products p 
                WHERE p.deleted_at IS NULL AND p.status = 'active'";

        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND p.name LIKE :search";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['category'])) {
            $sql .= " AND p.category_id = :category";
            $params['category'] = $filters['category'];
        }
        if (!empty($filters['min_price'])) {
            $sql .= " AND IF(p.price_sale > 0, p.price_sale, p.price_regular) >= :min_price";
            $params['min_price'] = $filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $sql .= " AND IF(p.price_sale > 0, p.price_sale, p.price_regular) <= :max_price";
            $params['max_price'] = $filters['max_price'];
        }

        $stmt = $conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    /**
     * Lấy danh sách Category để làm thanh menu Sidebar
     */
    public function getCategories()
    {
        $sql = "SELECT * FROM categories WHERE deleted_at IS NULL";
        return $this->connect()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách sản phẩm theo mảng ID (Dành cho tính năng So sánh)
     */
    public function getProductsByIds($ids)
    {
        if (empty($ids)) return [];
        
        $conn = $this->connect();
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        $sql = "SELECT p.*, c.name as category_name, b.name as brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.id IN ($placeholders) AND p.deleted_at IS NULL";
                
        $stmt = $conn->prepare($sql);
        $stmt->execute(array_values($ids));
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}