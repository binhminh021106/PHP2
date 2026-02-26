<?php

class DashboardModel extends Model
{
    /**
     * Lấy tổng doanh thu (Chỉ tính các đơn hàng đã giao thành công)
     */
    public function getTotalRevenue()
    {
        $sql = "SELECT SUM(total_amount) as total FROM orders WHERE status = 'delivered'";
        $result = $this->connect()->query($sql)->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    /**
     * Lấy tổng số lượng đơn hàng trong hệ thống
     */
    public function getTotalOrders()
    {
        $sql = "SELECT COUNT(id) as count FROM orders";
        $result = $this->connect()->query($sql)->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    /**
     * Lấy số lượng đơn hàng đang chờ xác nhận (pending)
     */
    public function getPendingOrders()
    {
        $sql = "SELECT COUNT(id) as count FROM orders WHERE status = 'pending'";
        $result = $this->connect()->query($sql)->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    /**
     * Lấy tổng số lượng sản phẩm đang có
     */
    public function getTotalProducts()
    {
        $sql = "SELECT COUNT(id) as count FROM products WHERE deleted_at IS NULL";
        $result = $this->connect()->query($sql)->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    /**
     * Lấy số lượng sản phẩm sắp hết hàng (số lượng <= 5)
     */
    public function getLowStockProducts()
    {
        $sql = "SELECT COUNT(id) as count FROM product_variants WHERE quantity <= 5";
        $result = $this->connect()->query($sql)->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    /**
     * Lấy tổng số lượng khách hàng (Giả sử role = 0 là Member)
     */
    public function getTotalUsers()
    {
        $sql = "SELECT COUNT(id) as count FROM users WHERE role = 0";
        $result = $this->connect()->query($sql)->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    /**
     * Lấy danh sách các đơn hàng gần đây nhất
     */
    public function getRecentOrders($limit = 5)
    {
        $sql = "SELECT * FROM orders ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy dữ liệu doanh thu 7 ngày gần nhất
     */
    public function getRevenueLast7Days()
    {
        $sql = "SELECT DATE(created_at) as date, SUM(total_amount) as revenue 
                FROM orders 
                WHERE status = 'delivered' AND created_at >= DATE(NOW()) - INTERVAL 6 DAY 
                GROUP BY DATE(created_at) 
                ORDER BY DATE(created_at) ASC";
                
        $results = $this->connect()->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        // Tạo mảng mặc định cho 7 ngày qua (để nếu ngày nào không có đơn thì giá trị là 0)
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $dateStr = date('Y-m-d', strtotime("-$i days"));
            $data[$dateStr] = 0;
        }

        // Đổ dữ liệu thật vào
        foreach ($results as $row) {
            $data[$row['date']] = (float)$row['revenue'];
        }

        // Format lại nhãn (Ngày/Tháng) để biểu đồ hiển thị đẹp hơn
        $labels = [];
        foreach (array_keys($data) as $d) {
            $labels[] = date('d/m', strtotime($d));
        }

        return [
            'labels' => $labels,
            'revenues' => array_values($data)
        ];
    }

    /**
     * Lấy dữ liệu thống kê trạng thái đơn hàng (Cho biểu đồ tròn)
     */
    public function getOrderStatusStats()
    {
        $sql = "SELECT status, COUNT(*) as count FROM orders GROUP BY status";
        $results = $this->connect()->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        // Khởi tạo mảng số lượng theo đúng thứ tự mảng màu trên giao diện:
        // ['Chờ duyệt', 'Đang xử lý', 'Đang giao', 'Đã giao', 'Đã hủy']
        $stats = [
            'pending' => 0,
            'processing' => 0,
            'shipped' => 0,
            'delivered' => 0,
            'cancelled' => 0
        ];

        foreach ($results as $row) {
            if (array_key_exists($row['status'], $stats)) {
                $stats[$row['status']] = (int)$row['count'];
            }
        }

        return array_values($stats); // Trả về dạng mảng indexed [15, 10, 25, 45, 5]
    }
}