<?php

class WishlistController extends Controller
{
    private $wishlistModel;

    public function __construct()
    {
        $this->wishlistModel = new WishlistModel(); // Hoặc $this->model('WishlistModel');
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Kiểm tra đúng chuẩn Session 'user' từ AuthController của bạn
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            $_SESSION['error'] = "Vui lòng đăng nhập để sử dụng tính năng yêu thích!";
            header('Location: /auth/login'); 
            exit;
        }
    }

    /**
     * Hiển thị trang Wishlist
     * URL: /wishlist
     */
    public function index()
    {
        // Lấy ID chuẩn từ Session user
        $userId = $_SESSION['user']['id'];
        
        $wishlistItems = $this->wishlistModel->getWishlistByUser($userId);

        $successMsg = $_SESSION['success'] ?? '';
        $errorMsg = $_SESSION['error'] ?? '';
        unset($_SESSION['success'], $_SESSION['error']);

        $this->view('wishlist/index', [
            'title' => 'Danh sách yêu thích',
            'wishlistItems' => $wishlistItems,
            'successMsg' => $successMsg,
            'errorMsg' => $errorMsg
        ]);
    }

    /**
     * Thêm vào Wishlist (gọi từ nút trái tim)
     * URL: /wishlist/add/{id}
     */
    public function add($productId = null)
    {
        if ($productId) {
            $userId = $_SESSION['user']['id'];
            $added = $this->wishlistModel->add($userId, $productId);
            
            if ($added) {
                $_SESSION['success'] = "Đã thêm vào danh sách yêu thích!";
            } else {
                $_SESSION['error'] = "Sản phẩm này đã có trong danh sách yêu thích!";
            }
        }
        
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        header("Location: $referer");
        exit;
    }

    /**
     * Xóa khỏi Wishlist
     * URL: /wishlist/remove/{id}
     */
    public function remove($productId = null)
    {
        if ($productId) {
            $userId = $_SESSION['user']['id'];
            $this->wishlistModel->remove($userId, $productId);
            $_SESSION['success'] = "Đã xóa khỏi danh sách yêu thích!";
        }
        
        $referer = $_SERVER['HTTP_REFERER'] ?? '/wishlist';
        header("Location: $referer");
        exit;
    }
}