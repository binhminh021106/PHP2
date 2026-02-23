<?php

class WishlistModel extends Model
{
    private $table = 'wishlists';

    /**
     * Lấy danh sách yêu thích của người dùng
     */
    public function getWishlistByUser($userId)
    {
        $sql = "SELECT w.product_id as wishlist_product_id, p.*, c.name as category_name
                FROM {$this->table} w
                JOIN products p ON w.product_id = p.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE w.user_id = :user_id AND p.deleted_at IS NULL
                ORDER BY w.created_at DESC";
        
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Thêm sản phẩm vào wishlist
     */
    public function add($userId, $productId)
    {
        $conn = $this->connect();

        // Kiểm tra xem đã có trong wishlist chưa
        $checkSql = "SELECT id FROM {$this->table} WHERE user_id = :user_id AND product_id = :product_id";
        $stmtCheck = $conn->prepare($checkSql);
        $stmtCheck->execute([
            'user_id' => $userId,
            'product_id' => $productId
        ]);

        if ($stmtCheck->fetch()) {
            return false; // Đã tồn tại
        }

        // Thêm mới
        $sql = "INSERT INTO {$this->table} (user_id, product_id) VALUES (:user_id, :product_id)";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            'user_id' => $userId,
            'product_id' => $productId
        ]);
    }

    /**
     * Xóa sản phẩm khỏi wishlist
     */
    public function remove($userId, $productId)
    {
        $sql = "DELETE FROM {$this->table} WHERE user_id = :user_id AND product_id = :product_id";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            'user_id' => $userId,
            'product_id' => $productId
        ]);
    }
}