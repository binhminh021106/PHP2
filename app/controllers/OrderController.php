<?php

class OrderController extends Controller
{
    private $orderModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel(); // Hoặc clone $this->model('OrderModel'); tùy phiên bản framework của bạn
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            $_SESSION['error'] = "Vui lòng đăng nhập để xem lịch sử đơn hàng!";
            header('Location: /auth/login'); 
            exit;
        }
    }

    /**
     * HIỂN THỊ DANH SÁCH LỊCH SỬ ĐƠN HÀNG
     * URL: /order/history
     */
    public function history()
    {
        $userId = $_SESSION['user']['id'];
        
        // Lấy danh sách đơn hàng
        $orders = $this->orderModel->getOrdersByUser($userId);

        $successMsg = $_SESSION['success'] ?? '';
        $errorMsg = $_SESSION['error'] ?? '';
        unset($_SESSION['success'], $_SESSION['error']);

        $this->view('order/history', [
            'title' => 'Lịch sử đơn hàng',
            'orders' => $orders,
            'successMsg' => $successMsg,
            'errorMsg' => $errorMsg
        ]);
    }

    /**
     * XEM CHI TIẾT 1 ĐƠN HÀNG
     * URL: /order/detail/{id}
     */
    public function detail($orderId = null)
    {
        if (!$orderId) {
            header('Location: /order/history');
            exit;
        }

        $userId = $_SESSION['user']['id'];
        
        // Lấy thông tin đơn hàng
        $order = $this->orderModel->getOrderById($orderId, $userId);
        
        if (!$order) {
            $_SESSION['error'] = "Không tìm thấy đơn hàng hoặc bạn không có quyền xem!";
            header('Location: /order/history');
            exit;
        }

        // Lấy danh sách sản phẩm trong đơn
        $orderItems = $this->orderModel->getOrderItems($orderId);
        
        // Giải mã attributes (Color, Size)
        foreach ($orderItems as &$item) {
            $item['parsed_attr'] = is_string($item['attributes']) ? json_decode($item['attributes'], true) : [];
        }

        $this->view('order/detail', [
            'title' => 'Chi tiết đơn hàng #' . $order['id'],
            'order' => $order,
            'orderItems' => $orderItems
        ]);
    }

    /**
     * HỦY ĐƠN HÀNG
     * URL: /order/cancel/{id}
     */
    public function cancel($orderId = null)
    {
        if ($orderId) {
            $userId = $_SESSION['user']['id'];
            $result = $this->orderModel->cancelOrder($orderId, $userId);
            
            if ($result) {
                $_SESSION['success'] = "Hủy đơn hàng thành công!";
            } else {
                $_SESSION['error'] = "Không thể hủy đơn hàng này (Có thể đơn đã được xác nhận hoặc xử lý).";
            }
        }
        
        // Quay lại trang chi tiết hoặc trang lịch sử
        $referer = $_SERVER['HTTP_REFERER'] ?? '/order/history';
        header("Location: $referer");
        exit;
    }
}