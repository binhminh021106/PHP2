@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold text-primary">
            <a href="/brand" class="text-decoration-none text-primary"><i class="fa-solid fa-arrow-left me-2"></i></a>
            Thêm Thương Hiệu Mới
        </h4>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <form method="POST" action="/brand/store" enctype="multipart/form-data">

                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Tên Thương Hiệu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control {{ isset($errors['name']) ? 'is-invalid' : '' }}"
                                id="name" name="name"
                                value="{{ $old['name'] ?? '' }}"
                                placeholder="Nhập tên thương hiệu">
                            @if(isset($errors['name']))
                            <div class="invalid-feedback">{{ $errors['name'] }}</div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label fw-bold">Trạng thái</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" {{ (isset($old['status']) && $old['status'] == 'active') ? 'selected' : '' }}>Hiển thị</option>
                                <option value="inactive" {{ (isset($old['status']) && $old['status'] == 'inactive') ? 'selected' : '' }}>Ẩn</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Mô Tả</label>
                            <textarea class="form-control" id="description" name="description" rows="4" placeholder="Nhập mô tả...">{{ $old['description'] ?? '' }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label for="image" class="form-label fw-bold">Hình Ảnh Logo</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">

                            <div class="mt-3 text-center d-none" id="previewContainer">
                                <img id="imagePreview" src="#" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="/brand" class="btn btn-secondary">Hủy bỏ</a>
                            <button type="submit" class="btn btn-primary px-4">Lưu lại</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const imageInput = document.getElementById('image');
    const previewContainer = document.getElementById('previewContainer');
    const imagePreview = document.getElementById('imagePreview');

    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    imagePreview.src = event.target.result;
                    previewContainer.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            } else {
                previewContainer.classList.add('d-none');
            }
        });
    }
</script>
@endsection