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
            // Bắt đầu transaction để đảm bảo an toàn dữ liệu (Nếu lỗi sẽ rollback lại toàn bộ)
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

            $sqlStock = "UPDATE product_variants SET stock = stock - :qty WHERE id = :variant_id AND stock >= :qty";
            $stmtStock = $conn->prepare($sqlStock);

            foreach ($cartItems as $item) {
                // Lưu chi tiết đơn
                $stmtItem->execute([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['product_variant_id'] ?? 0, // Nếu không có variant thì để 0
                    'price' => $item['price'],
                    'quantity' => $item['quantity']
                ]);

                // Trừ tồn kho (nếu có dùng biến thể)
                if (isset($item['product_variant_id']) && $item['product_variant_id'] > 0) {
                    $stmtStock->execute([
                        'qty' => $item['quantity'],
                        'variant_id' => $item['product_variant_id']
                    ]);
                }
            }
 
            // 3. Xóa các sản phẩm trong giỏ hàng sau khi đặt thành công
            $stmtClearCart = $conn->prepare("DELETE FROM cart_items WHERE cart_id = :cart_id");
            $stmtClearCart->execute(['cart_id' => $cartId]);

            // Hoàn tất transaction
            $conn->commit();
            return $orderId;

        } catch (Exception $e) {
            $conn->rollBack();
            return false;
        }
    }
}