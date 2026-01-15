<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Sản Phẩm - Admin Dashboard</title>

    <!-- Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #0d6efd;
            --bg-light: #f8f9fa;
            --text-dark: #343a40;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-size: 0.95rem;
        }

        .main-card {
            border: none;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            background: white;
            transition: all 0.3s ease;
        }

        .table thead th {
            background-color: #f1f5f9;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            border-bottom: 2px solid #e2e8f0;
            padding: 1rem;
        }

        .table tbody td {
            vertical-align: middle;
            padding: 1rem;
            color: #475569;
        }

        .product-thumb {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .badge-status-active {
            background-color: #dcfce7;
            color: #166534;
            padding: 0.5em 0.8em;
            border-radius: 20px;
            font-weight: 600;
        }

        .badge-status-inactive {
            background-color: #f1f5f9;
            color: #64748b;
            padding: 0.5em 0.8em;
            border-radius: 20px;
            font-weight: 600;
        }

        /* Image Preview Area in Modal */
        .image-preview-container {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .preview-item {
            position: relative;
            width: 80px;
            height: 80px;
        }

        .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #ddd;
        }

        .preview-item .remove-btn {
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

        /* Form Switch Customization */
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            margin: 0 2px;
        }
    </style>
</head>

<body>

    <?php require_once VIEW_PATH . '/layout/admin/header.php'; ?>

    <div class="container pb-5">
        <!-- Header Controls -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">Danh Sách Sản Phẩm</h2>
                <p class="text-muted mb-0">Quản lý kho hàng và thông tin sản phẩm</p>
            </div>
            <button class="btn btn-primary btn-lg shadow-sm" onclick="openModal('add')">
                <i class="fa-solid fa-plus me-2"></i>Thêm Mới
            </button>
        </div>

        <!-- Filter/Search Bar -->
        <div class="main-card p-3 mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fa-solid fa-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0 ps-0" placeholder="Tìm kiếm tên sản phẩm...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select">
                        <option value="">Tất cả danh mục</option>
                        <option value="dt">Điện thoại</option>
                        <option value="lt">Laptop</option>
                        <option value="pk">Phụ kiện</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active">Đang bán</option>
                        <option value="inactive">Ngừng bán</option>
                    </select>
                </div>
                <div class="col-md-2 text-end">
                    <button class="btn btn-outline-secondary w-100"><i class="fa-solid fa-filter me-1"></i> Lọc</button>
                </div>
            </div>
        </div>

        <!-- Product Table -->
        <div class="main-card p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="productTable">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="10%">Hình Ảnh</th>
                            <th width="25%">Tên Sản Phẩm</th>
                            <th width="15%">Giá Bán (VNĐ)</th>
                            <th width="20%">Thuộc Tính</th>
                            <th width="10%">Ngày Tạo</th>
                            <th width="10%">Trạng Thái</th>
                            <th width="5%" class="text-end">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php foreach ($products as $items): ?>
                            <tr>
                                <td class="fw-bold">#<?= htmlspecialchars($items['id']) ?></td>
                                <td><img src="https://via.placeholder.com/150" class="product-thumb" alt="Product"></td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($items['name']) ?></div>
                                    <small class="text-muted">SKU: PROD-1</small>
                                </td>
                                <td class="fw-bold text-primary"><?= htmlspecialchars($items['price']) ?> ₫</td>
                                <td>
                                    <span class="badge bg-secondary me-1 fw-normal">Màu: Titan Tự Nhiên</span>
                                    <span class="badge bg-secondary me-1 fw-normal">Dung lượng: 256GB</span>
                                </td>
                                <td><small class="text-muted">2023-10-15</small></td>
                                <td>
                                    <span class="badge-status-active">Đang bán</span>
                                    <div class="form-check form-switch d-inline-block ms-2" title="Bật/Tắt nhanh">
                                        <input class="form-check-input" type="checkbox" onchange="toggleStatus(1)" checked>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-light btn-icon text-primary" onclick="openModal('edit')" title="Sửa">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light btn-icon text-danger" onclick="confirmDelete(1)" title="Xóa">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                <span class="text-muted small">Hiển thị 1-5 trong tổng số 20 sản phẩm</span>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item disabled"><a class="page-link" href="#">Trước</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item"><a class="page-link" href="#">Sau</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Modal: Thêm/Sửa Sản Phẩm -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="productForm" onsubmit="handleFormSubmit(event)"> <!-- Form Action cho PHP sẽ đặt ở đây -->
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold" id="modalTitle">Thêm Sản Phẩm Mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <input type="hidden" id="productId" name="product_id">

                        <div class="row g-3">
                            <!-- Tên SP -->
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Tên sản phẩm <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="productName" required placeholder="Nhập tên sản phẩm">
                            </div>

                            <!-- Danh mục -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Danh mục</label>
                                <select class="form-select" name="category_id" id="productCategory">
                                    <option value="1">Điện thoại</option>
                                    <option value="2">Laptop</option>
                                    <option value="3">Phụ kiện</option>
                                </select>
                            </div>

                            <!-- Giá & Kho -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Giá bán</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="price" id="productPrice" required min="0">
                                    <span class="input-group-text">VNĐ</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Giá khuyến mãi (Nếu có)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="sale_price" id="productSalePrice" min="0">
                                    <span class="input-group-text">VNĐ</span>
                                </div>
                            </div>

                            <!-- Hình ảnh (Multiple) -->
                            <div class="col-12">
                                <label class="form-label fw-semibold">Hình ảnh sản phẩm</label>
                                <input type="file" class="form-control" name="images[]" id="productImages" multiple accept="image/*" onchange="previewImages(this)">
                                <div class="form-text text-muted">Có thể chọn nhiều ảnh. Ảnh đầu tiên sẽ là ảnh đại diện.</div>
                                <div class="image-preview-container" id="imagePreviewContainer">
                                    <!-- Ảnh preview sẽ hiện ở đây -->
                                </div>
                            </div>

                            <!-- Thuộc tính động -->
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-semibold mb-0">Thuộc tính (Màu, Size, ...)</label>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addAttributeRow()">
                                        <i class="fa-solid fa-plus"></i> Thêm
                                    </button>
                                </div>
                                <div id="attributesContainer" class="bg-light p-3 rounded">
                                    <!-- Các dòng thuộc tính sẽ được thêm vào đây -->
                                </div>
                            </div>

                            <!-- Trạng thái -->
                            <div class="col-12 mt-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status" id="productStatus" checked>
                                    <label class="form-check-label" for="productStatus">Hiển thị sản phẩm này ngay lập tức</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-save me-2"></i>Lưu Lại</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php require_once VIEW_PATH . '/layout/admin/footer.php'; ?>


    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const productModal = new bootstrap.Modal(document.getElementById('productModal'));

        // --- XỬ LÝ UI: MODAL ---
        window.openModal = function(type) {
            // Logic reset form cơ bản để nhập mới
            if (type === 'add') {
                document.getElementById('productForm').reset();
                document.getElementById('modalTitle').innerText = 'Thêm Sản Phẩm Mới';
                document.getElementById('imagePreviewContainer').innerHTML = '';
                document.getElementById('attributesContainer').innerHTML = '';
                addAttributeRow(); // Thêm 1 dòng trống
            } else {
                // Logic giả lập khi bấm sửa (PHP sẽ fill dữ liệu vào đây)
                document.getElementById('modalTitle').innerText = 'Cập Nhật Sản Phẩm';
            }
            productModal.show();
        }

        // --- XỬ LÝ UI: THUỘC TÍNH ĐỘNG (Frontend Only) ---
        window.addAttributeRow = function(key = '', value = '') {
            const container = document.getElementById('attributesContainer');
            const div = document.createElement('div');
            div.className = 'row g-2 mb-2 align-items-center attr-row';
            div.innerHTML = `
                <div class="col-5">
                    <input type="text" class="form-control form-control-sm" name="attr_name[]" placeholder="Tên (VD: Màu)" value="${key}">
                </div>
                <div class="col-5">
                    <input type="text" class="form-control form-control-sm" name="attr_value[]" placeholder="Giá trị (VD: Đỏ)" value="${value}">
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="this.closest('.attr-row').remove()"><i class="fa-solid fa-times"></i></button>
                </div>
            `;
            container.appendChild(div);
        }

        // --- XỬ LÝ UI: PREVIEW ẢNH (Frontend Only) ---
        window.previewImages = function(input) {
            const container = document.getElementById('imagePreviewContainer');
            if (input.files) {
                Array.from(input.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'preview-item';
                        div.innerHTML = `
                            <img src="${e.target.result}">
                            <span class="remove-btn" onclick="this.parentElement.remove()">&times;</span>
                        `;
                        container.appendChild(div);
                    }
                    reader.readAsDataURL(file);
                });
            }
        }

        // --- THÔNG BÁO: SUBMIT FORM ---
        window.handleFormSubmit = function(e) {
            e.preventDefault(); // Ngăn reload để demo sweetalert

            // Giả lập loading
            const btn = e.target.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';

            setTimeout(() => {
                productModal.hide();
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: 'Dữ liệu đã được gửi đi (Demo).',
                    timer: 1500,
                    showConfirmButton: false
                });
                btn.disabled = false;
                btn.innerHTML = originalText;

                // Sau này PHP sẽ reload trang hoặc redirect
            }, 800);
        }

        // --- THÔNG BÁO: XÁO ---
        window.confirmDelete = function(id) {
            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: "Dữ liệu sẽ không thể khôi phục!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Vâng, xóa nó!',
                cancelButtonText: 'Hủy bỏ'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire(
                        'Đã xóa!',
                        'Thông báo xóa thành công (Demo).',
                        'success'
                    )
                    // Chỗ này sẽ gọi window.location.href = 'delete.php?id=' + id
                }
            })
        }

        // --- THÔNG BÁO: TRẠNG THÁI ---
        window.toggleStatus = function(id) {
            const toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            toast.fire({
                icon: 'success',
                title: `Đã cập nhật trạng thái sản phẩm #${id}`
            });
        }
    </script>
</body>

</html>