<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Google\Client as GoogleClient;

class AuthController extends \Controller {
    private $authModel;

    public function __construct() {
        $this->authModel = $this->model('AuthModel');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login() {
        if (isset($_SESSION['user'])) {
            if ($_SESSION['user']['role'] == 1) {
                header('Location: /admin/product');
            } else {
                header('Location: /home');
            }
            exit;
        }

        $data = [];
        if (isset($_SESSION['success'])) {
            $data['success'] = $_SESSION['success'];
            unset($_SESSION['success']); 
        }

        $this->view('Auth/login', $data);
    }

    public function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            if (empty($email) || empty($password)) {
                $data['error'] = 'Vui lòng nhập đầy đủ email và mật khẩu';
                $this->view('Auth/login', $data);
                return;
            }

            $user = $this->authModel->findUserByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                if ($user['status'] !== 'active') {
                    $data['error'] = 'Tài khoản của bạn đang bị khóa';
                    $this->view('Auth/login', $data);
                    return;
                }

                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ];

                $_SESSION['success'] = 'Đăng nhập thành công!';

                if ($user['role'] == 1) {
                    header('Location: /admin/product');
                } else {
                    header('Location: /home');
                }
                exit;

            } else {
                $data['error'] = 'Email hoặc mật khẩu không chính xác';
                $this->view('Auth/login', $data);
            }
        }
    }

    public function register() {
        $data = [
            'old' => [],
            'errors' => []
        ];

        if (isset($_SESSION['success'])) {
            $data['success'] = $_SESSION['success'];
            unset($_SESSION['success']);
        }

        $this->view('Auth/register', $data);
    }

    public function handleRegister() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name']);
            $phone = trim($_POST['phone']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            
            $errors = [];
            $old = $_POST;

            if (empty($name)) {
                $errors['name'] = 'Họ tên không được để trống';
            }

            if (empty($phone)) {
                $errors['phone'] = 'Số điện thoại không được để trống';
            } elseif ($this->authModel->isPhoneExists($phone)) {
                $errors['phone'] = 'Số điện thoại này đã được sử dụng';
            }

            if (empty($email)) {
                $errors['email'] = 'Email không được để trống';
            } elseif ($this->authModel->isEmailExists($email)) {
                $errors['email'] = 'Email này đã được sử dụng';
            }

            if (empty($password)) {
                $errors['password'] = 'Mật khẩu không được để trống';
            } elseif (strlen($password) < 6) {
                $errors['password'] = 'Mật khẩu phải có ít nhất 6 ký tự';
            }

            if ($password !== $confirm_password) {
                $errors['confirm_password'] = 'Mật khẩu nhập lại không khớp';
            }

            if (!empty($errors)) {
                $this->view('Auth/register', [
                    'error' => 'Vui lòng kiểm tra lại thông tin bên dưới',
                    'errors' => $errors,
                    'old' => $old
                ]);
                return;
            }

            $userData = [
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'password' => $password,
                'role' => 0
            ];

            if ($this->authModel->registerUser($userData)) {
                $_SESSION['success'] = 'Đăng ký tài khoản thành công! Vui lòng đăng nhập.';
                header('Location: /auth/login');
                exit;
            } else {
                $this->view('Auth/register', [
                    'error' => 'Có lỗi hệ thống, vui lòng thử lại sau',
                    'old' => $old,
                    'errors' => []
                ]);
            }
        }
    }

    public function googleLogin() {
        $client = new GoogleClient();
        $client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
        $client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
        $client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI']);
        $client->addScope('email');
        $client->addScope('profile');

        $authUrl = $client->createAuthUrl();
        header('Location: ' . $authUrl);
        exit;
    }

    public function callbackGoogle() {
        $client = new GoogleClient();
        $client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
        $client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
        $client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI']);

        if (isset($_GET['code'])) {
            $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
            $client->setAccessToken($token);

            $oauth = new Google\Service\Oauth2($client);
            $googleUser = $oauth->userinfo->get();

            $userData = [
                'email' => $googleUser->email,
                'name' => $googleUser->name,
                'picture' => $googleUser->picture
            ];

            $user = $this->authModel->findOrCreateUserFromGoogle($userData);

            if ($user && $user['status'] === 'active') {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ];

                $_SESSION['success'] = 'Đăng nhập bằng Google thành công!';

                if ($user['role'] == 1) {
                    header('Location: /admin/product');
                } else {
                    header('Location: /home');
                }
                exit;
            } else {
                $data['error'] = 'Tài khoản của bạn đang bị khóa';
                $this->view('Auth/login', $data);
            }
        } else {
            $data['error'] = 'Đăng nhập Google thất bại';
            $this->view('Auth/login', $data);
        }
    }

    public function logout() {
        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
        }
        
        $_SESSION['success'] = 'Bạn đã đăng xuất tài khoản thành công.';
        header('Location: /home');
        exit;
    }
}