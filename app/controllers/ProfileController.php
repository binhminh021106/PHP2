<?php

class ProfileController extends Controller
{
    private $profileModel;

    public function __construct()
    {
        $this->profileModel = new ProfileModel(); // Hệ thống của bạn đang dùng 'new Model()'
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Bắt buộc đăng nhập (sử dụng session user của AuthController)
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            $_SESSION['error'] = "Vui lòng đăng nhập để truy cập trang cá nhân!";
            header('Location: /auth/login');
            exit;
        }
    }

    /**
     * Giao diện chính của Profile
     * Tự động chạy khi gọi URL: /profile hoặc /profile/index
     */
    public function index()
    {
        $userId = $_SESSION['user']['id'];
        
        // Lấy thông tin user hiện tại từ DB (Tránh lấy từ session vì session có thể cũ)
        $userInfo = $this->profileModel->getUserById($userId);
        
        // Cập nhật lại session trong trường hợp vừa đổi ảnh đại diện
        $_SESSION['user']['name'] = $userInfo['name'];
        if(isset($userInfo['picture'])) {
             $_SESSION['user']['picture'] = $userInfo['picture'];
        }
        
        // Lấy danh sách địa chỉ
        $addresses = $this->profileModel->getAddresses($userId);

        $successMsg = $_SESSION['success'] ?? '';
        $errorMsg = $_SESSION['error'] ?? '';
        unset($_SESSION['success'], $_SESSION['error']);

        $this->view('profile/index', [
            'title' => 'Tài khoản của tôi',
            'userInfo' => $userInfo,
            'addresses' => $addresses,
            'successMsg' => $successMsg,
            'errorMsg' => $errorMsg
        ]);
    }

    /**
     * Cập nhật thông tin cá nhân (Tên, SĐT)
     * Tự động chạy khi POST form từ view
     */
    public function updateInfo()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user']['id'];
            $name = trim($_POST['name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');

            if (empty($name)) {
                $_SESSION['error'] = "Tên không được để trống!";
            } else {
                if ($this->profileModel->updateUserInfo($userId, $name, $phone)) {
                    $_SESSION['success'] = "Cập nhật thông tin thành công!";
                } else {
                    $_SESSION['error'] = "Có lỗi xảy ra, vui lòng thử lại!";
                }
            }
        }
        header('Location: /profile');
        exit;
    }

    /**
     * Cập nhật Avatar (Ảnh đại diện)
     */
    public function updateAvatar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
            $userId = $_SESSION['user']['id'];
            $file = $_FILES['avatar'];

            // Kiểm tra lỗi upload
            if ($file['error'] === UPLOAD_ERR_OK) {
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                $fileInfo = pathinfo($file['name']);
                $extension = strtolower($fileInfo['extension']);

                // Validate đuôi file
                if (in_array($extension, $allowedExtensions)) {
                    // Cấu trúc thư mục upload (Dựa theo dự án của bạn)
                    $uploadDir = __DIR__ . '/../../public/storage/uploads/users/';
                    
                    // Tạo thư mục nếu chưa tồn tại
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    // Đổi tên file để tránh trùng lặp
                    $newFileName = 'user_' . $userId . '_' . time() . '.' . $extension;
                    $uploadPath = $uploadDir . $newFileName;

                    // Di chuyển file
                    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                        // Lưu vào DB
                        $this->profileModel->updateAvatar($userId, $newFileName);
                        $_SESSION['success'] = "Đã cập nhật ảnh đại diện!";
                    } else {
                        $_SESSION['error'] = "Không thể lưu file ảnh!";
                    }
                } else {
                    $_SESSION['error'] = "Chỉ chấp nhận file ảnh định dạng JPG, PNG, GIF.";
                }
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra khi tải ảnh lên.";
            }
        }
        header('Location: /profile');
        exit;
    }

    /**
     * Thêm địa chỉ mới
     */
    public function addAddress()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user']['id'];
            $fullname = trim($_POST['fullname'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $isDefault = isset($_POST['is_default']) ? 1 : 0;

            if (empty($fullname) || empty($phone) || empty($address)) {
                $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin địa chỉ!";
            } else {
                if ($this->profileModel->addAddress($userId, $fullname, $phone, $address, $isDefault)) {
                    $_SESSION['success'] = "Đã thêm địa chỉ mới!";
                } else {
                    $_SESSION['error'] = "Có lỗi khi thêm địa chỉ.";
                }
            }
        }
        header('Location: /profile');
        exit;
    }

    /**
     * Set địa chỉ mặc định
     */
    public function setDefaultAddress($addressId = null)
    {
        if ($addressId) {
            $userId = $_SESSION['user']['id'];
            $this->profileModel->setDefaultAddress($addressId, $userId);
            $_SESSION['success'] = "Đã thay đổi địa chỉ mặc định!";
        }
        header('Location: /profile');
        exit;
    }

    /**
     * Xóa địa chỉ
     */
    public function deleteAddress($addressId = null)
    {
        if ($addressId) {
            $userId = $_SESSION['user']['id'];
            $this->profileModel->deleteAddress($addressId, $userId);
            $_SESSION['success'] = "Đã xóa địa chỉ!";
        }
        header('Location: /profile');
        exit;
    }
}