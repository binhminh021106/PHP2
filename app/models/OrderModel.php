<?php

class OrderModel extends Model
{
    /**
     * Tạo đơn hàng mới, lưu chi tiết, trừ tồn kho và xóa giỏ hàng
     */
    public function createOrder($orderData, $cartItems, $cartId)
    {
        $conn = $this->connect();
        try {
            $conn->beginTransaction();

            // 1. Thêm thông tin vào bảng orders
            $sqlOrder = "INSERT INTO orders 
                        (user_id, fullname, phone, email, address, note, total_amount, payment_method, status, created_at) 
                        VALUES (:user_id, :fullname, :phone, :email, :address, :note, :total_amount, :payment_method, 'pending', NOW())";
            
            $stmtOrder = $conn->prepare($sqlOrder);
            $stmtOrder->execute([
                'user_id' => $orderData['user_id'],
                'fullname' => $orderData['fullname'],
                'phone' => $orderData['phone'],
                'email' => $orderData['email'],
                'address' => $orderData['address'],
                'note' => $orderData['note'],
                'total_amount' => $orderData['total_amount'],
                'payment_method' => $orderData['payment_method']
            ]);

            $orderId = $conn->lastInsertId();

            // 2. Thêm vào bảng order_items và trừ Tồn kho
            $sqlItem = "INSERT INTO order_items (order_id, product_id, product_variant_id, price, quantity) VALUES (:order_id, :product_id, :variant_id, :price, :quantity)";
            $stmtItem = $conn->prepare($sqlItem);

            $sqlStock = "UPDATE product_variants SET quantity = quantity - :qty WHERE id = :variant_id AND quantity >= :qty";
            $stmtStock = $conn->prepare($sqlStock);

            foreach ($cartItems as $item) {
                // Sửa lỗi: Check cả 2 trường hợp tên key từ Cart (variant_id) hoặc bảng khác (product_variant_id)
                $vId = $item['variant_id'] ?? $item['product_variant_id'] ?? 0;
                $pId = $item['product_id'] ?? $item['id'];

                $stmtItem->execute([
                    'order_id' => $orderId,
                    'product_id' => $pId,
                    'variant_id' => $vId,
                    'price' => $item['price'],
                    'quantity' => $item['quantity']
                ]);

                // Trừ tồn kho (nếu có ID biến thể hợp lệ)
                if ($vId > 0) {
                    $stmtStock->execute([
                        'qty' => $item['quantity'],
                        'variant_id' => $vId
                    ]);
                }
            }

            // 3. Xóa các sản phẩm trong giỏ hàng sau khi đặt thành công
            if ($cartId) {
                $stmtClearCart = $conn->prepare("DELETE FROM cart_items WHERE cart_id = :cart_id");
                $stmtClearCart->execute(['cart_id' => $cartId]);
            }

            $conn->commit();
            return $orderId;

        } catch (Exception $e) {
            $conn->rollBack();
            return false;
        }
    }

    /**
     * Lấy toàn bộ danh sách đơn hàng của 1 user (Lịch sử)
     */
    public function getOrdersByUser($userId)
    {
        $sql = "SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy thông tin chung của 1 đơn hàng cụ thể (Có kiểm tra bảo mật user_id)
     */
    public function getOrderById($orderId, $userId)
    {
        $sql = "SELECT * FROM orders WHERE id = :id AND user_id = :user_id";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(['id' => $orderId, 'user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách sản phẩm bên trong 1 đơn hàng
     */
    public function getOrderItems($orderId)
    {
        // Join với bảng products và product_variants để lấy tên và hình ảnh
        $sql = "SELECT oi.*, p.name as product_name, COALESCE(pv.image, p.img_thumbnail) as image, pv.attributes 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                LEFT JOIN product_variants pv ON oi.product_variant_id = pv.id 
                WHERE oi.order_id = :order_id";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(['order_id' => $orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Hủy đơn hàng (Chỉ được hủy khi trạng thái là pending)
     */
    public function cancelOrder($orderId, $userId)
    {
        $conn = $this->connect();
        try {
            $conn->beginTransaction();

            // 1. Cập nhật trạng thái thành 'cancelled'
            $sql = "UPDATE orders SET status = 'cancelled' WHERE id = :id AND user_id = :user_id AND status = 'pending'";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['id' => $orderId, 'user_id' => $userId]);

            if ($stmt->rowCount() > 0) {
                // 2. Hoàn lại số lượng tồn kho
                $items = $this->getOrderItems($orderId);
                $sqlRestore = "UPDATE product_variants SET quantity = quantity + :qty WHERE id = :variant_id";
                $stmtRestore = $conn->prepare($sqlRestore);
                
                foreach ($items as $item) {
                    if ($item['product_variant_id'] > 0) {
                        $stmtRestore->execute([
                            'qty' => $item['quantity'],
                            'variant_id' => $item['product_variant_id']
                        ]);
                    }
                }
                
                $conn->commit();
                return true;
            }

            $conn->rollBack();
            return false;
        } catch (Exception $e) {
            $conn->rollBack();
            return false;
        }
    }

    /**
     * =========================================
     * CÁC HÀM DÀNH CHO QUẢN TRỊ VIÊN (ADMIN)
     * =========================================
     */

    /**
     * Lấy toàn bộ đơn hàng của tất cả khách hàng (Có Phân trang & Tìm kiếm)
     */
    public function getAllOrders($search = '', $limit = 10, $offset = 0)
    {
        $sql = "SELECT * FROM orders";
        $params = [];

        if (!empty($search)) {
            // Tìm kiếm theo ID đơn hàng, tên khách, số điện thoại hoặc email
            $sql .= " WHERE id LIKE :search OR fullname LIKE :search OR phone LIKE :search OR email LIKE :search";
            $params['search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        
        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Đếm tổng số đơn hàng (Dành cho tính toán phân trang)
     */
    public function getTotalAdminOrders($search = '')
    {
        $sql = "SELECT COUNT(id) as total FROM orders";
        $params = [];

        if (!empty($search)) {
            $sql .= " WHERE id LIKE :search OR fullname LIKE :search OR phone LIKE :search OR email LIKE :search";
            $params['search'] = '%' . $search . '%';
        }

        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    /**
     * Lấy chi tiết đơn hàng cho Admin (Không cần check user_id)
     */
    public function getOrderByIdAdmin($orderId)
    {
        $sql = "SELECT * FROM orders WHERE id = :id";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(['id' => $orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cập nhật trạng thái đơn hàng (Dành cho Admin)
     */
    public function updateOrderStatus($orderId, $status)
    {
        $sql = "UPDATE orders SET status = :status WHERE id = :id";
        $stmt = $this->connect()->prepare($sql);
        return $stmt->execute([
            'status' => $status,
            'id' => $orderId
        ]);
    }
}