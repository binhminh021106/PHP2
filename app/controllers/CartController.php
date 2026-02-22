<?php

class CartController extends Controller
{
    private $cartModel;

    public function __construct()
    {
        // Khởi tạo model (Cách gọi tuỳ thuộc vào cấu trúc framework của bạn)
        // Ví dụ: $this->cartModel = $this->model('CartModel');
        $this->cartModel = new CartModel();
        
        // Đảm bảo session đã được khởi động để quản lý khách vãng lai
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Tạo một session ID riêng cho khách nếu chưa có
        if (!isset($_SESSION['guest_session_id'])) {
            $_SESSION['guest_session_id'] = session_create_id();
        }
    }

    /**
     * HELPER: Lấy ID giỏ hàng hiện tại (hoặc tự tạo mới nếu chưa có)
     */
    private function getCurrentCartId()
    {
        // Kiểm tra xem user đã đăng nhập chưa (Giả sử bạn lưu id user ở $_SESSION['user_id'])
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $sessionId = $_SESSION['guest_session_id'];

        // 1. Tìm giỏ hàng trong database
        $cart = $this->cartModel->getCart($userId, $sessionId);

        if ($cart) {
            return $cart['id']; // Trả về ID nếu đã có
        }

        // 2. Nếu chưa có, tạo giỏ hàng mới và trả về ID vừa tạo
        return $this->cartModel->createCart($userId, $sessionId);
    }

    /**
     * HIỂN THỊ TRANG GIỎ HÀNG
     * URL: /cart hoặc /cart/index
     */
    public function index()
    {
        // 1. Lấy ID giỏ hàng
        $cartId = $this->getCurrentCartId();
        
        // 2. Lấy danh sách sản phẩm từ Model
        $cartItems = $this->cartModel->getCartItems($cartId);
        
        // 3. Tính tổng tiền (Tuỳ chọn: bạn có thể tính luôn ở View hoặc tính ở đây truyền sang)
        $totalPrice = 0;
        foreach ($cartItems as $item) {
            $totalPrice += $item['price'] * $item['quantity'];
        }

        // 4. Gọi View hiển thị (Điều chỉnh lại hàm view() cho khớp với framework của bạn)
        /*
        $this->view('cart/index', [
            'cartItems' => $cartItems,
            'totalPrice' => $totalPrice
        ]);
        */

        // Đoạn này in ra màn hình để bạn dễ debug test dữ liệu:
        echo "<h3>Dữ liệu giỏ hàng:</h3>";
        echo "<pre>";
        print_r($cartItems);
        echo "<b>Tổng tiền: </b>" . number_format($totalPrice) . " đ";
        echo "</pre>";
    }

    /**
     * XỬ LÝ THÊM SẢN PHẨM VÀO GIỎ
     * Nơi nhận dữ liệu form POST từ trang Chi tiết sản phẩm
     * URL: /cart/add
     */
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy dữ liệu người dùng gửi lên
            $productId = $_POST['product_id'] ?? null;
            $variantId = $_POST['variant_id'] ?? null;
            $quantity = $_POST['quantity'] ?? 1;

            if ($productId && $variantId) {
                // Lấy/Tạo ID giỏ hàng
                $cartId = $this->getCurrentCartId();
                
                // Gọi model để lưu vào database
                $this->cartModel->addToCart($cartId, $productId, $variantId, $quantity);
                
                // Chuyển hướng người dùng về trang giỏ hàng
                header('Location: /cart');
                exit;
            }
        }
        
        // Nếu không có POST data hoặc thiếu ID
        echo "Dữ liệu không hợp lệ!";
    }

    /**
     * CẬP NHẬT SỐ LƯỢNG (Khi khách bấm + / -)
     * URL: /cart/update
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cartItemId = $_POST['cart_item_id'] ?? null;
            $quantity = (int)($_POST['quantity'] ?? 1);

            if ($cartItemId) {
                if ($quantity > 0) {
                    // Cập nhật số lượng mới
                    $this->cartModel->updateQuantity($cartItemId, $quantity);
                } else {
                    // Nếu khách giảm số lượng về 0 -> Xóa luôn sản phẩm đó
                    $this->cartModel->removeCartItem($cartItemId);
                }
            }
        }
        
        // Reload lại trang giỏ hàng
        header('Location: /cart');
        exit;
    }

    /**
     * XÓA MỘT SẢN PHẨM KHỎI GIỎ HÀNG
     * URL: /cart/delete/{cartItemId}
     */
    public function delete($cartItemId = null)
    {
        if ($cartItemId) {
            $this->cartModel->removeCartItem($cartItemId);
        }
        
        header('Location: /cart');
        exit;
    }
}