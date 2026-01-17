<!DOCTYPE html>
<html lang="en">

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
</head>

<body>
    <?php require_once VIEW_PATH . '/layout/admin/header.php'; ?>

    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h5 class="m-0 font-weight-bold text-primary"><?php echo $title; ?></h5>
                <a href="/category/index" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left"></i> Quay lại
                </a>
            </div>
            <div class="card-body">
                <form action="/category/update/<?php echo $category['id']; ?>" method="POST">

                    <!-- Tên danh mục -->
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text"
                            class="form-control"
                            id="name"
                            name="name"
                            value="<?php echo isset($category['name']) ? $category['name'] : ''; ?>"
                            required
                            placeholder="Nhập tên danh mục...">
                    </div>

                    <!-- Mô tả -->
                    <div class="form-group mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control"
                            id="description"
                            name="description"
                            rows="4"
                            placeholder="Mô tả ngắn về danh mục..."><?php echo isset($category['description']) ? $category['description'] : ''; ?></textarea>
                    </div>

                    <!-- Icon -->
                    <div class="form-group mb-4">
                        <label for="icon" class="form-label">Icon (CSS Class)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-icons"></i></span>
                            <input type="text"
                                class="form-control"
                                id="icon"
                                name="icon"
                                value="<?php echo isset($category['icon']) ? $category['icon'] : ''; ?>"
                                placeholder="Ví dụ: fa fa-home, fas fa-user...">
                        </div>
                        <small class="text-muted">Nhập class icon từ bộ thư viện FontAwesome hoặc tương tự.</small>
                    </div>

                    <!-- Nút hành động -->
                    <div class="form-group text-end">
                        <button type="reset" class="btn btn-warning me-2">
                            <i class="fa fa-sync"></i> Nhập lại
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Lưu thay đổi
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <?php require_once VIEW_PATH . '/layout/admin/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>