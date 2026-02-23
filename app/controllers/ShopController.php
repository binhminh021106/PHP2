<?php

class ShopController extends Controller
{
    private $shopModel;

    public function __construct()
    {
        $this->shopModel = new ShopModel(); // Hoặc $this->model('ShopModel');
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Khởi tạo session so sánh nếu chưa có
        if (!isset($_SESSION['compare'])) {
            $_SESSION['compare'] = [];
        }
    }

    /**
     * HIỂN THỊ TRANG SHOP CHÍNH (CÓ LỌC & PHÂN TRANG)
     * URL: /shop
     */
    public function index()
    {
        // 1. Nhận tham số từ URL (GET)
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 9; // Số sản phẩm trên 1 trang
        $offset = ($page - 1) * $limit;

        $filters = [
            'search' => $_GET['search'] ?? '',
            'category' => $_GET['category'] ?? '',
            'min_price' => $_GET['min_price'] ?? '',
            'max_price' => $_GET['max_price'] ?? '',
            'sort' => $_GET['sort'] ?? 'newest'
        ];

        // 2. Lấy dữ liệu từ Model
        $products = $this->shopModel->getFilteredProducts($filters, $limit, $offset);
        $totalProducts = $this->shopModel->getTotalProducts($filters);
        $categories = $this->shopModel->getCategories();

        // 3. Tính toán phân trang
        $totalPages = ceil($totalProducts / $limit);

        // Hiển thị thông báo
        $successMsg = $_SESSION['success'] ?? '';
        $errorMsg = $_SESSION['error'] ?? '';
        unset($_SESSION['success'], $_SESSION['error']);

        $this->view('shop/index', [
            'title' => 'Cửa hàng',
            'products' => $products,
            'categories' => $categories,
            'filters' => $filters,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
            'successMsg' => $successMsg,
            'errorMsg' => $errorMsg
        ]);
    }

    /**
     * THÊM SẢN PHẨM VÀO DANH SÁCH SO SÁNH
     * URL: /shop/addCompare/{id}
     */
    public function addCompare($id = null)
    {
        if ($id) {
            // Giới hạn tối đa 4 sản phẩm để so sánh
            if (count($_SESSION['compare']) >= 4 && !in_array($id, $_SESSION['compare'])) {
                $_SESSION['error'] = "Bạn chỉ có thể so sánh tối đa 4 sản phẩm cùng lúc!";
            } else {
                if (!in_array($id, $_SESSION['compare'])) {
                    $_SESSION['compare'][] = (int)$id;
                    $_SESSION['success'] = "Đã thêm vào danh sách so sánh!";
                } else {
                    $_SESSION['error'] = "Sản phẩm này đã có trong danh sách so sánh!";
                }
            }
        }
        
        $referer = $_SERVER['HTTP_REFERER'] ?? '/shop';
        header("Location: $referer");
        exit;
    }

    /**
     * XÓA SẢN PHẨM KHỎI DANH SÁCH SO SÁNH
     * URL: /shop/removeCompare/{id}
     */
    public function removeCompare($id = null)
    {
        if ($id) {
            $key = array_search((int)$id, $_SESSION['compare']);
            if ($key !== false) {
                unset($_SESSION['compare'][$key]);
                // Re-index lại mảng
                $_SESSION['compare'] = array_values($_SESSION['compare']);
                $_SESSION['success'] = "Đã xóa khỏi danh sách so sánh!";
            }
        }
        
        $referer = $_SERVER['HTTP_REFERER'] ?? '/shop/compare';
        header("Location: $referer");
        exit;
    }

    /**
     * HIỂN THỊ TRANG SO SÁNH SẢN PHẨM
     * URL: /shop/compare
     */
    public function compare()
    {
        $compareIds = $_SESSION['compare'] ?? [];
        $compareProducts = $this->shopModel->getProductsByIds($compareIds);

        $successMsg = $_SESSION['success'] ?? '';
        $errorMsg = $_SESSION['error'] ?? '';
        unset($_SESSION['success'], $_SESSION['error']);

        $this->view('shop/compare', [
            'title' => 'So sánh sản phẩm',
            'products' => $compareProducts,
            'successMsg' => $successMsg,
            'errorMsg' => $errorMsg
        ]);
    }
}