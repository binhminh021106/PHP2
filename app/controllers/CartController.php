<?php

class CartController extends Controller
{
    private $cartModel;

    public function __construct()
    {
        $this->cartModel = new CartModel();
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $userId = $this->getUserId();

        if (!$userId) {
            $_SESSION['error'] = "Vui lòng đăng nhập để sử dụng giỏ hàng!";
            header('Location: /auth/login'); 
            exit;
        }
    }

    /**
     * Hàm tự động tìm ID người dùng từ Session
     * (Giúp tương thích với nhiều cách viết Login khác nhau)
     */
    private function getUserId()
    {
        if (isset($_SESSION['user_id'])) return $_SESSION['user_id'];
        if (isset($_SESSION['user']) && is_array($_SESSION['user'])) return $_SESSION['user']['id'] ?? null;
        if (isset($_SESSION['user']) && is_object($_SESSION['user'])) return $_SESSION['user']->id ?? null;
        if (isset($_SESSION['id'])) return $_SESSION['id'];
        if (isset($_SESSION['account_id'])) return $_SESSION['account_id'];
        
        return null; 
    }

    /**
     * Lấy ID giỏ hàng của User hiện tại (Tạo mới nếu chưa có)
     */
    private function getCurrentCartId()
    {
        $userId = $this->getUserId(); 

        $cart = $this->cartModel->getCart($userId, null);

        if ($cart) {
            return $cart['id']; 
        }

        return $this->cartModel->createCart($userId, null);
    }

    /**
     * HIỂN THỊ TRANG GIỎ HÀNG
     */
    public function index()
    {
        $cartId = $this->getCurrentCartId();
        
        $cartItems = $this->cartModel->getCartItems($cartId);
        
        $totalPrice = 0;
        if (!empty($cartItems)) {
            foreach ($cartItems as &$item) {
                $item['parsed_attr'] = is_string($item['attributes']) ? json_decode($item['attributes'], true) : [];
                $totalPrice += $item['price'] * $item['quantity'];
            }
        }

        $successMsg = $_SESSION['success'] ?? '';
        $errorMsg = $_SESSION['error'] ?? '';
        unset($_SESSION['success'], $_SESSION['error']);

        $this->view('cart/index', [
            'title' => 'Giỏ hàng của bạn',
            'cartItems' => $cartItems,
            'totalPrice' => $totalPrice,
            'successMsg' => $successMsg,
            'errorMsg' => $errorMsg
        ]);
    }

    /**
     * XỬ LÝ THÊM SẢN PHẨM VÀO GIỎ
     */
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = $_POST['product_id'] ?? null;
            $variantId = $_POST['variant_id'] ?? null;
            $quantity = (int)($_POST['quantity'] ?? 1);

            // Nếu đầy đủ dữ liệu hợp lệ
            if ($productId && $variantId && $quantity > 0) {
                $cartId = $this->getCurrentCartId();
                
                // Gọi model để lưu vào database
                $this->cartModel->addToCart($cartId, $productId, $variantId, $quantity);
                
                $_SESSION['success'] = "Đã thêm sản phẩm vào giỏ hàng!";
                header('Location: /cart'); 
                exit;
            } else {
                $_SESSION['error'] = "Lỗi: Không xác định được phân loại sản phẩm!";
            }
        } else {
            $_SESSION['error'] = "Phương thức không hợp lệ!";
        }
        
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        header("Location: $referer");
        exit;
    }

    /**
     * CẬP NHẬT SỐ LƯỢNG (Khi khách bấm + / -)
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cartItemId = $_POST['cart_item_id'] ?? null;
            $quantity = (int)($_POST['quantity'] ?? 1);

            if ($cartItemId) {
                if ($quantity > 0) {
                    $this->cartModel->updateQuantity($cartItemId, $quantity);
                    $_SESSION['success'] = "Đã cập nhật số lượng!";
                } else {
                    $this->cartModel->removeCartItem($cartItemId);
                    $_SESSION['success'] = "Đã xóa sản phẩm khỏi giỏ hàng!";
                }
            }
        }
        
        header('Location: /cart');
        exit;
    }

    /**
     * XÓA MỘT SẢN PHẨM KHỎI GIỎ HÀNG
     */
    public function delete($cartItemId = null)
    {
        if ($cartItemId) {
            $this->cartModel->removeCartItem($cartItemId);
            $_SESSION['success'] = "Đã xóa sản phẩm khỏi giỏ hàng!";
        }
        
        header('Location: /cart');
        exit;
    }
}