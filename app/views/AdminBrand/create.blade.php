@extends('layouts.admin')

@section('title', 'Thêm Thương Hiệu')

@section('content')
<!-- Header Page -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0 text-dark">Thêm Thương Hiệu</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/brand">Thương Hiệu</a></li>
                <li class="breadcrumb-item active" aria-current="page">Thêm Mới</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Form Card -->
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form method="POST" action="/brand/store" enctype="multipart/form-data">
                    <!-- Name Field -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold text-dark">
                            Tên Thương Hiệu <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control form-control-lg" id="name" name="name" 
                            placeholder="Nhập tên thương hiệu" required>
                        <small class="text-muted d-block mt-2">Tên thương hiệu phải là duy nhất</small>
                    </div>

                    <!-- Description Field -->
                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold text-dark">
                            Mô Tả
                        </label>
                        <textarea class="form-control form-control-lg" id="description" name="description" 
                            rows="4" placeholder="Nhập mô tả thương hiệu"></textarea>
                        <small class="text-muted d-block mt-2">Mô tả chi tiết về thương hiệu</small>
                    </div>

                    <!-- Image Field -->
                    <div class="mb-3">
                        <label for="image" class="form-label fw-bold text-dark">
                            Hình Ảnh Logo
                        </label>
                        <div class="input-group">
                            <input type="file" class="form-control form-control-lg" id="image" name="image" 
                                accept="image/*">
                        </div>
                        <small class="text-muted d-block mt-2">
                            Định dạng: JPG, PNG, GIF. Kích thước tối đa: 5MB
                        </small>
                        <!-- Image Preview -->
                        <div class="mt-3">
                            <div id="imagePreview" class="border rounded p-3 bg-light" style="min-height: 150px; display: flex; align-items: center; justify-content: center;">
                                <span class="text-muted">Preview hình ảnh</span>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg flex-grow-1">
                            <i class="fa-solid fa-check me-2"></i>Thêm Thương Hiệu
                        </button>
                        <a href="/brand" class="btn btn-secondary btn-lg">
                            <i class="fa-solid fa-xmark me-2"></i>Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Help Info -->
        <div class="alert alert-info mt-3" role="alert">
            <i class="fa-solid fa-info-circle me-2"></i>
            <strong>Hướng dẫn:</strong> Điền đầy đủ thông tin thương hiệu. Các trường có dấu <span class="text-danger">*</span> là bắt buộc.
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Image Preview
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');

    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File quá lớn! Kích thước tối đa là 5MB');
                    this.value = '';
                    imagePreview.innerHTML = '<span class="text-muted">Preview hình ảnh</span>';
                    return;
                }

                // Validate file type
                if (!file.type.startsWith('image/')) {
                    alert('Vui lòng chọn file hình ảnh!');
                    this.value = '';
                    imagePreview.innerHTML = '<span class="text-muted">Preview hình ảnh</span>';
                    return;
                }

                // Show preview
                const reader = new FileReader();
                reader.onload = function(event) {
                    imagePreview.innerHTML = `<img src="${event.target.result}" style="max-width: 100%; max-height: 200px; border-radius: 4px;">`;
                };
                reader.readAsDataURL(file);
            } else {
                imagePreview.innerHTML = '<span class="text-muted">Preview hình ảnh</span>';
            }
        });
    }
</script>
@endsection
