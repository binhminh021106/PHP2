<?php

class AuthController extends Controller
{
    private $authModel;

    public function __construct()
    {
        $this->authModel = $this->model('AuthModel');
    }

    // --- ĐĂNG NHẬP ---
    public function login()
    {
        // Nếu đã đăng nhập thì chuyển hướng về trang chủ
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }

        $error = $_SESSION['error'] ?? '';
        unset($_SESSION['error']);

        $this->view('Auth.login', [
            'title' => 'Đăng nhập',
            'error' => $error
        ]);
    }

    public function handleLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            if (empty($email) || empty($password)) {
                $_SESSION['error'] = 'Vui lòng nhập đầy đủ email và mật khẩu.';
                $this->redirect('/auth/login');
            }

            // Tìm user theo email
            $user = $this->authModel->findUserByEmail($email);

            if ($user) {
                // Kiểm tra mật khẩu (đã hash)
                if (password_verify($password, $user['password'])) {
                    
                    // Kiểm tra trạng thái tài khoản
                    if ($user['status'] == 'inactive') {
                        $_SESSION['error'] = 'Tài khoản của bạn đã bị khóa.';
                        $this->redirect('/auth/login');
                    }

                    // Lưu session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'] ?? 'user'; // Nếu có phân quyền

                    // Nếu là admin thì vào trang quản trị, ngược lại về trang chủ
                    if (isset($user['role']) && $user['role'] == 'admin') {
                        $this->redirect('/dashboard');
                    } else {
                        $this->redirect('/');
                    }

                } else {
                    $_SESSION['error'] = 'Mật khẩu không chính xác.';
                    header('Location: /auth/login');
                }
            } else {
                $_SESSION['error'] = 'Email này chưa được đăng ký.';
                header('Location: /auth/login');
            }
        }
    }

    // --- ĐĂNG KÝ ---
    public function register()
    {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }

        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);

        $this->view('Auth.register', [
            'title' => 'Đăng ký tài khoản',
            'errors' => $errors,
            'old' => $old
        ]);
    }

    public function handleRegister()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $phone = trim($_POST['phone']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            $address = trim($_POST['address']);

            $errors = [];

            // Validate đơn giản
            if (empty($name)) $errors['name'] = "Họ tên không được để trống";
            if (empty($email)) $errors['email'] = "Email không được để trống";
            if (empty($phone)) $errors['phone'] = "SĐT không được để trống";
            if (empty($password)) $errors['password'] = "Mật khẩu không được để trống";
            
            if ($password !== $confirm_password) {
                $errors['confirm_password'] = "Mật khẩu nhập lại không khớp";
            }

            // Check trùng trong DB
            if ($this->authModel->isEmailExists($email)) {
                $errors['email'] = "Email này đã được sử dụng";
            }
            if ($this->authModel->isPhoneExists($phone)) {
                $errors['phone'] = "Số điện thoại này đã được sử dụng";
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $_POST;
                header('Location: /auth/register');
            }

            // Lưu vào DB (Model đã có hàm registerUser lo hash password)
            $data = [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'password' => $password,
                'address' => $address
            ];

            if ($this->authModel->registerUser($data)) {
                $_SESSION['success'] = "Đăng ký thành công! Vui lòng đăng nhập.";
                header('Location: /auth/login');
            } else {
                $_SESSION['error'] = "Đã có lỗi xảy ra, vui lòng thử lại.";
                header('Location: /auth/register');
            }
        }
    }

    // --- ĐĂNG XUẤT ---
    public function logout()
    {
        session_destroy();
        header('Location: /auth/login');
        exit;
    }
}