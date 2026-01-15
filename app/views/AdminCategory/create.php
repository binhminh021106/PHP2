<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }</style>
</head>
<body>
    <?php require_once VIEW_PATH . '/layout/admin/header.php'; ?>

    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h4 class="mb-0 fw-bold"><i class="fa-solid fa-plus me-2 text-primary"></i>Thêm Danh Mục Mới</h4>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="/category/store">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tên danh mục <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" required placeholder="Ví dụ: Laptop Gaming">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Mô tả</label>
                                <textarea class="form-control" name="description" rows="4" placeholder="Nhập mô tả cho danh mục..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Icon (FontAwesome)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-icons"></i></span>
                                    <input type="text" class="form-control" name="icon" placeholder="Ví dụ: fa-solid fa-laptop">
                                </div>
                                <div class="form-text">Bạn có thể lấy mã icon từ trang FontAwesome 6.</div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="/category/index" class="btn btn-secondary">Hủy bỏ</a>
                                <button type="submit" class="btn btn-primary px-4">Lưu danh mục</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once VIEW_PATH . '/layout/admin/footer.php'; ?>
</body>
</html>