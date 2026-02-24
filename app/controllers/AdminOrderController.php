<?php

class AdminOrderController extends Controller
{
    private $orderModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel(); // Hoặc $this->model('OrderModel')
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // KIỂM TRA QUYỀN ADMIN (Giả sử role = 1 là Admin)
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 1) {
            $_SESSION['error'] = "Bạn không có quyền truy cập khu vực này!";
            header('Location: /'); 
            exit;
        }
    }

    /**
     * HIỂN THỊ DANH SÁCH TOÀN BỘ ĐƠN HÀNG
     * URL: /adminorder
     */
    public function index()
    {
        $orders = $this->orderModel->getAllOrders();

        $successMsg = $_SESSION['success'] ?? '';
        $errorMsg = $_SESSION['error'] ?? '';
        unset($_SESSION['success'], $_SESSION['error']);

        $this->view('Admin/AdminOrder/index', [
            'title' => 'Quản lý đơn hàng',
            'orders' => $orders,
            'successMsg' => $successMsg,
            'errorMsg' => $errorMsg
        ]);
    }

    /**
     * XEM CHI TIẾT ĐƠN HÀNG VÀ CẬP NHẬT TRẠNG THÁI
     * URL: /adminorder/detail/{id}
     */
    public function detail($orderId = null)
    {
        if (!$orderId) {
            header('Location: /adminorder');
            exit;
        }

        $order = $this->orderModel->getOrderByIdAdmin($orderId);
        
        if (!$order) {
            $_SESSION['error'] = "Không tìm thấy đơn hàng!";
            header('Location: /adminorder');
            exit;
        }

        // Lấy danh sách sản phẩm (Dùng lại hàm getOrderItems đã có ở Model)
        $orderItems = $this->orderModel->getOrderItems($orderId);
        
        foreach ($orderItems as &$item) {
            $item['parsed_attr'] = is_string($item['attributes']) ? json_decode($item['attributes'], true) : [];
        }

        $successMsg = $_SESSION['success'] ?? '';
        $errorMsg = $_SESSION['error'] ?? '';
        unset($_SESSION['success'], $_SESSION['error']);

        $this->view('Admin/AdminOrder/detail', [
            'title' => 'Chi tiết đơn hàng #' . $order['id'],
            'order' => $order,
            'orderItems' => $orderItems,
            'successMsg' => $successMsg,
            'errorMsg' => $errorMsg
        ]);
    }

    /**
     * XỬ LÝ CẬP NHẬT TRẠNG THÁI (POST)
     * URL: /adminorder/updateStatus
     */
    public function updateStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = $_POST['order_id'] ?? null;
            $status = $_POST['status'] ?? null;

            $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

            if ($orderId && in_array($status, $validStatuses)) {
                if ($this->orderModel->updateOrderStatus($orderId, $status)) {
                    $_SESSION['success'] = "Cập nhật trạng thái thành công!";
                } else {
                    $_SESSION['error'] = "Có lỗi xảy ra, vui lòng thử lại!";
                }
            } else {
                $_SESSION['error'] = "Dữ liệu không hợp lệ!";
            }
            
            header("Location: /adminorder/detail/" . $orderId);
            exit;
        }
    }
}