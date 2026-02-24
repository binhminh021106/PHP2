@extends('layouts.admin')

@section('title', $title ?? 'Cập Nhật Thương Hiệu')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Breadcrumb & Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mt-0 mb-2">Cập Nhật Thương Hiệu</h1>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/brand/index" class="text-decoration-none text-muted">Thương hiệu</a></li>
                <li class="breadcrumb-item active">Cập nhật</li>
            </ol>
        </div>
        <a href="/brand/index" class="btn btn-outline-dark shadow-sm px-4" style="text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">
            <i class="fa-solid fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <i class="fa-solid fa-tag me-1 text-muted"></i> Thông tin thương hiệu
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="/brand/update/{{ $brand['id'] }}" enctype="multipart/form-data">

                        <div class="mb-4">
                            <label for="name" class="form-label fw-bold" style="font-size: 0.95rem;">Tên Thương Hiệu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-0 py-2 {{ isset($errors['name']) ? 'is-invalid' : '' }}"
                                id="name" name="name"
                                value="{{ $brand['name'] }}" required
                                placeholder="Nhập tên thương hiệu...">
                            @if(isset($errors['name']))
                            <div class="invalid-feedback">{{ $errors['name'] }}</div>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label for="status" class="form-label fw-bold" style="font-size: 0.95rem;">Trạng thái</label>
                            <select class="form-select rounded-0 py-2" id="status" name="status">
                                <option value="active" {{ $brand['status'] == 'active' ? 'selected' : '' }}>Hiển thị</option>
                                <option value="inactive" {{ $brand['status'] == 'inactive' ? 'selected' : '' }}>Ẩn</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold" style="font-size: 0.95rem;">Mô Tả</label>
                            <textarea class="form-control rounded-0" id="description" name="description" rows="4" placeholder="Nhập mô tả thương hiệu...">{{ $brand['description'] }}</textarea>
                        </div>

                        <div class="mb-5">
                            <label for="image" class="form-label fw-bold" style="font-size: 0.95rem;">Hình Ảnh Logo</label>
                            <input type="file" class="form-control rounded-0" id="image" name="image" accept="image/*">
                            <div class="form-text mt-2 text-muted">Để trống nếu không muốn thay đổi ảnh.</div>

                            <div class="row mt-4">
                                <!-- Cột: Ảnh hiện tại -->
                                <div class="col-md-6 text-center mb-3 mb-md-0">
                                    <div class="p-3 bg-light border rounded h-100 d-flex flex-column justify-content-center">
                                        <p class="text-muted small mb-3 text-uppercase" style="letter-spacing: 1px;">Ảnh hiện tại</p>
                                        @if(!empty($brand['image']))
                                            <img src="/storage/uploads/brands/{{ $brand['image'] }}" class="bg-white p-2 border shadow-sm mx-auto" style="max-height: 120px; object-fit: contain; max-width: 100%;">
                                        @else
                                            <div class="text-muted fst-italic py-4"><i class="fa-regular fa-image fs-3 mb-2 d-block"></i>Không có ảnh</div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Cột: Ảnh mới chọn (Xem trước) -->
                                <div class="col-md-6 text-center">
                                    <div class="p-3 bg-light border border-dashed rounded h-100 d-flex flex-column justify-content-center" id="previewContainerWrapper">
                                        <p class="text-muted small mb-3 text-uppercase" style="letter-spacing: 1px;">Ảnh mới chọn</p>
                                        
                                        <div id="previewContainer" class="d-none">
                                            <img id="imagePreview" src="#" alt="Preview" class="bg-white p-2 border shadow-sm mx-auto" style="max-height: 120px; object-fit: contain; max-width: 100%;">
                                        </div>
                                        
                                        <div id="noNewImage" class="text-muted fst-italic py-4">Chưa chọn ảnh mới</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-3 border-top pt-4">
                            <a href="/brand/index" class="btn btn-outline-dark px-4 py-2" style="text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">Hủy bỏ</a>
                            <button type="submit" class="btn btn-dark px-4 py-2" style="text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">
                                <i class="fa-solid fa-save me-2"></i>Lưu thay đổi
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    .border-dashed {
        border-style: dashed !important;
        border-width: 2px !important;
        border-color: #dee2e6 !important;
    }
</style>
<script>
    const imageInput = document.getElementById('image');
    const previewContainer = document.getElementById('previewContainer');
    const imagePreview = document.getElementById('imagePreview');
    const noNewImage = document.getElementById('noNewImage');

    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    imagePreview.src = event.target.result;
                    previewContainer.classList.remove('d-none');
                    if(noNewImage) noNewImage.classList.add('d-none');
                };
                reader.readAsDataURL(file);
            } else {
                previewContainer.classList.add('d-none');
                if(noNewImage) noNewImage.classList.remove('d-none');
            }
        });
    }
</script>
@endsection