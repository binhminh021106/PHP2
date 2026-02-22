<?php

class CartModel extends \Model
{
    private $tableCarts = 'carts';
    private $tableCartItems = 'cart_items';

    /**
     * TÌM GIỎ HÀNG (Dựa vào user_id hoặc session_id)
     */
    public function getCart($userId = null, $sessionId = null)
    {
        $conn = $this->connect();
        
        if ($userId) {
            $sql = "SELECT * FROM $this->tableCarts WHERE user_id = :user_id LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
        } else {
            $sql = "SELECT * FROM $this->tableCarts WHERE session_id = :session_id LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['session_id' => $sessionId]);
        }
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * TẠO GIỎ HÀNG MỚI (Trả về ID của giỏ hàng vừa tạo)
     */
    public function createCart($userId = null, $sessionId = null)
    {
        $sql = "INSERT INTO $this->tableCarts (user_id, session_id) VALUES (:user_id, :session_id)";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'session_id' => $sessionId
        ]);
        
        // Trả về ID của giỏ hàng vừa được insert thành công
        return $conn->lastInsertId();
    }

    /**
     * LẤY DANH SÁCH SẢN PHẨM TRONG GIỎ HÀNG (JOIN CÁC BẢNG)
     * Đây là hàm quan trọng nhất để hiển thị ra màn hình View Cart
     */
    public function getCartItems($cartId)
    {
        $sql = "SELECT 
                    ci.id as cart_item_id,
                    ci.quantity,
                    p.id as product_id,
                    p.name as product_name,
                    p.slug,
                    COALESCE(pv.image, p.img_thumbnail) as image, -- Ưu tiên ảnh variant, không có lấy ảnh product
                    pv.sku,
                    pv.price,
                    pv.attributes -- Dữ liệu JSON (Size, Color)
                FROM $this->tableCartItems ci
                JOIN products p ON ci.product_id = p.id
                JOIN product_variants pv ON ci.product_variant_id = pv.id
                WHERE ci.cart_id = :cart_id";

        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute(['cart_id' => $cartId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * THÊM SẢN PHẨM VÀO GIỎ HÀNG
     * Logic: Nếu đã tồn tại biến thể này -> Cộng dồn số lượng. Nếu chưa -> Thêm mới.
     */
    public function addToCart($cartId, $productId, $variantId, $quantity)
    {
        $conn = $this->connect();

        // 1. Kiểm tra xem sản phẩm (biến thể này) đã có trong giỏ chưa
        $sqlCheck = "SELECT id, quantity FROM $this->tableCartItems WHERE cart_id = :cart_id AND product_variant_id = :variant_id";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->execute([
            'cart_id' => $cartId,
            'variant_id' => $variantId
        ]);
        
        $existingItem = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($existingItem) {
            // 2. NẾU CÓ RỒI: Update cộng dồn số lượng
            $newQuantity = $existingItem['quantity'] + $quantity;
            $sqlUpdate = "UPDATE $this->tableCartItems SET quantity = :quantity WHERE id = :id";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            return $stmtUpdate->execute([
                'quantity' => $newQuantity,
                'id' => $existingItem['id']
            ]);
        } else {
            // 3. NẾU CHƯA CÓ: Insert dòng mới
            $sqlInsert = "INSERT INTO $this->tableCartItems (cart_id, product_id, product_variant_id, quantity) 
                          VALUES (:cart_id, :product_id, :variant_id, :quantity)";
            $stmtInsert = $conn->prepare($sqlInsert);
            return $stmtInsert->execute([
                'cart_id' => $cartId,
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $quantity
            ]);
        }
    }

    /**
     * CẬP NHẬT SỐ LƯỢNG (Khi khách bấm + / - ở trang Giỏ hàng)
     */
    public function updateQuantity($cartItemId, $quantity)
    {
        $sql = "UPDATE $this->tableCartItems SET quantity = :quantity WHERE id = :id";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            'quantity' => $quantity,
            'id' => $cartItemId
        ]);
    }

    /**
     * XÓA 1 SẢN PHẨM KHỎI GIỎ HÀNG
     */
    public function removeCartItem($cartItemId)
    {
        $sql = "DELETE FROM $this->tableCartItems WHERE id = :id";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        return $stmt->execute(['id' => $cartItemId]);
    }
    
    /**
     * XÓA TOÀN BỘ GIỎ HÀNG (Sau khi đặt hàng thành công)
     */
    public function clearCart($cartId)
    {
        $sql = "DELETE FROM $this->tableCartItems WHERE cart_id = :cart_id";
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        return $stmt->execute(['cart_id' => $cartId]);
    }
}