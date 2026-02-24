<?php

class CheckoutController extends Controller
{
    private $cartModel;
    private $orderModel;
    private $profileModel;

    public function __construct()
    {
        // Khởi tạo model giống y hệt cách bạn làm ở CartController
        $this->cartModel = new CartModel();
        $this->orderModel = new OrderModel();
        $this->profileModel = new ProfileModel();
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // KIỂM TRA ĐĂNG NHẬP THÔNG MINH
        $userId = $this->getUserId();

        if (!$userId) {
            $_SESSION['error'] = "Vui lòng đăng nhập để tiến hành thanh toán!";
            header('Location: /auth/login'); 
            exit;
        }
    }

    /**
     * Hàm tự động tìm ID người dùng từ Session
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
     * Lấy ID giỏ hàng hiện tại
     */
    private function getCurrentCartId()
    {
        $userId = $this->getUserId(); 
        $cart = $this->cartModel->getCart($userId, null);
        return $cart ? $cart['id'] : null;
    }

    /**
     * HIỂN THỊ TRANG THANH TOÁN (GET)
     * URL: /checkout hoặc /checkout/index
     */
    public function index()
    {
        $userId = $this->getUserId();
        $cartId = $this->getCurrentCartId();
        
        if (!$cartId) {
            header('Location: /shop');
            exit;
        }

        // Lấy danh sách sản phẩm trong giỏ
        $cartItems = $this->cartModel->getCartItems($cartId);
        if (empty($cartItems)) {
            $_SESSION['error'] = "Giỏ hàng của bạn đang trống!";
            header('Location: /cart');
            exit;
        }

        // Tính tổng tiền đơn hàng
        $totalPrice = 0;
        foreach ($cartItems as &$item) {
            $item['parsed_attr'] = is_string($item['attributes']) ? json_decode($item['attributes'], true) : [];
            $totalPrice += $item['price'] * $item['quantity'];
        }

        // Lấy địa chỉ mặc định từ Sổ địa chỉ (nếu có)
        $addresses = $this->profileModel->getAddresses($userId);
        $defaultAddress = null;
        if (!empty($addresses)) {
            foreach ($addresses as $addr) {
                if ($addr['is_default'] == 1) {
                    $defaultAddress = $addr;
                    break;
                }
            }
            if (!$defaultAddress) {
                $defaultAddress = $addresses[0]; // Lấy tạm địa chỉ đầu tiên
            }
        }

        $errorMsg = $_SESSION['error'] ?? '';
        unset($_SESSION['error']);

        // Gọi View và truyền dữ liệu
        $this->view('checkout/index', [
            'title' => 'Thanh toán đơn hàng',
            'cartItems' => $cartItems,
            'totalPrice' => $totalPrice,
            'user' => $_SESSION['user'] ?? [],
            'defaultAddress' => $defaultAddress,
            'errorMsg' => $errorMsg
        ]);
    }

    /**
     * XỬ LÝ ĐẶT HÀNG (POST)
     * URL: /checkout/process
     */
    public function process()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $this->getUserId();
            $cartId = $this->getCurrentCartId();
            
            if (!$cartId) {
                header('Location: /cart');
                exit;
            }

            $cartItems = $this->cartModel->getCartItems($cartId);
            if (empty($cartItems)) {
                header('Location: /cart');
                exit;
            }

            // Tính lại tổng tiền ở Backend để bảo mật
            $totalPrice = 0;
            foreach ($cartItems as $item) {
                $totalPrice += $item['price'] * $item['quantity'];
            }
            $shippingFee = 0; // Giả sử đang miễn phí ship
            $finalTotal = $totalPrice + $shippingFee;

            // Nhận dữ liệu từ form thanh toán
            $fullname = trim($_POST['fullname'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $note = trim($_POST['note'] ?? '');
            $paymentMethod = $_POST['payment_method'] ?? 'cod';

            if (empty($fullname) || empty($phone) || empty($address)) {
                $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin nhận hàng!";
                header('Location: /checkout/index');
                exit;
            }

            // Chuẩn bị mảng dữ liệu để ném vào Model
            $orderData = [
                'user_id' => $userId,
                'fullname' => $fullname,
                'phone' => $phone,
                'email' => $email,
                'address' => $address,
                'note' => $note,
                'total_amount' => $finalTotal,
                'payment_method' => $paymentMethod
            ];

            // Gọi hàm tạo đơn hàng (đã xử lý transaction trong model)
            $orderId = $this->orderModel->createOrder($orderData, $cartItems, $cartId);

            if ($orderId) {
                $_SESSION['success'] = "Đặt hàng thành công!";
                header("Location: /checkout/success?order_id=" . $orderId); 
                exit;
            } else {
                $_SESSION['error'] = "Có lỗi hệ thống. Vui lòng thử lại!";
                header('Location: /checkout/index');
                exit;
            }
        } else {
            // Tránh việc user truy cập trực tiếp bằng GET vào link process
            header('Location: /checkout/index');
            exit;
        }
    }

    /**
     * TRANG THÔNG BÁO THÀNH CÔNG (GET)
     * URL: /checkout/success
     */
    public function success()
    {
        $orderId = $_GET['order_id'] ?? null;
        if (!$orderId) {
            header('Location: /');
            exit;
        }

        $successMsg = $_SESSION['success'] ?? 'Cảm ơn bạn đã mua hàng!';
        unset($_SESSION['success']);

        $this->view('checkout/success', [
            'title' => 'Đặt hàng thành công',
            'orderId' => $orderId,
            'successMsg' => $successMsg
        ]);
    }
}