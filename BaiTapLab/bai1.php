<?php
// =========================================================================
// 1. CẤU HÌNH & KẾT NỐI DATABASE
// =========================================================================
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'sinhvien';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

class Student {
    private $db;

    public function __construct($conn) {
        $this->db = $conn;
    }

    // Tính xếp loại
    public function calculateRank($score) {
        if ($score < 5) return 'Yếu';
        if ($score < 6.5) return 'Trung bình';
        if ($score < 8) return 'Khá';
        return 'Giỏi';
    }

    // Check trùng MSSV
    public function checkExist($mssv) {
        $stmt = $this->db->prepare("SELECT mssv FROM sinhvien WHERE mssv = ?");
        $stmt->bind_param("s", $mssv);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    // Thêm sinh viên
    public function create($mssv, $hoten, $diem) {
        $stmt = $this->db->prepare("INSERT INTO sinhvien (mssv, hoten, diem) VALUES (?, ?, ?)");
        $stmt->bind_param("ssd", $mssv, $hoten, $diem);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Cập nhật sinh viên
    public function update($mssv, $hoten, $diem) {
        $stmt = $this->db->prepare("UPDATE sinhvien SET hoten = ?, diem = ? WHERE mssv = ?");
        $stmt->bind_param("sds", $hoten, $diem, $mssv);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Xoá sinh viên
    public function delete($mssv) {
        $stmt = $this->db->prepare("DELETE FROM sinhvien WHERE mssv = ?");
        $stmt->bind_param("s", $mssv);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function getAll() {
        $sql = "SELECT * FROM sinhvien";
        $result = $this->db->query($sql);
        $data = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Tự động tính xếp loại cho từng dòng dữ liệu
                $row['xeploai'] = $this->calculateRank($row['diem']);
                $data[] = $row;
            }
        }
        return $data;
    }
}

$studentModel = new Student($conn);

$message = "";
$oldInput = ['mssv' => '', 'hoten' => '', 'diem' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $mssv = trim($_POST['mssv'] ?? '');
    $hoten = trim($_POST['hoten'] ?? '');
    $diem = trim($_POST['diem'] ?? '');

    $oldInput = compact('mssv', 'hoten', 'diem');

    if (empty($mssv) || empty($hoten) || $diem === '') {
        $message = "Lỗi: Vui lòng nhập đầy đủ thông tin.";
    } else if (!is_numeric($diem) || $diem < 0 || $diem > 10) {
        $message = "Lỗi: Điểm phải là số thực từ 0 đến 10.";
    } else {
        if ($studentModel->checkExist($mssv)) {
            $message = "Lỗi: MSSV '$mssv' đã tồn tại!";
        } else {
            if ($studentModel->create($mssv, $hoten, $diem)) {
                $message = "Thành công: Thêm sinh viên thành công!";
                $oldInput = ['mssv' => '', 'hoten' => '', 'diem' => ''];
            } else {
                $message = "Lỗi: Không thể lưu vào database.";
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_update'])) {
    $mssv_goc = $_POST['mssv_goc'] ?? '';
    $hoten = trim($_POST['hoten'] ?? '');
    $diem = trim($_POST['diem'] ?? '');

    if (empty($mssv_goc) || empty($hoten) || $diem === '') {
        $message = "Lỗi: Thông tin không hợp lệ.";
    } else if (!is_numeric($diem) || $diem < 0 || $diem > 10) {
        $message = "Lỗi: Điểm phải từ 0 - 10.";
    } else {
        if ($studentModel->update($mssv_goc, $hoten, $diem)) {
            $message = "Thành công: Cập nhật sinh viên $mssv_goc thành công!";
        } else {
            $message = "Lỗi: Cập nhật thất bại.";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_delete'])) {
    $mssv = $_POST['mssv_xoa'] ?? '';
    
    if ($studentModel->delete($mssv)) {
        $message = "Thành công: Đã xoá sinh viên $mssv.";
    } else {
        $message = "Lỗi: Xoá thất bại.";
    }
}

$students = $studentModel->getAll();

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Sinh Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Hệ Thống Quản Lý Sinh Viên (Class Version)</h2>

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

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Nhập thông tin sinh viên
            </div>
            <div class="card-body">
                <form method="post" action="">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="mssv" class="form-label">Mã số sinh viên (MSSV)</label>
                            <input type="text" class="form-control" id="mssv" name="mssv"
                                value="<?= htmlspecialchars($oldInput['mssv']) ?>"
                                placeholder="Ví dụ: SV001">
                        </div>
                        <div class="col-md-5">
                            <label for="hoten" class="form-label">Họ và Tên</label>
                            <input type="text" class="form-control" id="hoten" name="hoten"
                                value="<?= htmlspecialchars($oldInput['hoten']) ?>"
                                placeholder="Ví dụ: Nguyễn Văn A">
                        </div>
                        <div class="col-md-3">
                            <label for="diem" class="form-label">Điểm</label>
                            <input type="number" step="0.1" class="form-control" id="diem" name="diem"
                                value="<?= htmlspecialchars($oldInput['diem']) ?>"
                                placeholder="0.0 - 10.0">
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" name="submit" class="btn btn-primary">
                                Lưu Sinh Viên
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <h4 class="mb-3">Danh sách sinh viên trong Database</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" class="text-center" width="5%">STT</th>
                        <th scope="col" width="15%">MSSV</th>
                        <th scope="col">Họ Tên</th>
                        <th scope="col" width="10%">Điểm</th>
                        <th scope="col">Xếp loại</th>
                        <th scope="col" width="15%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $stt = 1; ?>
                    <?php foreach ($students as $sv): ?>
                        <tr>
                            <td class="text-center"><?= $stt++ ?></td>
                            <td><?= htmlspecialchars($sv['mssv']) ?></td>
                            <td><?= htmlspecialchars($sv['hoten']) ?></td>
                            <td><?= htmlspecialchars($sv['diem']) ?></td>
                            <td>
                                <?php 
                                    $badgeColor = 'secondary';
                                    if($sv['xeploai'] == 'Giỏi') $badgeColor = 'success';
                                    elseif($sv['xeploai'] == 'Khá') $badgeColor = 'info';
                                    elseif($sv['xeploai'] == 'Trung bình') $badgeColor = 'warning';
                                    else $badgeColor = 'danger';
                                ?>
                                <span class="badge bg-<?= $badgeColor ?>"><?= htmlspecialchars($sv['xeploai']) ?></span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning btn-edit"
                                    data-mssv="<?= htmlspecialchars($sv['mssv']) ?>"
                                    data-hoten="<?= htmlspecialchars($sv['hoten']) ?>"
                                    data-diem="<?= htmlspecialchars($sv['diem']) ?>">
                                    Sửa
                                </button>

                                <button type="button" class="btn btn-sm btn-danger btn-delete"
                                    data-mssv="<?= htmlspecialchars($sv['mssv']) ?>"
                                    data-hoten="<?= htmlspecialchars($sv['hoten']) ?>">
                                    Xoá
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">
                                Không có dữ liệu sinh viên nào.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">Cập nhật thông tin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="">
                    <div class="modal-body">
                        <input type="hidden" name="mssv_goc" id="modal_mssv_goc">
                        <div class="mb-3">
                            <label class="form-label">MSSV</label>
                            <input type="text" class="form-control" id="modal_mssv" name="mssv" disabled readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Họ và Tên</label>
                            <input type="text" class="form-control" id="modal_hoten" name="hoten" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Điểm số</label>
                            <input type="number" step="0.1" class="form-control" id="modal_diem" name="diem" required>
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
            // Modal Sửa
            const editModal = new bootstrap.Modal(document.getElementById('editModal'));
            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('modal_mssv').value = this.dataset.mssv;
                    document.getElementById('modal_mssv_goc').value = this.dataset.mssv;
                    document.getElementById('modal_hoten').value = this.dataset.hoten;
                    document.getElementById('modal_diem').value = this.dataset.diem;
                    editModal.show();
                });
            });

            // SweetAlert Xoá
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', function() {
                    const mssv = this.dataset.mssv;
                    const hoten = this.dataset.hoten;
                    Swal.fire({
                        title: 'Bạn chắc chắn chứ?',
                        text: `Xoá sinh viên ${hoten} (${mssv}) sẽ không thể hoàn tác!`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Vẫn xoá!',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '';
                            form.innerHTML = `<input type="hidden" name="mssv_xoa" value="${mssv}">
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