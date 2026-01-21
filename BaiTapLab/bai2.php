<?php
// [UPDATE 1] Khởi động Session và Buffer ở ngay dòng đầu tiên
session_start();
ob_start(); 

// === KẾT NỐI DB ===
$conn = new mysqli('localhost', 'root', '', 'sinhvien');
if ($conn->connect_error) die("Kết nối lỗi: " . $conn->connect_error);
$conn->set_charset("utf8mb4");

// === MODEL ===
class Bai2 {
    private $db;
    public function __construct($conn) { $this->db = $conn; }

    // --- DANH MỤC ---
    public function countCategories() {
        return $this->db->query("SELECT COUNT(*) as total FROM category")->fetch_assoc()['total'] ?? 0;
    }
    public function getCategories($limit = null, $offset = 0) {
        $sql = "SELECT * FROM category" . ($limit !== null ? " LIMIT $limit OFFSET $offset" : "");
        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }
    public function getAllCategories() { return $this->db->query("SELECT * FROM category")->fetch_all(MYSQLI_ASSOC); }
    
    public function categoryExists($name) {
        $stmt = $this->db->prepare("SELECT id FROM category WHERE name = ?");
        $stmt->bind_param("s", $name); $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
    public function addCategory($name, $desc, $icon) {
        $stmt = $this->db->prepare("INSERT INTO category (name, description, icon) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $desc, $icon); return $stmt->execute();
    }
    public function updateCategory($id, $name, $desc, $icon) {
        $stmt = $this->db->prepare("UPDATE category SET name=?, description=?, icon=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $desc, $icon, $id); return $stmt->execute();
    }
    public function deleteCategory($id) {
        $stmt = $this->db->prepare("DELETE FROM category WHERE id=?");
        $stmt->bind_param("i", $id); return $stmt->execute();
    }

    // --- SẢN PHẨM ---
    public function countProducts() {
        return $this->db->query("SELECT COUNT(*) as total FROM products")->fetch_assoc()['total'] ?? 0;
    }

    public function getProducts($limit = null, $offset = 0) {
        $sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN category c ON p.category_id = c.id ORDER BY p.id DESC";
        if ($limit !== null) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getProductImages($pid) {
        $stmt = $this->db->prepare("SELECT * FROM image_product WHERE product_id = ?");
        $stmt->bind_param("i", $pid); $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function addProduct($name, $cid, $price, $qty, $status, $avatar) {
        $stmt = $this->db->prepare("INSERT INTO products (name, category_id, price, quantity, status, avatar_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("siiiss", $name, $cid, $price, $qty, $status, $avatar);
        return $stmt->execute() ? $this->db->insert_id : false;
    }

    public function addProductGallery($pid, $url) {
        $stmt = $this->db->prepare("INSERT INTO image_product (product_id, image_url) VALUES (?, ?)");
        $stmt->bind_param("is", $pid, $url); return $stmt->execute();
    }

    public function updateProduct($id, $name, $cid, $price, $qty, $status, $avatar = null) {
        $sql = "UPDATE products SET name=?, category_id=?, price=?, quantity=?, status=?" . ($avatar ? ", avatar_url=?" : "") . " WHERE id=?";
        $stmt = $this->db->prepare($sql);
        if ($avatar) $stmt->bind_param("siiissi", $name, $cid, $price, $qty, $status, $avatar, $id);
        else $stmt->bind_param("siiisi", $name, $cid, $price, $qty, $status, $id);
        return $stmt->execute();
    }

    public function deleteProduct($id) {
        $prod = $this->db->query("SELECT avatar_url FROM products WHERE id=$id")->fetch_assoc();
        if ($prod && file_exists($prod['avatar_url'])) unlink($prod['avatar_url']);
        
        $imgs = $this->getProductImages($id);
        foreach ($imgs as $img) if (file_exists($img['image_url'])) unlink($img['image_url']);

        $this->db->query("DELETE FROM image_product WHERE product_id = $id");
        $this->db->query("DELETE FROM products WHERE id = $id");
        return true;
    }
}

// === CONTROLLER ===
$bai2 = new Bai2($conn);
$active_tab = 'product';

if (isset($_GET['page_cat']) || isset($_POST['addcategory']) || isset($_POST['delete_category'])) $active_tab = 'category';
if (isset($_GET['page_prod']) || isset($_POST['save_product']) || isset($_POST['delete_product'])) $active_tab = 'product';

// [UPDATE 2] Hàm hỗ trợ Redirect sau khi xử lý xong
function redirectWithMsg($msg) {
    $_SESSION['flash_msg'] = $msg;
    // Giữ lại query string (page, tab...)
    $url = $_SERVER['PHP_SELF'];
    if (!empty($_SERVER['QUERY_STRING'])) {
        $url .= '?' . $_SERVER['QUERY_STRING'];
    }
    header("Location: " . $url);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $msg = "";
    
    // -- Xử lý Danh mục --
    if (isset($_POST['addcategory'])) {
        $id = intval($_POST['id'] ?? 0);
        $name = trim($_POST['name']); $desc = trim($_POST['description']); $icon = trim($_POST['icon']);
        
        if (!$name || !$icon) $msg = "Lỗi: Thiếu tên hoặc icon.";
        else {
            if ($id > 0) $msg = $bai2->updateCategory($id, $name, $desc, $icon) ? "Cập nhật thành công." : "Lỗi cập nhật.";
            else $msg = $bai2->categoryExists($name) ? "Lỗi: Tên trùng." : ($bai2->addCategory($name, $desc, $icon) ? "Thêm thành công." : "Lỗi thêm.");
        }
        redirectWithMsg($msg); // Chuyển hướng ngay
    }

    if (isset($_POST['delete_category'])) {
        $msg = $bai2->deleteCategory($_POST['delete_id']) ? "Đã xóa danh mục." : "Lỗi xóa.";
        redirectWithMsg($msg);
    }

    // -- Xử lý Sản phẩm --
    if (isset($_POST['save_product'])) {
        $id = intval($_POST['prod_id'] ?? 0);
        $name = $_POST['name']; $cid = $_POST['category_id'];
        $price = intval($_POST['price']); 
        $qty = intval($_POST['quantity']);
        $status = $_POST['status'];

        if ($price < 0 || $qty < 0) {
            $msg = "Lỗi: Giá và Số lượng phải lớn hơn hoặc bằng 0!";
            redirectWithMsg($msg);
        } else {
            $avatar = null;
            if (!empty($_FILES['avatar']['name'])) {
                if (!is_dir("uploads/")) mkdir("uploads/", 0777, true);
                $path = "uploads/" . time() . "_main_" . basename($_FILES['avatar']['name']);
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $path)) $avatar = $path;
            }

            if ($id > 0) {
                if ($bai2->updateProduct($id, $name, $cid, $price, $qty, $status, $avatar)) {
                    $msg = "Cập nhật SP thành công!";
                    if (!empty($_FILES['gallery']['name'][0])) {
                        foreach ($_FILES['gallery']['name'] as $k => $n) {
                            $path = "uploads/" . time() . "_gal_" . basename($n);
                            if (move_uploaded_file($_FILES['gallery']['tmp_name'][$k], $path)) $bai2->addProductGallery($id, $path);
                        }
                    }
                } else $msg = "Lỗi cập nhật SP.";
            } else { 
                $avatar = $avatar ?: 'https://via.placeholder.com/150';
                $new_id = $bai2->addProduct($name, $cid, $price, $qty, $status, $avatar);
                if ($new_id) {
                    $msg = "Thêm SP thành công!";
                    if (!empty($_FILES['gallery']['name'][0])) {
                        foreach ($_FILES['gallery']['name'] as $k => $n) {
                            $path = "uploads/" . time() . "_gal_" . basename($n);
                            if (move_uploaded_file($_FILES['gallery']['tmp_name'][$k], $path)) $bai2->addProductGallery($new_id, $path);
                        }
                    }
                } else $msg = "Lỗi thêm SP.";
            }
        }
        redirectWithMsg($msg); // Chuyển hướng ngay
    }

    if (isset($_POST['delete_product'])) {
        $msg = $bai2->deleteProduct($_POST['delete_prod_id']) ? "Đã xóa SP." : "Lỗi xóa SP.";
        redirectWithMsg($msg);
    }
}

// [UPDATE 3] Lấy thông báo từ Session ra hiển thị
$message = "";
if (isset($_SESSION['flash_msg'])) {
    $message = $_SESSION['flash_msg'];
    unset($_SESSION['flash_msg']); // Xóa ngay sau khi lấy
}

// --- DATA HIỂN THỊ ---
$cats = $bai2->getAllCategories();

// 1. Phân trang Category
$limit_cat = 5; 
$page_cat = max(1, intval($_GET['page_cat'] ?? 1));
$total_cat = $bai2->countCategories();
$list_cat = $bai2->getCategories($limit_cat, ($page_cat - 1) * $limit_cat);

// 2. Phân trang Product
$limit_prod = 5;
$page_prod = max(1, intval($_GET['page_prod'] ?? 1));
$total_prod = $bai2->countProducts(); 
$prods = $bai2->getProducts($limit_prod, ($page_prod - 1) * $limit_prod);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .product-img { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; }
        .gallery-thumb { width: 30px; height: 30px; object-fit: cover; border-radius: 3px; border: 1px solid #ccc; margin-right: 2px; }
        .nav-link.active { background: #0d6efd; color: white !important; }
        .btn-icon { width: 32px; height: 32px; padding: 0; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; }
    </style>
</head>
<body>
    <?php if ($message): ?>
    <script>
        // Hiển thị SweetAlert từ thông báo trong Session
        Swal.fire({ 
            title: "Thông báo", 
            text: "<?= addslashes($message) ?>", 
            icon: "<?= strpos($message,'Lỗi')!==false?'error':'success' ?>", 
            confirmButtonColor: '#3085d6' 
        }).then((result) => {
            // Logic Javascript để xóa param khỏi URL cho sạch đẹp (tuỳ chọn)
            if (window.history.replaceState) {
                // window.history.replaceState(null, null, window.location.href);
            }
        });
    </script>
    <?php endif; ?>

    <nav class="navbar navbar-dark bg-primary mb-4 px-3"><a class="navbar-brand fw-bold" href="#">Admin Dashboard</a></nav>

    <div class="container-fluid px-4">
        <ul class="nav nav-pills mb-3">
            <li class="nav-item"><button class="nav-link <?= $active_tab=='product'?'active':'' ?>" data-bs-toggle="pill" data-bs-target="#tab-prod">Sản Phẩm</button></li>
            <li class="nav-item"><button class="nav-link <?= $active_tab=='category'?'active':'' ?>" data-bs-toggle="pill" data-bs-target="#tab-cat">Danh Mục</button></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade <?= $active_tab=='product'?'show active':'' ?>" id="tab-prod">
                <div class="card shadow-sm">
                    <div class="card-header bg-white"><button class="btn btn-success" onclick="openProdModal()"><i class="fas fa-plus"></i> Thêm SP</button></div>
                    <div class="card-body p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light"><tr><th>STT</th><th>Hình ảnh</th><th>Tên SP</th><th>Danh mục</th><th>Giá</th><th>Kho</th><th>Trạng thái</th><th>Hành động</th></tr></thead>
                            <tbody>
                                <?php 
                                $stt_start = ($page_prod - 1) * $limit_prod;
                                $i = 1; 
                                foreach ($prods as $p): ?>
                                <tr>
                                    <td><?= $stt_start + $i++ ?></td>
                                    <td>
                                        <img src="<?= $p['avatar_url'] ?>" class="product-img">
                                        <div class="mt-1 d-flex">
                                            <?php 
                                            $gall = $bai2->getProductImages($p['id']);
                                            foreach($gall as $g): ?>
                                                <img src="<?= $g['image_url'] ?>" class="gallery-thumb" title="Ảnh phụ">
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                    <td class="fw-bold"><?= htmlspecialchars($p['name']) ?></td>
                                    <td><span class="badge bg-info text-dark"><?= $p['category_name'] ?></span></td>
                                    <td class="text-danger fw-bold"><?= number_format($p['price']) ?>₫</td>
                                    <td><?= $p['quantity'] ?></td>
                                    <td><span class="badge <?= $p['status']=='active'?'bg-success':'bg-secondary' ?>"><?= $p['status'] ?></span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary btn-icon" onclick='editProd(<?= json_encode($p) ?>)'><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-danger btn-icon" onclick="delProd(<?= $p['id'] ?>)"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-white">
                        <nav>
                            <ul class="pagination pagination-sm justify-content-end mb-0">
                                <?php for($k=1; $k<=ceil($total_prod/$limit_prod); $k++): ?>
                                    <li class="page-item <?= $k==$page_prod?'active':'' ?>">
                                        <a class="page-link" href="?page_prod=<?= $k ?>"><?= $k ?></a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade <?= $active_tab=='category'?'show active':'' ?>" id="tab-cat">
                <div class="card shadow-sm">
                    <div class="card-header bg-white"><button class="btn btn-primary" onclick="openCatModal()"><i class="fas fa-plus"></i> Thêm DM</button></div>
                    <div class="card-body p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light"><tr><th>ID</th><th>Tên</th><th>Mô tả</th><th>Icon</th><th>Hành động</th></tr></thead>
                            <tbody>
                                <?php foreach ($list_cat as $c): ?>
                                <tr>
                                    <td><?= $c['id'] ?></td>
                                    <td class="fw-bold"><?= htmlspecialchars($c['name']) ?></td>
                                    <td><?= htmlspecialchars($c['description']) ?></td>
                                    <td><i class="<?= htmlspecialchars($c['icon']) ?>"></i></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary btn-icon" onclick='editCat(<?= json_encode($c) ?>)'><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-danger btn-icon" onclick="delCat(<?= $c['id'] ?>)"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-white">
                        <nav><ul class="pagination pagination-sm justify-content-end mb-0">
                            <?php for($k=1; $k<=ceil($total_cat/$limit_cat); $k++): ?>
                                <li class="page-item <?= $k==$page_cat?'active':'' ?>"><a class="page-link" href="?page_cat=<?= $k ?>"><?= $k ?></a></li>
                            <?php endfor; ?>
                        </ul></nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="prodModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="post" enctype="multipart/form-data" id="prod_form">
                    <div class="modal-header"><h5 class="modal-title" id="prodTitle">Sản Phẩm</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <input type="hidden" name="prod_id" id="prod_id" value="0">
                        <div class="row mb-3">
                            <div class="col-6"><label>Tên SP *</label><input type="text" class="form-control" name="name" id="prod_name" required></div>
                            <div class="col-6"><label>Danh mục *</label>
                                <select class="form-select" name="category_id" id="prod_cat" required>
                                    <option value="">-- Chọn --</option>
                                    <?php foreach ($cats as $c): ?><option value="<?= $c['id'] ?>"><?= $c['name'] ?></option><?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4"><label>Giá *</label><input type="number" class="form-control" name="price" id="prod_price" required min="0"></div>
                            <div class="col-4"><label>Kho *</label><input type="number" class="form-control" name="quantity" id="prod_qty" required min="0"></div>
                            <div class="col-4"><label>Trạng thái</label>
                                <select class="form-select" name="status" id="prod_status"><option value="active">Active</option><option value="inactive">Inactive</option></select>
                            </div>
                        </div>
                        <div class="mb-3"><label>Avatar</label><input type="file" class="form-control" name="avatar" accept="image/*"></div>
                        <div class="mb-3 p-3 bg-light border rounded"><label class="fw-bold">Thư viện ảnh (chọn nhiều)</label><input type="file" class="form-control" name="gallery[]" multiple accept="image/*"></div>
                    </div>
                    <div class="modal-footer"><button type="submit" name="save_product" class="btn btn-primary">Lưu</button></div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="delProdModal" tabindex="-1"><div class="modal-dialog modal-sm"><div class="modal-content"><div class="modal-body text-center"><h5 class="mb-3">Xóa SP này?</h5><form method="POST"><input type="hidden" name="delete_prod_id" id="del_prod_id"><button type="submit" name="delete_product" class="btn btn-danger">Xóa Ngay</button></form></div></div></div></div>

    <div class="modal fade" id="catModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="post" id="cat_form"><div class="modal-header"><h5 class="modal-title">Danh Mục</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><input type="hidden" name="id" id="cat_id" value="0"><div class="mb-2"><label>Tên</label><input class="form-control" name="name" id="cat_name" required></div><div class="mb-2"><label>Mô tả</label><textarea class="form-control" name="description" id="cat_desc"></textarea></div><div class="mb-2"><label>Icon</label><input class="form-control" name="icon" id="cat_icon" required></div></div><div class="modal-footer"><button name="addcategory" class="btn btn-primary">Lưu</button></div></form></div></div></div>
    
    <div class="modal fade" id="delCatModal" tabindex="-1"><div class="modal-dialog modal-sm"><div class="modal-content"><div class="modal-body text-center"><h5 class="mb-3">Xóa DM này?</h5><form method="POST"><input type="hidden" name="delete_id" id="del_cat_id"><button type="submit" name="delete_category" class="btn btn-danger">Xóa Ngay</button></form></div></div></div></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // SP Logic
        const pModal = new bootstrap.Modal('#prodModal'), dPModal = new bootstrap.Modal('#delProdModal');
        function openProdModal() { document.getElementById('prod_form').reset(); document.getElementById('prod_id').value = 0; document.getElementById('prodTitle').innerText = "Thêm SP"; pModal.show(); }
        function editProd(p) {
            document.getElementById('prodTitle').innerText = "Cập Nhật SP"; document.getElementById('prod_id').value = p.id;
            document.getElementById('prod_name').value = p.name; document.getElementById('prod_price').value = p.price;
            document.getElementById('prod_qty').value = p.quantity; document.getElementById('prod_status').value = p.status;
            document.getElementById('prod_cat').value = p.category_id; pModal.show();
        }
        function delProd(id) { document.getElementById('del_prod_id').value = id; dPModal.show(); }

        // DM Logic
        const cModal = new bootstrap.Modal('#catModal'), dCModal = new bootstrap.Modal('#delCatModal');
        function openCatModal() { document.getElementById('cat_form').reset(); document.getElementById('cat_id').value = 0; cModal.show(); }
        function editCat(c) {
            document.getElementById('cat_id').value = c.id; document.getElementById('cat_name').value = c.name;
            document.getElementById('cat_desc').value = c.description; document.getElementById('cat_icon').value = c.icon; cModal.show();
        }
        function delCat(id) { document.getElementById('del_cat_id').value = id; dCModal.show(); }
    </script>
</body>
</html>