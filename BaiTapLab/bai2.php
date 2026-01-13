<?php

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'sinhvien';

// Kết nối database
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

class Bai2
{
    private $db;

    public function __construct($conn)
    {
        $this->db = $conn;
    }

    // Lấy danh sách sản phẩm
    public function getProducts()
    {
        $sql = "SELECT * FROM products";
        $result = $this->db->query($sql);
        $products = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }
        return $products;
    }

    // Đếm tổng số danh mục (Dùng cho phân trang)
    public function countCategories()
    {
        $sql = "SELECT COUNT(*) as total FROM category";
        $result = $this->db->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return $row['total'];
        }
        return 0;
    }

    // Lấy danh sách danh mục (Có phân trang)
    public function getCategories($limit = null, $offset = 0)
    {
        $sql = "SELECT * FROM category";
        if ($limit !== null) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        $result = $this->db->query($sql);
        $categories = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
        }
        return $categories;
    }

    // Check danh mục tồn tại
    public function categoryExists($name)
    {
        $stmt = $this->db->prepare("SELECT id FROM category WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    // Thêm danh mục
    public function addCategory($name, $description, $icon)
    {
        $stmt = $this->db->prepare("INSERT INTO category (name, description, icon) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $description, $icon);
        return $stmt->execute();
    }

    // Sửa danh mục
    public function updateCategory($id, $name, $description, $icon)
    {
        $stmt = $this->db->prepare("UPDATE category SET name = ?, description = ?, icon = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $description, $icon, $id);
        return $stmt->execute();
    }

    // Xóa danh mục
    public function deleteCategory($id)
    {
        $stmt = $this->db->prepare("DELETE FROM category WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}


$bai2 = new Bai2($conn);
$message = "";

$active_tab = 'product'; 
if (isset($_GET['page_cat']) || isset($_POST['addcategory']) || isset($_POST['delete_category'])) {
    $active_tab = 'category';
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    
    if (isset($_POST['addcategory'])) {
        $id_goc = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $icon = trim($_POST['icon']);

        if (empty($name) || empty($description) || empty($icon)) {
            $message = "Lỗi: Thông tin không hợp lệ.";
        } else {
            if ($id_goc > 0) {
                if ($bai2->updateCategory($id_goc, $name, $description, $icon)) {
                    $message = "Cập nhật danh mục thành công.";
                } else {
                    $message = "Lỗi: Không thể cập nhật danh mục.";
                }
            } else {
                if ($bai2->categoryExists($name)) {
                    $message = "Lỗi: Danh mục đã tồn tại.";
                } else {
                    if ($bai2->addCategory($name, $description, $icon)) {
                        $message = "Thêm danh mục thành công.";
                    } else {
                        $message = "Lỗi: Không thể thêm danh mục.";
                    }
                }
            }
        }
    }

    if (isset($_POST['delete_category'])) {
        $id_xoa = intval($_POST['delete_id']);
        if($id_xoa > 0){
            if ($bai2->deleteCategory($id_xoa)) {
                $message = "Xóa danh mục thành công.";
            } else {
                $message = "Lỗi: Không thể xóa danh mục.";
            }
        } else {
             $message = "Lỗi: ID danh mục không hợp lệ.";
        }
    }
}

$limit_cat = 5; 
$page_cat = isset($_GET['page_cat']) ? max(1, intval($_GET['page_cat'])) : 1;
$offset_cat = ($page_cat - 1) * $limit_cat;

$total_categories = $bai2->countCategories();
$total_pages_cat = ceil($total_categories / $limit_cat);

$category = $bai2->getCategories($limit_cat, $offset_cat);
$product = $bai2->getProducts();

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Kho Hàng & Sản Phẩm</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }

        .table img.product-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .nav-pills .nav-link.active {
            background-color: #0d6efd;
            box-shadow: 0 4px 6px rgba(13, 110, 253, 0.3);
        }

        .nav-pills .nav-link {
            color: #555;
            font-weight: 500;
            margin-right: 10px;
            border-radius: 8px;
            cursor: pointer;
        }

        .status-badge {
            font-size: 0.8rem;
            padding: 5px 10px;
            border-radius: 20px;
        }

        /* Style cho phần upload nhiều ảnh */
        .image-preview-container {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .preview-box {
            width: 80px;
            height: 80px;
            border: 2px dashed #ccc;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ccc;
            position: relative;
            background-size: cover;
            background-position: center;
        }

        .preview-box .remove-img {
            position: absolute;
            top: -5px;
            right: -5px;
            background: red;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <?php if (!empty($message)): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let msgContent = "<?= addslashes($message) ?>";
                let msgType = msgContent.includes('Lỗi') ? 'error' : 'success';
                let msgTitle = msgContent.includes('Lỗi') ? 'Thất bại!' : 'Thành công!';

                Swal.fire({
                    title: msgTitle,
                    text: msgContent,
                    icon: msgType,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Đóng'
                });
            });
        </script>
    <?php endif; ?>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#"><i class="fas fa-boxes me-2"></i>Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid px-4">

        <!-- Tab Navigation -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="text-secondary fw-bold mb-0">Quản Lý Cửa Hàng</h3>
            <ul class="nav nav-pills" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $active_tab == 'product' ? 'active' : '' ?>" id="pills-product-tab" data-bs-toggle="pill" data-bs-target="#pills-product" type="button" role="tab"><i class="fas fa-box-open me-2"></i>Sản Phẩm</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $active_tab == 'category' ? 'active' : '' ?>" id="pills-category-tab" data-bs-toggle="pill" data-bs-target="#pills-category" type="button" role="tab"><i class="fas fa-tags me-2"></i>Danh Mục</button>
                </li>
            </ul>
        </div>

        <div class="tab-content" id="pills-tabContent">

            <!-- PRODUCT TAB -->
            <div class="tab-pane fade <?= $active_tab == 'product' ? 'show active' : '' ?>" id="pills-product" role="tabpanel">
                <div class="card">
                    <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center">
                        <div class="d-flex gap-2 mb-2 mb-md-0">
                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#productModal">
                                <i class="fas fa-plus me-1"></i> Thêm Sản Phẩm
                            </button>
                        </div>
                        <div class="input-group" style="max-width: 300px;">
                            <input type="text" class="form-control" placeholder="Tìm kiếm sản phẩm...">
                            <button class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="60">STT</th>
                                        <th width="80">Hình ảnh</th>
                                        <th>Tên sản phẩm</th>
                                        <th>Danh mục</th>
                                        <th>Giá bán</th>
                                        <th>Tồn kho</th>
                                        <th>Trạng thái</th>
                                        <th width="120" class="text-end">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $stt = 1; ?>
                                    <?php foreach ($product as $items): ?>
                                        <tr>
                                            <td><?= $stt++; ?></td>
                                            <td><img src="https://via.placeholder.com/150" alt="sp" class="product-img"></td>
                                            <td>
                                                <div class="fw-bold"><?= htmlspecialchars($items['name'])  ?></div>
                                            </td>
                                            <td>Điện thoại</td>
                                            <td class="fw-bold text-danger"><?= htmlspecialchars($items['price']) ?>₫</td>
                                            <td><?= htmlspecialchars($items['quantity']) ?></td>
                                            <td><span class="badge bg-success bg-opacity-10 text-success status-badge"><?= htmlspecialchars($items['status']) ?></span></td>
                                            <td class="text-end">
                                                <button class="btn btn-sm btn-outline-primary btn-icon" data-bs-toggle="modal" data-bs-target="#productModal"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-sm btn-outline-danger btn-icon"><i class="fas fa-trash-alt"></i></button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>

                                    <?php if (empty($product)): ?>
                                        <tr>
                                            <td colspan="12" class="text-center text-muted py-3">
                                                Không có dữ liệu sản phẩm nào.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Footer phân trang sản phẩm -->
                    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                       <small class="text-muted">...</small>
                    </div>
                </div>
            </div>

            <!-- CATEGORY TAB -->
            <div class="tab-pane fade <?= $active_tab == 'category' ? 'show active' : '' ?>" id="pills-category" role="tabpanel">
                <div class="card">
                    <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center">
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                                <i class="fas fa-plus me-1"></i> Thêm Danh Mục
                            </button>
                        </div>
                        <div class="input-group" style="max-width: 300px;">
                            <input type="text" class="form-control" placeholder="Tìm kiếm danh mục...">
                            <button class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="80">ID</th>
                                        <th>Tên danh mục</th>
                                        <th>Mô tả</th>
                                        <th>Icon</th>
                                        <th width="120" class="text-end">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Tính STT liên tục qua các trang -->
                                    <?php $stt = $offset_cat + 1; ?>
                                    <?php foreach ($category as $items): ?>
                                        <tr>
                                            <td><?= $stt++; ?></td>
                                            <td class="fw-bold"><?= htmlspecialchars($items['name']) ?></td>
                                            <td class="text-muted"><?= htmlspecialchars($items['description']) ?></td>
                                            <td><i class="<?= htmlspecialchars($items['icon']) ?>"></i> <?= htmlspecialchars($items['icon']) ?></td>
                                            <td class="text-end">
                                                <button class="btn btn-sm btn-outline-primary btn-icon" data-bs-toggle="modal" data-bs-target="#categoryModal"><i class="fas fa-edit"></i></button>
                                                
                                                <!-- NÚT XÓA DANH MỤC -->
                                                <button class="btn btn-sm btn-outline-danger btn-icon" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteModal"
                                                        data-id="<?= $items['id'] ?>">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>

                                    <?php if (empty($category)): ?>
                                        <tr>
                                            <td colspan="12" class="text-center text-muted py-3">
                                                Không có dữ liệu danh mục nào.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- PHÂN TRANG DANH MỤC -->
                    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            Hiển thị <?= !empty($category) ? ($offset_cat + 1) : 0 ?>-<?= ($offset_cat + count($category)) ?> trên tổng số <?= $total_categories ?> danh mục
                        </small>
                        
                        <?php if ($total_pages_cat > 1): ?>
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                                <!-- Nút Trước -->
                                <li class="page-item <?= ($page_cat <= 1) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page_cat=<?= $page_cat - 1 ?>#pills-category">Trước</a>
                                </li>

                                <!-- Các trang số -->
                                <?php for ($i = 1; $i <= $total_pages_cat; $i++): ?>
                                    <li class="page-item <?= ($i == $page_cat) ? 'active' : '' ?>">
                                        <a class="page-link" href="?page_cat=<?= $i ?>#pills-category"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>

                                <!-- Nút Sau -->
                                <li class="page-item <?= ($page_cat >= $total_pages_cat) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page_cat=<?= $page_cat + 1 ?>#pills-category">Sau</a>
                                </li>
                            </ul>
                        </nav>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: THÊM/SỬA SẢN PHẨM -->
     <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Thông tin sản phẩm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                 <div class="modal-body">
                    <p>Form sản phẩm ở đây...</p>
                 </div>
            </div>
        </div>
    </div>

    <!-- MODAL: THÊM/SỬA DANH MỤC -->
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Thông tin danh mục</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post">
                        <input type="hidden" name="id" id="cat_id_edit"> 
                        <div class="mb-3">
                            <label class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" placeholder="Ví dụ: Laptop Gaming">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả ngắn</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Icon <span class="text-danger">*</span></label>
                            <input name="icon" type="text" class="form-control" placeholder="Ví dụ: fas fa-laptop">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" name="addcategory" class="btn btn-primary">Lưu thay đổi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: XÁC NHẬN XÓA -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pb-4">
                    <i class="fas fa-exclamation-circle text-danger display-1 mb-3"></i>
                    <h4 class="fw-bold">Bạn có chắc chắn?</h4>
                    <p class="text-muted">Hành động này sẽ xóa dữ liệu vĩnh viễn và không thể khôi phục.</p>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="delete_id" id="delete_id_input">
                        <div class="d-flex justify-content-center gap-2 mt-4">
                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Không, hủy bỏ</button>
                            <button type="submit" name="delete_category" class="btn btn-danger px-4">Có, xóa ngay</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const deleteModal = document.getElementById('deleteModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const modalInput = deleteModal.querySelector('#delete_id_input');
                modalInput.value = id;
            });
        }
    </script>
</body>

</html>