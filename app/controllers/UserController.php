<?php

class UserController extends Controller
{
    // Hiển thị danh sách User
    public function index()
    {
        $userModel = $this->model('UserModel');
        $users = $userModel->index();

        $successMsg = '';
        if (isset($_SESSION['success'])) {
            $successMsg = $_SESSION['success'];
            unset($_SESSION['success']);
        }

        $this->view('AdminUser/index', [
            'user' => $users,
            'title' => "Quản lý thành viên",
            'success_msg' => $successMsg
        ]);
    }

    // Form thêm mới
    public function create()
    {
        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);

        $this->view('AdminUser/create', [
            'title' => "Thêm thành viên mới",
            'errors' => $errors,
            'old' => $old
        ]);
    }

    // Xử lý lưu User mới
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';
            $address = isset($_POST['address']) ? trim($_POST['address']) : '';
            $status = isset($_POST['status']) ? trim($_POST['status']) : '';

            $errors = [];

            if (empty($name)) $errors['name'] = "Vui lòng nhập họ tên.";
            if (empty($email)) $errors['email'] = "Vui lòng nhập email.";
            if (empty($address)) $errors['address'] = "Vui lòng nhập địa chỉ.";

            if (empty($phone)) {
                $errors['phone'] = "Vui lòng nhập số điện thoại.";
            } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
                $errors['phone'] = "Số điện thoại không hợp lệ (phải là 10 số).";
            } else {
                $userModel = $this->model('UserModel');
                if ($userModel->checkPhoneExists($phone)) {
                    $errors['phone'] = "Số điện thoại này đã được sử dụng.";
                }
            }

            if (empty($password)) {
                $errors['password'] = "Vui lòng nhập mật khẩu.";
            } elseif (strlen($password) < 8) {
                $errors['password'] = "Mật khẩu phải có ít nhất 8 kí tự.";
            } elseif (!preg_match('/[A-Z]/', $password)) {
                $errors['password'] = "Mật khẩu phải chứa ít nhất 1 chữ hoa.";
            } elseif (!preg_match('/[0-9]/', $password)) {
                $errors['password'] = "Mật khẩu phải chứa ít nhất 1 số.";
            } elseif (!preg_match('/[\W_]/', $password)) {
                $errors['password'] = "Phải có kí tự đặc biệt";
            }

            $userModel = $this->model('UserModel');
            if ($userModel->checkEmailExists($email)) {
                $errors['email'] = "Email này đã được sử dụng.";
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $_POST;
                header('Location: /user/create');
                exit;
            }

            $avatarUrl = null;
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
                $avatarUrl = $this->uploadImage($_FILES['avatar']);
            }

            $result = $userModel->create([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'password' => $password,
                'address' => $address,
                'avatar_url' => $avatarUrl,
                'status' => $status
            ]);

            if ($result) {
                $_SESSION['success'] = "Thêm thành viên thành công!";
                header('Location: /user');
            } else {
                $_SESSION['errors']['system'] = "Lỗi hệ thống, vui lòng thử lại.";
                header('Location: /user/create');
            }
            exit;
        }
    }

    // Form sửa User
    public function edit($id)
    {
        $userModel = $this->model('UserModel');
        $user = $userModel->show($id);

        if (!$user) {
            header('Location: /user');
            exit;
        }

        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);

        $this->view('AdminUser/edit', [
            'user' => $user,
            'title' => "Cập nhật thành viên",
            'errors' => $errors
        ]);
    }

    // Xử lý cập nhật User
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';
            $address = isset($_POST['address']) ? trim($_POST['address']) : '';
            $status = isset($_POST['status']) ? trim($_POST['status']) : '';

            $errors = [];

            if (empty($name)) $errors['name'] = "Vui lòng nhập họ tên.";
            if (empty($email)) $errors['email'] = "Vui lòng nhập email.";
            if (empty($address)) $errors['address'] = "Vui lòng nhập địa chỉ.";

            if (empty($phone)) {
                $errors['phone'] = "Vui lòng nhập số điện thoại.";
            } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
                $errors['phone'] = "Số điện thoại không hợp lệ (phải là 10 số).";
            } else {
                $userModel = $this->model('UserModel');
                if ($userModel->checkPhoneExists($phone, $id)) {
                    $errors['phone'] = "Số điện thoại này đã được sử dụng.";
                }
            }

            if (!empty($password)) {
                if (strlen($password) < 8) {
                    $errors['password'] = "Mật khẩu mới phải có ít nhất 8 kí tự.";
                } elseif (!preg_match('/[A-Z]/', $password)) {
                    $errors['password'] = "Mật khẩu mới phải chứa ít nhất 1 chữ hoa.";
                } elseif (!preg_match('/[0-9]/', $password)) {
                    $errors['password'] = "Mật khẩu mới phải chứa ít nhất 1 số.";
                } elseif (!preg_match('/[\W_]/', $password)) {
                    $errors['password'] = "Phải có kí tự đặc biệt";
                }
            }

            $userModel = $this->model('UserModel');

            if ($userModel->checkEmailExists($email, $id)) {
                $errors['email'] = "Email này đã được sử dụng.";
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header("Location: /user/edit/$id");
                exit;
            }

            $currentUser = $userModel->show($id);
            $avatarUrl = $currentUser['avatar_url'];

            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
                $newAvatar = $this->uploadImage($_FILES['avatar']);
                if ($newAvatar) {
                    $avatarUrl = $newAvatar;
                    if ($currentUser['avatar_url'] && file_exists('storage/uploads/users/' . $currentUser['avatar_url'])) {
                        unlink('storage/uploads/users/' . $currentUser['avatar_url']);
                    }
                }
            }

            $userModel->update($id, [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'password' => $password,
                'address' => $address,
                'avatar_url' => $avatarUrl,
                'status' => $status
            ]);

            $_SESSION['success'] = "Cập nhật thành viên thành công!";
            header('Location: /user');
            exit;
        }
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['delete_id'] ?? '';
            if ($id) {
                $this->model('UserModel')->delete($id);
                $_SESSION['success'] = "Đã xóa người dùng.";
            }
            header('Location: /user');
            exit;
        }
    }

    private function uploadImage($file)
    {
        $targetDir = "storage/uploads/users/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = $file['name'];
        $fileTmp = $file['tmp_name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($fileExt, $allowed)) {
            $uniqueName = uniqid() . '.' . $fileExt;
            if (move_uploaded_file($fileTmp, $targetDir . $uniqueName)) {
                return $uniqueName;
            }
        }
        return null;
    }
}
