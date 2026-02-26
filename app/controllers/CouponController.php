<?php

class CouponController extends \Controller
{
    public function __construct()
    {
        $this->checkAdmin();
    }

    public function index()
    {
        $couponModel = $this->model('CouponModel');
        
        // Xử lý Tìm kiếm và Phân trang
        $search = $_GET['search'] ?? '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 5; // Số lượng mã giảm giá trên 1 trang
        $offset = ($page - 1) * $limit;

        $data = $couponModel->index($search, $limit, $offset);
        $totalRecords = $couponModel->getTotalCoupons($search);
        $totalPages = ceil($totalRecords / $limit);

        $title = "Quản lý mã giảm giá";

        $successMsg = '';
        if (isset($_SESSION['success'])) {
            $successMsg = $_SESSION['success'];
            unset($_SESSION['success']);
        }

        $this->view('Admin/AdminCoupon/index', [
            'coupons' => $data,
            'title' => $title,
            'success_msg' => $successMsg,
            'search' => $search,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_records' => $totalRecords
        ]);
    }

    public function create()
    {
        $title = "Thêm mã giảm giá mới";
        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);

        $this->view('Admin/AdminCoupon/create', [
            'title' => $title,
            'errors' => $errors,
            'old' => $old
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $code = strtoupper(trim($_POST['code']));
            $type = $_POST['type'];
            $value = trim($_POST['value']);
            $quantity = trim($_POST['quantity']);
            $expired_at = $_POST['expired_at'];
            $status = $_POST['status'];

            $couponModel = $this->model('CouponModel');
            $errors = [];

            if (empty($code)) {
                $errors['code'] = "Vui lòng nhập mã giảm giá.";
            } else if ($couponModel->checkCodeExists($code)) {
                $errors['code'] = "Mã code này đã tồn tại.";
            }

            if (empty($value) || !is_numeric($value) || $value < 0) {
                $errors['value'] = "Giá trị giảm phải là số dương.";
            }
            if (empty($quantity) || !is_numeric($quantity) || $quantity < 0) {
                $errors['quantity'] = "Số lượng phải là số dương.";
            }
            if (empty($expired_at)) {
                $errors['expired_at'] = "Vui lòng chọn ngày hết hạn.";
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $_POST;
                header('Location: /coupon/create');
                exit();
            }

            $data = [
                'code' => $code,
                'type' => $type,
                'value' => $value,
                'quantity' => $quantity,
                'status' => $status,
                'expired_at' => $expired_at
            ];

            if ($couponModel->create($data)) {
                $_SESSION['success'] = 'Thêm mã giảm giá thành công!';
                header('Location: /coupon/index');
            } else {
                $_SESSION['errors']['system'] = "Lỗi hệ thống, vui lòng thử lại.";
                header('Location: /coupon/create');
            }
            exit();
        }
    }

    public function edit($id)
    {
        $couponModel = $this->model('CouponModel');
        $data = $couponModel->show($id);

        if (!$data) {
            header('Location: /coupon/index');
            exit();
        }

        $title = "Chỉnh sửa mã giảm giá";
        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);

        $this->view('Admin/AdminCoupon/edit', [
            'coupon' => $data,
            'title' => $title,
            'errors' => $errors
        ]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $code = strtoupper(trim($_POST['code']));
            $type = $_POST['type'];
            $value = trim($_POST['value']);
            $quantity = trim($_POST['quantity']);
            $expired_at = $_POST['expired_at'];
            $status = $_POST['status'];

            $couponModel = $this->model('CouponModel');
            $errors = [];

            if (empty($code)) {
                $errors['code'] = "Vui lòng nhập mã giảm giá.";
            } else if ($couponModel->checkCodeExists($code, $id)) {
                $errors['code'] = "Mã code này đã tồn tại.";
            }

            if (empty($value) || !is_numeric($value) || $value < 0) {
                $errors['value'] = "Giá trị không hợp lệ.";
            }
            if (empty($quantity) || !is_numeric($quantity) || $quantity < 0) {
                $errors['quantity'] = "Số lượng không hợp lệ.";
            }
            if (empty($expired_at)) {
                $errors['expired_at'] = "Vui lòng chọn ngày hết hạn.";
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header("Location: /coupon/edit/$id");
                exit();
            }

            $data = [
                'code' => $code,
                'type' => $type,
                'value' => $value,
                'quantity' => $quantity,
                'status' => $status,
                'expired_at' => $expired_at
            ];

            if ($couponModel->update($id, $data)) {
                $_SESSION['success'] = 'Cập nhật mã giảm giá thành công!';
                header('Location: /coupon/index');
            } else {
                $_SESSION['errors']['system'] = "Cập nhật thất bại.";
                header("Location: /coupon/edit/$id");
            }
            exit();
        }
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $id = $_POST['delete_id'];
            if (!empty($id)) {
                $this->model('CouponModel')->destroy($id);
                $_SESSION['success'] = 'Đã xóa mã giảm giá thành công!';
            }
        }
        header('Location: /coupon/index');
        exit();
    }
}