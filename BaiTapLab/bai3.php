<?php
// KẾT NỐI DB
$conn = new mysqli("localhost", "root", "", "sinhvien");
if ($conn->connect_error) {
    die("Lỗi kết nối DB");
}
$conn->set_charset("utf8mb4");

// MODEL NEWS
class News
{
    private $db;

    public function __construct($conn)
    {
        $this->db = $conn;
    }

    // Check slug trùng
    public function checkSlug($slug, $excludeId = null)
    {
        $sql = "SELECT id FROM news WHERE slug = ?";
        if ($excludeId) {
            $sql .= " AND id != ?";
        }
        
        $stmt = $this->db->prepare($sql);
        
        if ($excludeId) {
            $stmt->bind_param("si", $slug, $excludeId);
        } else {
            $stmt->bind_param("s", $slug);
        }

        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM news WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Thêm tin
    public function create($title, $slug, $summary, $content, $status)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO news(title, slug, summary, content, status) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssssi", $title, $slug, $summary, $content, $status);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    // Cập nhật tin
    public function update($id, $title, $slug, $summary, $content, $status)
    {
        $stmt = $this->db->prepare(
            "UPDATE news SET title=?, slug=?, summary=?, content=?, status=? WHERE id=?"
        );
        $stmt->bind_param("ssssii", $title, $slug, $summary, $content, $status, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    // Xoá tin
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM news WHERE id=?");
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    // Lấy danh sách
    public function getAll()
    {
        $result = $this->db->query("SELECT * FROM news ORDER BY id DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

$newsModel = new News($conn);
$message = "";
$currentNews = null; 

// ====== LOGIC LẤY DỮ LIỆU SỬA ======
if (isset($_GET['edit_id'])) {
    $currentNews = $newsModel->getById($_GET['edit_id']);
}

// ====== THÊM MỚI ======
if (isset($_POST['btn_add'])) {
    $title = trim($_POST['title']);
    $slug = trim($_POST['slug']);
    $summary = trim($_POST['summary']);
    $content = trim($_POST['content']);
    $status = $_POST['status'];

    if ($title == "" || $slug == "" || $content == "") {
        $message = "Lỗi: Không được để trống!";
    } elseif ($newsModel->checkSlug($slug)) {
        $message = "Lỗi: Slug đã tồn tại!";
    } else {
        $newsModel->create($title, $slug, $summary, $content, $status);
        $message = "Thêm tin thành công!";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// CẬP NHẬT
if (isset($_POST['btn_update'])) {
    $id = $_POST['id'];
    $title = trim($_POST['title']);
    $slug = trim($_POST['slug']);
    $summary = trim($_POST['summary']);
    $content = trim($_POST['content']);
    $status = $_POST['status'];

    if ($title == "" || $slug == "" || $content == "") {
        $message = "Lỗi: Không được để trống!";
        $currentNews = $_POST; 
    } elseif ($newsModel->checkSlug($slug, $id)) {
        $message = "Lỗi: Slug đã tồn tại!";
        $currentNews = $_POST;
    } else {
        $newsModel->update($id, $title, $slug, $summary, $content, $status);
        $message = "Cập nhật thành công!";
        echo "<script>alert('Cập nhật thành công'); window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
        exit;
    }
}

// XOÁ 
if (isset($_POST['btn_delete'])) {
    $newsModel->delete($_POST['id']);
    $message = "Đã xoá tin!";
    if (isset($_GET['edit_id']) && $_GET['edit_id'] == $_POST['id']) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

$listNews = $newsModel->getAll();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý tin tức</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">

    <h3>Quản lý tin tức</h3>
    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <?= $currentNews ? 'Cập nhật tin tức' : 'Thêm tin mới' ?>
        </div>
        <div class="card-body">
            <form method="post">
                <?php if ($currentNews): ?>
                    <input type="hidden" name="id" value="<?= $currentNews['id'] ?>">
                <?php endif; ?>

                <div class="mb-2">
                    <label>Tiêu đề:</label>
                    <input class="form-control" name="title" value="<?= $currentNews['title'] ?? '' ?>" required>
                </div>
                
                <div class="mb-2">
                    <label>Slug:</label>
                    <input class="form-control" name="slug" value="<?= $currentNews['slug'] ?? '' ?>" required>
                </div>

                <div class="mb-2">
                    <label>Mô tả ngắn:</label>
                    <textarea class="form-control" name="summary" rows="2"><?= $currentNews['summary'] ?? '' ?></textarea>
                </div>

                <div class="mb-2">
                    <label>Nội dung:</label>
                    <textarea class="form-control" name="content" rows="4" required><?= $currentNews['content'] ?? '' ?></textarea>
                </div>

                <div class="mb-2">
                    <label>Trạng thái:</label>
                    <select class="form-control" name="status">
                        <option value="1" <?= (isset($currentNews['status']) && $currentNews['status'] == 1) ? 'selected' : '' ?>>Hiển thị</option>
                        <option value="0" <?= (isset($currentNews['status']) && $currentNews['status'] == 0) ? 'selected' : '' ?>>Ẩn</option>
                    </select>
                </div>

                <?php if ($currentNews): ?>
                    <button class="btn btn-warning" name="btn_update">Cập nhật</button>
                    <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-secondary">Hủy bỏ</a>
                <?php else: ?>
                    <button class="btn btn-primary" name="btn_add">Thêm tin</button>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Tiêu đề</th>
                <th>Slug</th>
                <th>Trạng thái</th>
                <th style="width: 150px;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($listNews as $n): ?>
                <tr>
                    <td><?= $n['id'] ?></td>
                    <td><?= htmlspecialchars($n['title']) ?></td>
                    <td><?= $n['slug'] ?></td>
                    <td>
                        <?php if($n['status']): ?>
                            <span class="badge bg-success">Hiển thị</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Ẩn</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="?edit_id=<?= $n['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                        
                        <form method="post" style="display:inline" onsubmit="return confirm('Bạn có chắc chắn muốn xoá?');">
                            <input type="hidden" name="id" value="<?= $n['id'] ?>">
                            <button class="btn btn-danger btn-sm" name="btn_delete">Xoá</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>