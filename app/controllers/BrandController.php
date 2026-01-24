<?php

class BrandController extends Controller
{
    public function index()
    {
        $brand = $this->model('BrandModel');
        $data = $brand->index();

        // Lấy thông báo thành công từ Session (nếu có) để hiển thị SweetAlert
        $successMsg = '';
        if (isset($_SESSION['success'])) {
            $successMsg = $_SESSION['success'];
            unset($_SESSION['success']); // Xóa ngay sau khi lấy để không hiện lại khi F5
        }

        $this->view('AdminBrand/index', [
            'brand' => $data,
            'title' => "Quản lí Brand",
            'success_msg' => $successMsg // Truyền biến này xuống view
        ]);
    }

    public function create()
    {
        // Lấy lỗi và dữ liệu cũ từ Session (nếu có) do validate thất bại
        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];

        // Xóa session flash sau khi lấy
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

            // --- 1. VALIDATE DỮ LIỆU ---
            $errors = [];

            // Kiểm tra rỗng
            if (empty($name)) {
                $errors['name'] = 'Tên thương hiệu không được để trống';
            }

            // Kiểm tra trùng tên (Lấy hết ra check thủ công vì chưa có hàm check trong model)
            $brandModel = $this->model('BrandModel');
            $allBrands = $brandModel->index();
            foreach ($allBrands as $b) {
                // strcasecmp so sánh chuỗi không phân biệt hoa thường
                if (strcasecmp($b['name'], $name) == 0) {
                    $errors['name'] = 'Tên thương hiệu đã tồn tại';
                    break;
                }
            }

            // Nếu có lỗi -> Redirect lại trang create kèm thông tin lỗi
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $_POST; // Giữ lại dữ liệu vừa nhập
                header('Location: /brand/create');
                exit;
            }

            // --- 2. XỬ LÝ UPLOAD ẢNH ---
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
                    }
                }
            }

            // --- 3. LƯU VÀO DATABASE ---
            $result = $brandModel->create([
                'name' => $name,
                'image' => $imageName,
                'description' => $description
            ]);

            if ($result) {
                $_SESSION['success'] = 'Thêm thương hiệu mới thành công!';
                header('Location: /brand');
            } else {
                // Nếu lỗi hệ thống khi insert
                $_SESSION['errors']['name'] = 'Lỗi hệ thống, không thể thêm mới. Vui lòng thử lại.';
                $_SESSION['old'] = $_POST;
                header('Location: /brand/create');
            }
            exit;
        }
    }

    public function show($id)
    {
        $brand = $this->model('BrandModel');
        $data = $brand->show($id);
        $title = "Xem chi tiết brand";
        // Do view index đang lặp foreach nên bọc $data vào mảng
        $this->view('AdminBrand/index', [
            'brand' => [$data],
            'title' => $title
        ]);
    }

    public function edit($id)
    {
        $data = $this->model('BrandModel')->show($id);

        if (!$data) {
            header("Location: /brand");
            exit();
        }

        // Lấy lỗi từ Session (nếu update thất bại redirect về)
        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);

        $this->view('AdminBrand/edit', [
            'brand' => $data,
            'title' => "Sửa thương hiệu",
            'errors' => $errors
        ]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';

            // --- VALIDATE UPDATE ---
            $errors = [];
            if (empty($name)) {
                $errors['name'] = 'Tên thương hiệu không được để trống';
            }

            // Check trùng tên (trừ chính bản thân nó ra)
            $brandModel = $this->model('BrandModel');
            $allBrands = $brandModel->index();
            foreach ($allBrands as $b) {
                // Nếu trùng tên VÀ id không phải id đang sửa
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

            // Lấy thông tin cũ
            $currentBrand = $brandModel->show($id);
            if (!$currentBrand) {
                header('Location: /brand');
                exit;
            }

            $imageName = $currentBrand['image']; // Mặc định giữ ảnh cũ

            // Nếu có upload ảnh mới
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $targetDir = "storage/uploads/brands/";
                if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);

                $fileName = $_FILES['image']['name'];
                $fileTmp = $_FILES['image']['tmp_name'];
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];

                if (in_array($fileExt, $allowed)) {
                    $uniqueName = uniqid() . '.' . $fileExt;
                    $targetFilePath = $targetDir . $uniqueName;

                    if (move_uploaded_file($fileTmp, $targetFilePath)) {
                        $imageName = $uniqueName; // Cập nhật tên ảnh mới

                        // Xóa ảnh cũ nếu tồn tại
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
                'description' => $description
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

                // Xóa file ảnh vật lý trước nếu có
                if ($brand && !empty($brand['image'])) {
                    $imagePath = "storage/uploads/brands/" . $brand['image'];
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }

                // Sau đó xóa trong DB
                $brandModel->destroy($id);
                $_SESSION['success'] = 'Đã xóa thương hiệu thành công!';
            }

            header('Location: /brand');
            exit;
        }
    }
}
