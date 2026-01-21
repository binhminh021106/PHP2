<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>

    <!-- Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }

        .main-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            background: white;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
        }

        .table thead th {
            background-color: #f1f5f9;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            padding: 1rem;
        }

        .table tbody td {
            vertical-align: middle;
            padding: 1rem;
            color: #475569;
        }

        a.text-decoration-none {
            text-decoration: none !important;
        }
    </style>
</head>

<body>

    <?php require_once VIEW_PATH . '/layout/admin/header.php'; ?>

    <div class="container pb-5">
        <!-- Header Controls -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">Danh Mục Sản Phẩm</h2>
                <p class="text-muted mb-0">Quản lý các nhóm sản phẩm</p>
            </div>
            <!-- Thay đổi: Nút chuyển sang trang create -->
            <a href="/category/create" class="btn btn-primary btn-lg shadow-sm text-decoration-none">
                <i class="fa-solid fa-plus me-2"></i>Thêm Mới
            </a>
        </div>

        <!-- Category Table -->
        <div class="main-card p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="5%">STT</th>
                            <th width="20%">Tên Danh Mục</th>
                            <th width="30%">Mô Tả</th>
                            <th width="15%">Biểu Tượng (Icon)</th>
                            <th width="15%" class="text-end">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($category)): ?>
                            <?php $i = 1;
                            foreach ($category as $item): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($item['name']) ?></div>
                                    </td>
                                    <td><?= htmlspecialchars($item['description']) ?></td>
                                    <td>
                                        <?php if (!empty($item['icon'])): ?>
                                            <i class="<?= htmlspecialchars($item['icon']) ?> me-2"></i>
                                            <small class="text-muted"><?= htmlspecialchars($item['icon']) ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <!-- Thay đổi: Nút chuyển sang trang edit kèm ID -->
                                        <a href="/category/edit/<?= $item['id'] ?>"
                                            class="btn btn-sm btn-light btn-icon text-primary border me-1"
                                            title="Sửa">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>

                                        <!-- Nút Xóa vẫn giữ Modal vì trải nghiệm UX tốt hơn là chuyển trang -->
                                        <button class="btn btn-sm btn-light btn-icon text-danger border"
                                            onclick="confirmDelete(<?= $item['id'] ?>)"
                                            title="Xóa">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Chưa có danh mục nào.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL: XÓA (Giữ lại để xác nhận xóa) -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow">
                <form method="POST" action="/category/delete">
                    <div class="modal-body text-center pt-4 pb-4">
                        <i class="fas fa-exclamation-circle text-danger display-1 mb-3"></i>
                        <h4 class="fw-bold">Xóa danh mục?</h4>
                        <p class="text-muted mb-4">Hành động này không thể hoàn tác.</p>

                        <input type="hidden" name="delete_id" id="deleteIdInput">

                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-secondary w-50" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-danger w-50">Xóa</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php require_once VIEW_PATH . '/layout/admin/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Chỉ còn script cho Modal Xóa
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

        function confirmDelete(id) {
            document.getElementById('deleteIdInput').value = id;
            deleteModal.show();
        }
    </script>
</body>

</html>