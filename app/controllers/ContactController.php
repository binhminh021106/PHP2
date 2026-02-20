<?php

class ContactController extends \Controller
{
    public function __construct()
    {
        $this->checkAdmin();
    }

    public function index()
    {
        $contactModel = $this->model('ContactModel');

        // 1. Xử lý Tìm kiếm
        $search = $_GET['search'] ?? '';
        $search = trim($search);

        // 2. Xử lý Phân trang
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;

        $limit = 5; // Số lượng hiển thị trên 1 trang
        $offset = ($page - 1) * $limit;

        // 3. Gọi Model
        // Lấy tổng số dòng (để tính tổng số trang)
        $totalRecords = $contactModel->countContacts($search);

        // Lấy dữ liệu cho trang hiện tại
        $data = $contactModel->getContacts($search, $limit, $offset);

        // Tính tổng số trang
        $totalPages = ceil($totalRecords / $limit);

        $title = "Quản lý liên hệ";

        // Flash message
        $successMsg = '';
        if (isset($_SESSION['success'])) {
            $successMsg = $_SESSION['success'];
            unset($_SESSION['success']);
        }

        // 4. Truyền hết dữ liệu sang View
        $this->view('Admin/AdminContact/index', [
            'contact' => $data,
            'title' => $title,
            'success_msg' => $successMsg,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'search' => $search,
            'total_records' => $totalRecords
        ]);
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $id = $_POST['delete_id'];
            if (!empty($id)) {
                $this->model('ContactModel')->destroy($id);
                $_SESSION['success'] = 'Đã xóa liên hệ thành công!';
            }
        }
        header('Location: /contact/index');
        exit();
    }
}
