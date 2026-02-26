<?php

class DashboardController extends Controller
{
    private $dashboardModel;

    public function __construct()
    {
        // Khởi tạo Model
        $this->dashboardModel = new DashboardModel(); 
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // KIỂM TRA QUYỀN ADMIN (Bắt buộc phải là Quản trị viên mới được vào Dashboard)
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 1) {
            $_SESSION['error'] = "Bạn không có quyền truy cập khu vực quản trị!";
            header('Location: /'); 
            exit;
        }
    }

    /**
     * Hiển thị giao diện tổng quan (Trang chủ Admin)
     * URL: /dashboard hoặc /admin/dashboard
     */
    public function index()
    {
        // 1. Lấy dữ liệu thống kê từ Model
        $totalRevenue = $this->dashboardModel->getTotalRevenue();
        $totalOrders = $this->dashboardModel->getTotalOrders();
        $pendingOrders = $this->dashboardModel->getPendingOrders();
        $totalProducts = $this->dashboardModel->getTotalProducts();
        $lowStockProducts = $this->dashboardModel->getLowStockProducts();
        $totalUsers = $this->dashboardModel->getTotalUsers();
        
        // 2. Lấy 5 đơn hàng mới nhất để hiển thị ở bảng cuối trang
        $recentOrders = $this->dashboardModel->getRecentOrders(5);

        // 3. Lấy dữ liệu cho biểu đồ (Doanh thu 7 ngày qua và Trạng thái đơn hàng)
        $revenueChartData = $this->dashboardModel->getRevenueLast7Days();
        $orderStatusData = $this->dashboardModel->getOrderStatusStats();

        // 4. Gọi View và truyền dữ liệu
        // Đã sửa lại đường dẫn view thành 'Admin/AdminDashboard/index' để khớp với thư mục của bạn
        $this->view('Admin/AdminDashboard/index', [
            'title' => 'Tổng quan hệ thống - Admin MENSWEAR',
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
            'totalProducts' => $totalProducts,
            'lowStockProducts' => $lowStockProducts,
            'totalUsers' => $totalUsers,
            'recentOrders' => $recentOrders,
            'revenueChartData' => $revenueChartData,
            'orderStatusData' => $orderStatusData
        ]);
    }
}