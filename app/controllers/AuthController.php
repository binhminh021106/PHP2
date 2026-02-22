<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Google\Client as GoogleClient;
use League\OAuth2\Client\Provider\Facebook;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class AuthController extends \Controller
{
    private $authModel;

    public function __construct()
    {
        $this->authModel = $this->model('AuthModel');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login()
    {
        if (isset($_SESSION['user'])) {
            if ($_SESSION['user']['role'] == 1) {
                header('Location: /home');
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

    public function handleLogin()
    {
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
                    header('Location: /home');
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

    public function register()
    {
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

    public function handleRegister()
    {
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

    public function googleLogin()
    {
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

    public function callbackGoogle()
    {
        $client = new GoogleClient();
        $client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
        $client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
        $client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI']);

        if (isset($_GET['code'])) {
            $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
            $client->setAccessToken($token);

            $oauth = new Google\Service\Oauth2($client);
            $googleUser = $oauth->userinfo->get();

            // FIX: Bổ sung 'id' vào mảng data để truyền qua Model
            $userData = [
                'id'      => $googleUser->id,
                'email'   => $googleUser->email,
                'name'    => $googleUser->name,
                'picture' => $googleUser->picture
            ];

            $user = $this->authModel->findOrCreateUserFromGoogle($userData);

            if ($user && $user['status'] === 'active') {
                $_SESSION['user'] = [
                    'id'    => $user['id'],
                    'name'  => $user['name'],
                    'email' => $user['email'],
                    'role'  => $user['role']
                ];

                $_SESSION['success'] = 'Đăng nhập bằng google thành công!';

                if ($user['role'] == 1) {
                    header('Location: /product');
                } else {
                    header('Location: /home');
                }
                exit;
            } else {
                $data['error'] = 'Tài khoản của bạn đang bị khóa';
                $this->view('Auth/login', $data);
            }
        } else {
            $data['error'] = 'Đăng nhập thất bại';
            $this->view('Auth/login', $data);
        }
    }

    private function getFacebookProvider()
    {
        return new Facebook([
            'clientId'          => $_ENV['FACEBOOK_APP_ID'],
            'clientSecret'      => $_ENV['FACEBOOK_APP_SECRET'],
            'redirectUri'       => $_ENV['FACEBOOK_REDIRECT_URI'],
            'graphApiVersion'   => 'v19.0',
        ]);
    }

    public function facebookLogin()
    {
        $provider = $this->getFacebookProvider();

        $authUrl = $provider->getAuthorizationUrl([
            'scopes' => ['email', 'public_profile'],
        ]);

        $_SESSION['oauth2state'] = $provider->getState();

        header('Location: ' . $authUrl);
        exit;
    }

    public function callbackFacebook()
    {
        $provider = $this->getFacebookProvider();

        if (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {
            if (isset($_SESSION['oauth2state'])) {
                unset($_SESSION['oauth2state']);
            }
            $_SESSION['error'] = 'Trạng thái xác thực không hợp lệ, vui lòng thử lại.';
            header('Location: /auth/login');
            exit;
        }

        try {
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $_GET['code']
            ]);

            $user = $provider->getResourceOwner($token);

            $userData = [
                'id'      => $user->getId(),
                'email'   => $user->getEmail() ?? $user->getId() . '@facebook.com',
                'name'    => $user->getName(),
                'picture' => $user->getPictureUrl()
            ];

            $authUser = $this->authModel->findOrCreateUserFromFacebook($userData);

            if ($authUser && $authUser['status'] === 'active') {
                $_SESSION['user'] = [
                    'id'    => $authUser['id'],
                    'name'  => $authUser['name'],
                    'email' => $authUser['email'],
                    'role'  => $authUser['role']
                ];

                $_SESSION['success'] = 'Đăng nhập bằng Facebook thành công!';

                if ($authUser['role'] == 1) {
                    header('Location: /product');
                } else {
                    header('Location: /home');
                }
                exit;
            } else {
                $_SESSION['error'] = 'Tài khoản của bạn đang bị khóa.';
                header('Location: /auth/login');
                exit;
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Đăng nhập Facebook thất bại: ' . $e->getMessage();
            header('Location: /auth/login');
            exit;
        }
    }

    public function logout()
    {
        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
        }

        $_SESSION['success'] = 'Bạn đã đăng xuất tài khoản thành công.';
        header('Location: /home');
        exit;
    }

    public function forgotPassword()
    {
        $data = [];
        if (isset($_SESSION['error'])) {
            $data['error'] = $_SESSION['error'];
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            $data['success'] = $_SESSION['success'];
            unset($_SESSION['success']);
        }
        $this->view('Auth/forgotPassword', $data);
    }

    // 2. Xử lý gửi Email
    public function handleForgotPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email']);

            if (empty($email)) {
                $_SESSION['error'] = 'Vui lòng nhập địa chỉ email.';
                header('Location: /auth/forgotPassword');
                exit;
            }

            $user = $this->authModel->findUserByEmail($email);

            if (!$user) {
                // Bảo mật: Kể cả ko có email cũng báo thành công để hacker ko dò được email
                $_SESSION['success'] = 'Nếu email hợp lệ, một đường dẫn khôi phục đã được gửi tới email của bạn.';
                header('Location: /auth/forgotPassword');
                exit;
            }

            // Tạo Token ngẫu nhiên (32 ký tự)
            $token = bin2hex(random_bytes(32));

            // Thời gian hết hạn: 15 phút tính từ hiện tại
            date_default_timezone_set('Asia/Ho_Chi_Minh');
            $expiry = date('Y-m-d H:i:s', time() + (15 * 60));

            // Lưu vào DB
            $this->authModel->saveResetToken($email, $token, $expiry);

            // Tạo link khôi phục
            $resetLink = "http://localhost:8000/auth/resetPassword/" . $token;

            // Gửi Email
            if ($this->sendResetEmail($email, $resetLink)) {
                $_SESSION['success'] = 'Một đường dẫn khôi phục đã được gửi tới email của bạn. Vui lòng kiểm tra hộp thư.';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra trong quá trình gửi mail. Vui lòng thử lại sau.';
            }

            header('Location: /auth/forgotPassword');
            exit;
        }
    }

    // 3. Hàm nội bộ dùng để gửi mail cấu hình bằng SMTP Gmail
    private function sendResetEmail($toEmail, $resetLink)
    {
        $mail = new PHPMailer(true);

        try {
            // Cấu hình Server (Nên đưa các thông số này vào file .env)
            $mail->SMTPDebug = SMTP::DEBUG_OFF; // Tắt debug
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'minhdzwama211@gmail.com'; // Thay bằng Gmail của bạn
            $mail->Password   = 'evjw sqtz zqoj wqsm'; // Thay bằng Mật khẩu ứng dụng Gmail
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->CharSet    = 'UTF-8';

            // Người gửi / Người nhận
            $mail->setFrom('minhdzwama211@gmail.com', 'MENSWEAR. Hệ Thống');
            $mail->addAddress($toEmail);

            // Nội dung Email
            $mail->isHTML(true);
            $mail->Subject = '[MENSWEAR] Yêu cầu khôi phục mật khẩu';
            $mail->Body    = "
                <h3>Xin chào,</h3>
                <p>Chúng tôi nhận được yêu cầu khôi phục mật khẩu cho tài khoản của bạn.</p>
                <p>Vui lòng click vào đường dẫn dưới đây để đặt lại mật khẩu mới. Link này sẽ hết hạn sau 15 phút:</p>
                <p><a href='{$resetLink}' style='padding:10px 20px; background:#111; color:#fff; text-decoration:none;'>Đặt lại mật khẩu</a></p>
                <p>Hoặc copy đường dẫn này dán vào trình duyệt: <br> {$resetLink}</p>
                <p>Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email này.</p>
            ";

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // 4. Hiển thị form đặt lại mật khẩu (Khi người dùng click vào link trong mail)
    public function resetPassword($token = '')
    {
        if (empty($token)) {
            $_SESSION['error'] = 'Đường dẫn không hợp lệ.';
            header('Location: /auth/login');
            exit;
        }

        // Kiểm tra token trong DB
        $user = $this->authModel->getUserByResetToken($token);

        if (!$user) {
            $_SESSION['error'] = 'Đường dẫn đã hết hạn hoặc không tồn tại. Vui lòng gửi lại yêu cầu.';
            header('Location: /auth/forgotPassword');
            exit;
        }

        // Token hợp lệ, hiển thị view đặt lại mật khẩu
        $this->view('Auth/resetPassword', ['token' => $token]);
    }

    // 5. Xử lý cập nhật mật khẩu mới
    public function handleResetPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $token = $_POST['token'];
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            if (empty($password) || strlen($password) < 6) {
                $data['error'] = 'Mật khẩu phải có ít nhất 6 ký tự.';
                $data['token'] = $token;
                $this->view('Auth/resetPassword', $data);
                return;
            }

            if ($password !== $confirm_password) {
                $data['error'] = 'Mật khẩu nhập lại không khớp.';
                $data['token'] = $token;
                $this->view('Auth/resetPassword', $data);
                return;
            }

            // Kiểm tra token lại 1 lần nữa cho chắc
            $user = $this->authModel->getUserByResetToken($token);

            if (!$user) {
                $_SESSION['error'] = 'Đường dẫn đã hết hạn. Vui lòng gửi lại yêu cầu.';
                header('Location: /auth/forgotPassword');
                exit;
            }

            // Cập nhật mật khẩu mới
            if ($this->authModel->updatePassword($user['id'], $password)) {
                $_SESSION['success'] = 'Đặt lại mật khẩu thành công! Bạn có thể đăng nhập bằng mật khẩu mới.';
                header('Location: /auth/login');
                exit;
            } else {
                $data['error'] = 'Có lỗi xảy ra, vui lòng thử lại.';
                $data['token'] = $token;
                $this->view('Auth/resetPassword', $data);
            }
        }
    }
}
