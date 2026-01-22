<?php

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'sinhvien'; // Database sinhvien

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

class User
{
    private $db;

    public function __construct($conn)
    {
        $this->db = $conn;
    }

    // Check trùng Email (tránh 1 người đăng ký nhiều lần)
    public function checkExist($email)
    {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    // Thêm User mới
    public function create($name, $email, $phone, $address)
    {
        $stmt = $this->db->prepare("INSERT INTO users (name, email, phone, address) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $phone, $address);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Cập nhật User
    public function update($id, $name, $email, $phone, $address)
    {
        $sql = "UPDATE users SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssssi", $name, $email, $phone, $address, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Xoá User
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM users ORDER BY id DESC";
        $result = $this->db->query($sql);
        $data = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }
}

$userModel = new User($conn);

$message = "";
$oldInput = ['name' => '', 'email' => '', 'phone' => '', 'address' => ''];

// --- XỬ LÝ THÊM MỚI ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_create'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    $oldInput = compact('name', 'email', 'phone', 'address');

    if (empty($name) || empty($email) || empty($phone) || empty($address)) {
        $message = "Lỗi: Vui lòng nhập đầy đủ thông tin (Tên, Email, SĐT, Địa chỉ).";
    } else {
        // Kiểm tra email trùng lặp (nếu cần)
        if ($userModel->checkExist($email)) {
            $message = "Lỗi: Email '$email' đã tồn tại trong hệ thống!";
        } else {
            if ($userModel->create($name, $email, $phone, $address)) {
                $message = "Thành công: Thêm User thành công!";
                $oldInput = ['name' => '', 'email' => '', 'phone' => '', 'address' => ''];
            } else {
                $message = "Lỗi: Không thể lưu vào database.";
            }
        }
    }
}

// --- XỬ LÝ CẬP NHẬT ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_update'])) {
    $id = $_POST['id_edit'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if (empty($id) || empty($name) || empty($email)) {
        $message = "Lỗi: Thông tin không hợp lệ.";
    } else {
        if ($userModel->update($id, $name, $email, $phone, $address)) {
            $message = "Thành công: Cập nhật User ID $id thành công!";
        } else {
            $message = "Lỗi: Cập nhật thất bại.";
        }
    }
}

// --- XỬ LÝ XOÁ ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_delete'])) {
    $id = $_POST['id_xoa'] ?? '';

    if ($userModel->delete($id)) {
        $message = "Thành công: Đã xoá User có ID $id.";
    } else {
        $message = "Lỗi: Xoá thất bại.";
    }
}

$users = $userModel->getAll();

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý User (CRUD)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4 text-center text-uppercase fw-bold text-primary">Quản Lý User</h2>

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

        <!-- Form Thêm User -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-success text-white">
                <i class="fas fa-user-plus"></i> Thêm User Mới
            </div>
            <div class="card-body">
                <form method="post" action="">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="name" class="form-label">Họ và Tên (Name)</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="<?= htmlspecialchars($oldInput['name']) ?>"
                                placeholder="Nhập tên...">
                        </div>
                        <div class="col-md-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?= htmlspecialchars($oldInput['email']) ?>"
                                placeholder="name@example.com">
                        </div>
                        <div class="col-md-3">
                            <label for="phone" class="form-label">Số điện thoại (Phone)</label>
                            <input type="text" class="form-control" id="phone" name="phone"
                                value="<?= htmlspecialchars($oldInput['phone']) ?>"
                                placeholder="0909xxxxxx">
                        </div>
                        <div class="col-md-3">
                            <label for="address" class="form-label">Địa chỉ (Address)</label>
                            <input type="text" class="form-control" id="address" name="address"
                                value="<?= htmlspecialchars($oldInput['address']) ?>"
                                placeholder="Nhập địa chỉ...">
                        </div>
                        <div class="col-12 mt-3 text-end">
                            <button type="submit" name="submit_create" class="btn btn-success">
                                <i class="fas fa-save"></i> Lưu User
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bảng Danh Sách -->
        <h4 class="mb-3 text-secondary"><i class="fas fa-list"></i> Danh sách User</h4>
        <div class="table-responsive bg-white shadow-sm rounded">
            <table class="table table-bordered table-striped table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" class="text-center" width="5%">ID</th>
                        <th scope="col" width="20%">Name</th>
                        <th scope="col" width="20%">Email</th>
                        <th scope="col" width="15%">Phone</th>
                        <th scope="col">Address</th>
                        <th scope="col" width="15%" class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td class="text-center fw-bold"><?= htmlspecialchars($u['id']) ?></td>
                            <td><?= htmlspecialchars($u['name']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= htmlspecialchars($u['phone']) ?></td>
                            <td><?= htmlspecialchars($u['address']) ?></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-primary btn-edit"
                                    data-id="<?= htmlspecialchars($u['id']) ?>"
                                    data-name="<?= htmlspecialchars($u['name']) ?>"
                                    data-email="<?= htmlspecialchars($u['email']) ?>"
                                    data-phone="<?= htmlspecialchars($u['phone']) ?>"
                                    data-address="<?= htmlspecialchars($u['address']) ?>">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <button type="button" class="btn btn-sm btn-danger btn-delete"
                                    data-id="<?= htmlspecialchars($u['id']) ?>"
                                    data-name="<?= htmlspecialchars($u['name']) ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-box-open fa-2x mb-2"></i><br>
                                Chưa có dữ liệu nào.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Edit User -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-user-edit"></i> Cập nhật thông tin</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="">
                    <div class="modal-body">
                        <input type="hidden" name="id_edit" id="modal_id">

                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" id="modal_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="modal_email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" id="modal_phone" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control" id="modal_address" name="address" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" name="btn_update" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Xử lý Modal Sửa ---
            const editModal = new bootstrap.Modal(document.getElementById('editModal'));
            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    const email = this.dataset.email;
                    const phone = this.dataset.phone;
                    const address = this.dataset.address;

                    document.getElementById('modal_id').value = id;
                    document.getElementById('modal_name').value = name;
                    document.getElementById('modal_email').value = email;
                    document.getElementById('modal_phone').value = phone;
                    document.getElementById('modal_address').value = address;

                    editModal.show();
                });
            });

            // --- Xử lý SweetAlert Xoá ---
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;

                    Swal.fire({
                        title: 'Xác nhận xoá?',
                        text: `Bạn có chắc muốn xoá: ${name} (ID: ${id})?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Vẫn xoá!',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '';
                            form.innerHTML = `<input type="hidden" name="id_xoa" value="${id}">
                                              <input type="hidden" name="btn_delete" value="1">`;
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
</body>

</html>