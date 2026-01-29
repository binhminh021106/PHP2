<?php

class BrandController extends Controller
{
    public function index()
    {
        $brand = $this->model('BrandModel');
        $data = $brand->index();

        $successMsg = '';
        if (isset($_SESSION['success'])) {
            $successMsg = $_SESSION['success'];
            unset($_SESSION['success']);
        }

        $this->view('AdminBrand/index', [
            'brand' => $data,
            'title' => "Quản lý Thương hiệu",
            'success_msg' => $successMsg
        ]);
    }

    public function create()
    {
        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];

        unset($_SESSION['errors']);
        unset($_SESSION['old']);

        $this->view('AdminBrand/create', [
            'title' => "Thêm thương hiệu",
            'errors' => $errors,
            'old' => $old
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            $status = isset($_POST['status']) ? $_POST['status'] : 'active';

            $errors = [];

            if (empty($name)) {
                $errors['name'] = 'Tên thương hiệu không được để trống';
            }

            $brandModel = $this->model('BrandModel');
            $allBrands = $brandModel->index();
            foreach ($allBrands as $b) {
                if (strcasecmp($b['name'], $name) == 0) {
                    $errors['name'] = 'Tên thương hiệu đã tồn tại';
                    break;
                }
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $_POST;
                header('Location: /brand/create');
                exit;
            }

            $imageName = "";
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $targetDir = "storage/uploads/brands/";
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                $fileName = $_FILES['image']['name'];
                $fileTmp = $_FILES['image']['tmp_name'];
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (in_array($fileExt, $allowed)) {
                    $uniqueName = uniqid() . '.' . $fileExt;
                    $targetFilePath = $targetDir . $uniqueName;
                    if (move_uploaded_file($fileTmp, $targetFilePath)) {
                        $imageName = $uniqueName;
                    }
                }
            }

            $result = $brandModel->create([
                'name' => $name,
                'image' => $imageName,
                'description' => $description,
                'status' => $status
            ]);

            if ($result) {
                $_SESSION['success'] = 'Thêm thương hiệu mới thành công!';
                header('Location: /brand');
            } else {
                $_SESSION['errors']['name'] = 'Lỗi hệ thống, vui lòng thử lại.';
                $_SESSION['old'] = $_POST;
                header('Location: /brand/create');
            }
            exit;
        }
    }

    public function edit($id)
    {
        $data = $this->model('BrandModel')->show($id);

        if (!$data) {
            header("Location: /brand");
            exit();
        }

        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);

        $this->view('AdminBrand/edit', [
            'brand' => $data,
            'title' => "Cập nhật thương hiệu",
            'errors' => $errors
        ]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            $status = isset($_POST['status']) ? $_POST['status'] : 'active';

            $errors = [];
            if (empty($name)) {
                $errors['name'] = 'Tên thương hiệu không được để trống';
            }

            $brandModel = $this->model('BrandModel');
            $allBrands = $brandModel->index();
            foreach ($allBrands as $b) {
                if (strcasecmp($b['name'], $name) == 0 && $b['id'] != $id) {
                    $errors['name'] = 'Tên thương hiệu đã tồn tại';
                    break;
                }
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header("Location: /brand/edit/$id");
                exit;
            }

            $currentBrand = $brandModel->show($id);
            if (!$currentBrand) {
                header('Location: /brand');
                exit;
            }

            $imageName = $currentBrand['image'];

            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $targetDir = "storage/uploads/brands/";
                if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);

                $fileName = $_FILES['image']['name'];
                $fileTmp = $_FILES['image']['tmp_name'];
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (in_array($fileExt, $allowed)) {
                    $uniqueName = uniqid() . '.' . $fileExt;
                    $targetFilePath = $targetDir . $uniqueName;

                    if (move_uploaded_file($fileTmp, $targetFilePath)) {
                        $imageName = $uniqueName;

                        $oldPath = $targetDir . $currentBrand['image'];
                        if (!empty($currentBrand['image']) && file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                    }
                }
            }

            $result = $brandModel->update($id, [
                'name' => $name,
                'image' => $imageName,
                'description' => $description,
                'status' => $status
            ]);

            if ($result) {
                $_SESSION['success'] = 'Cập nhật thông tin thành công!';
                header('Location: /brand');
            } else {
                $_SESSION['errors']['name'] = 'Cập nhật thất bại do lỗi hệ thống.';
                header("Location: /brand/edit/$id");
            }
            exit;
        }
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $id = $_POST['delete_id'] ?? '';

            if (!empty($id)) {
                $brandModel = $this->model('BrandModel');
                $brand = $brandModel->show($id);

                if ($brand && !empty($brand['image'])) {
                    $imagePath = "storage/uploads/brands/" . $brand['image'];
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }

                $brandModel->destroy($id);
                $_SESSION['success'] = 'Đã xóa thương hiệu thành công!';
            }

            header('Location: /brand');
            exit;
        }
    }
}
