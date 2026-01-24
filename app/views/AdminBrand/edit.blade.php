@extends('layouts.admin')

@section('title', $title)

@section('content')
<!-- Header Page -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0 text-dark">Cập Nhật Thương Hiệu</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/brand">Thương Hiệu</a></li>
                <li class="breadcrumb-item active" aria-current="page">Cập nhật</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Form Card -->
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                {{-- Lưu ý: action phải trỏ về route update --}}
                <form method="POST" action="/brand/update/{{ $brand['id'] }}" enctype="multipart/form-data">

                    {{-- Tên thương hiệu --}}
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold text-dark">
                            Tên Thương Hiệu <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control form-control-lg" id="name" name="name"
                            value="{{ $brand['name'] }}" required>
                    </div>

                    {{-- Mô tả --}}
                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold text-dark">
                            Mô Tả
                        </label>
                        <textarea class="form-control form-control-lg" id="description" name="description"
                            rows="4">{{ $brand['description'] }}</textarea>
                    </div>

                    {{-- Hình ảnh --}}
                    <div class="mb-3">
                        <label for="image" class="form-label fw-bold text-dark">
                            Hình Ảnh Logo
                        </label>
                        <div class="input-group">
                            <input type="file" class="form-control form-control-lg" id="image" name="image"
                                accept="image/*">
                        </div>
                        <small class="text-muted d-block mt-2">
                            Để trống nếu không muốn thay đổi ảnh.
                        </small>

                        <!-- Preview ảnh cũ và ảnh mới -->
                        <div class="mt-3 d-flex gap-3">
                            <div class="text-center">
                                <span class="d-block small text-muted mb-1">Ảnh hiện tại</span>
                                @if(!empty($brand['image']))
                                <img src="/storage/uploads/brands/{{ $brand['image'] }}" style="height: 100px; border-radius: 4px; border: 1px solid #ddd;">
                                @else
                                <div class="p-3 bg-light border rounded text-muted">Không có ảnh</div>
                                @endif
                            </div>

                            <div id="imagePreview" class="text-center" style="display: none;">
                                <span class="d-block small text-muted mb-1">Ảnh mới chọn</span>
                                <!-- JS sẽ render ảnh vào đây -->
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg flex-grow-1">
                            <i class="fa-solid fa-save me-2"></i>Lưu Thay Đổi
                        </button>
                        <a href="/brand" class="btn btn-secondary btn-lg">
                            <i class="fa-solid fa-xmark me-2"></i>Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Script preview ảnh khi chọn file mới
    const imageInput = document.getElementById('image');
    const imagePreviewContainer = document.getElementById('imagePreview');

    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    imagePreviewContainer.style.display = 'block';
                    // Tạo thẻ img nếu chưa có hoặc update src
                    let img = imagePreviewContainer.querySelector('img');
                    if (!img) {
                        img = document.createElement('img');
                        img.style.height = '100px';
                        img.style.borderRadius = '4px';
                        img.style.border = '1px solid #ddd';
                        imagePreviewContainer.appendChild(img);
                    }
                    img.src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }
</script>
@endsection