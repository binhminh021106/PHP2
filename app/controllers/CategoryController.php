<?php

class CategoryController extends \Controller
{

    public function __construct()
    {
        $this->checkAdmin();
    }
    
    public function index()
    {
        $categoryModel = $this->model('CategoryModel');
        
        // Xử lý Tìm kiếm và Phân trang
        $search = $_GET['search'] ?? '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 5; // Số lượng danh mục trên 1 trang
        $offset = ($page - 1) * $limit;

        $data = $categoryModel->index($search, $limit, $offset);
        $totalRecords = $categoryModel->getTotalCategories($search);
        $totalPages = ceil($totalRecords / $limit);
        
        $title = "Quản lý danh mục";

        $successMsg = '';
        if (isset($_SESSION['success'])) {
            $successMsg = $_SESSION['success'];
            unset($_SESSION['success']);
        }

        $this->view('Admin/AdminCategory/index', [
            'category' => $data,
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
        $title = "Thêm danh mục mới";

        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);

        $this->view('Admin/AdminCategory/create', [
            'title' => $title,
            'errors' => $errors,
            'old' => $old
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name']);
            $description = trim($_POST['description']);
            $icon = trim($_POST['icon']);
            $status = $_POST['status'];

            $errors = [];
            $categoryModel = $this->model('CategoryModel');

            if (empty($name)) {
                $errors['name'] = "Vui lòng nhập tên danh mục.";
            } else if ($categoryModel->checkNameExists($name)) {
                $errors['name'] = 'Tên danh mục đã tồn tại.';
            }

            if (empty($icon)) {
                $errors['icon'] = "Vui lòng nhập class icon (ví dụ: fa-solid fa-laptop).";
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $_POST;
                header('Location: /category/create');
                exit();
            }

            $data = [
                'name' => $name,
                'description' => $description,
                'icon' => $icon,
                'status' => $status
            ];

            if ($categoryModel->create($data)) {
                $_SESSION['success'] = 'Thêm danh mục mới thành công!';
                header('Location: /category/index');
            } else {
                $_SESSION['errors']['system'] = "Lỗi hệ thống, vui lòng thử lại.";
                header('Location: /category/create');
            }
            exit();
        }
    }

    public function edit($id)
    {
        $categoryModel = $this->model('CategoryModel');
        $data = $categoryModel->show($id);

        if (!$data) {
            header('Location: /category/index');
            exit();
        }

        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);

        $title = "Chỉnh sửa danh mục";
        $this->view('Admin/AdminCategory/edit', [
            'category' => $data,
            'title' => $title,
            'errors' => $errors
        ]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name']);
            $description = trim($_POST['description']);
            $icon = trim($_POST['icon']);
            $status = $_POST['status'];

            $categoryModel = $this->model('CategoryModel');
            $errors = [];

            // --- VALIDATE ---
            if (empty($name)) {
                $errors['name'] = "Vui lòng nhập tên danh mục.";
            } else if ($categoryModel->checkNameExists($name, $id)) {
                $errors['name'] = 'Tên danh mục đã tồn tại.';
            }

            if (empty($icon)) {
                $errors['icon'] = "Vui lòng nhập class icon.";
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header("Location: /category/edit/$id");
                exit();
            }

            $data = [
                'name' => $name,
                'description' => $description,
                'icon' => $icon,
                'status' => $status,
            ];

            if ($categoryModel->update($id, $data)) {
                $_SESSION['success'] = 'Cập nhật danh mục thành công!';
                header('Location: /category/index');
            } else {
                $_SESSION['errors']['system'] = "Cập nhật thất bại.";
                header("Location: /category/edit/$id");
            }
            exit();
        }
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $id = $_POST['delete_id'];
            if (!empty($id)) {
                $this->model('CategoryModel')->destroy($id);
                $_SESSION['success'] = 'Đã xóa danh mục thành công!';
            }
        }
        header('Location: /category/index');
        exit();
    }
}