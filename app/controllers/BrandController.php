<?php

class BrandController extends Controller
{
    public function index() 
    {
        $brand = $this->model('BrandModel');
        $data = $brand->index();
        $title = "Quản lí Brand";
        $this->view('AdminBrand/index', [
            'brands' => $data,
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
            // 1. Lấy dữ liệu text và sanitize (làm sạch)
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            
            // Validate cơ bản
            if (empty($name)) {
                // Xử lý lỗi nếu tên trống (bạn có thể thêm flash message ở đây)
                header('Location: /brand/create?error=name_required');
                exit;
            }

            // 2. Xử lý Upload hình ảnh
            $imageName = ""; // Mặc định là rỗng nếu không up ảnh

            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                // Định nghĩa thư mục lưu trữ (Đường dẫn tính từ file index.php gốc)
                $targetDir = "storage/uploads/brands/";
                
                // Tạo thư mục nếu chưa tồn tại
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                // Lấy thông tin file
                $fileName = $_FILES['image']['name'];
                $fileTmp = $_FILES['image']['tmp_name'];
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                // Các định dạng cho phép
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];

                if (in_array($fileExt, $allowed)) {
                    // Tạo tên file mới để tránh trùng lặp (vd: 65a123bc.jpg)
                    $uniqueName = uniqid() . '.' . $fileExt;
                    $targetFilePath = $targetDir . $uniqueName;

                    // Di chuyển file từ thư mục tạm sang thư mục đích
                    if (move_uploaded_file($fileTmp, $targetFilePath)) {
                        $imageName = $uniqueName; // Lưu tên file để đưa vào DB
                    } else {
                        // Lỗi khi di chuyển file
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

            // 4. Điều hướng
            if ($result) {
                header('Location: /brand');
            } else {
                header('Location: /brand/create?error=insert_failed');
            }
            exit;
        }
    }
}