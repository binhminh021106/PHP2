<?php

class BrandController extends Controller
{
    public function index()
    {
        $brand = $this->model('BrandModel');
        $data = $brand->index();
        $title = "Quản lí Brand";
        $this->view('AdminBrand/index', [
            'brand' => $data,
            'title' => $title
        ]);
    }

    public function create()
    {
        $title = "Thêm thương hiệu";
        $this->view('AdminBrand/create', ['title' => $title]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';

            if (empty($name)) {
                header('Location: /brand/create?error=name_required');
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

                $allowed = ['jpg', 'jpeg', 'png', 'gif'];

                if (in_array($fileExt, $allowed)) {
                    $uniqueName = uniqid() . '.' . $fileExt;
                    $targetFilePath = $targetDir . $uniqueName;
                    if (move_uploaded_file($fileTmp, $targetFilePath)) {
                        $imageName = $uniqueName;
                    } else {
                        $imageName = "";
                    }
                } else {
                    // Lỗi định dạng không cho phép
                    // handle error...
                }
            }

            $brandModel = $this->model('BrandModel');

            $result = $brandModel->create([
                'name' => $name,
                'image' => $imageName,
                'description' => $description
            ]);

            if ($result) {
                header('Location: /brand');
            } else {
                header('Location: /brand/create?error=insert_failed');
            }
            exit;
        }
    }

    public function show($id)
    {
        $brand = $this->model('BrandModel');
        $data = $brand->show($id);
        $title = "Xem chi tiết brand";
        $this->view('AdminBrand/index', [
            'brand' => $data,
            'title' => $title
        ]);
    }

    public function edit($id)
    {
        $brandModel = $this->model('BrandModel');
        $data = $brandModel->show($id);

        if (!$data) {
            header("Location: brand/index");
            exit();
        }

        $title = "Sửa thương hiệu";
        $this->view('AdminBrand/edit', [
            'brand' => $data,
            'title' => $title
        ]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        }
    }
}
