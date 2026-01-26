@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container-fluid py-2">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 shadow-lg rounded-3">

                {{-- Header --}}
                <div class="card-header bg-primary text-white py-3 rounded-top-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 fw-bold">
                            <i class="fa-solid fa-user-plus me-2"></i>Thêm Thành Viên Mới
                        </h5>
                        <a href="/user" class="btn btn-outline-light btn-sm border-0 bg-white bg-opacity-25">
                            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
                        </a>
                    </div>
                </div>

                {{-- Body --}}
                <div class="card-body p-4 bg-white">
                    <form method="POST" action="/user/store" enctype="multipart/form-data">

                        <div class="row g-4">
                            {{-- Cột trái: Thông tin cơ bản --}}
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Họ và Tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control {{ isset($errors['name']) ? 'is-invalid' : '' }}"
                                        name="name" value="{{ $old['name'] ?? '' }}" placeholder="Nguyễn Văn A">
                                    @if(isset($errors['name']))
                                    <div class="invalid-feedback">{{ $errors['name'] }}</div>
                                    @endif
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control {{ isset($errors['email']) ? 'is-invalid' : '' }}"
                                            name="email" value="{{ $old['email'] ?? '' }}" placeholder="email@example.com">
                                        @if(isset($errors['email']))
                                        <div class="invalid-feedback">{{ $errors['email'] }}</div>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control {{ isset($errors['phone']) ? 'is-invalid' : '' }}"
                                            name="phone" value="{{ $old['phone'] ?? '' }}" placeholder="0909xxxxxx">
                                        @if(isset($errors['phone']))
                                        <div class="invalid-feedback">{{ $errors['phone'] }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Mật khẩu <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control {{ isset($errors['password']) ? 'is-invalid' : '' }}"
                                        name="password" placeholder="Nhập mật khẩu...">
                                    @if(isset($errors['password']))
                                    <div class="invalid-feedback">{{ $errors['password'] }}</div>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    {{-- ĐÃ SỬA: Thêm dấu * và class is-invalid cho textarea --}}
                                    <label class="form-label fw-bold">Địa chỉ <span class="text-danger">*</span></label>
                                    <textarea class="form-control {{ isset($errors['address']) ? 'is-invalid' : '' }}"
                                        name="address" rows="2" placeholder="Địa chỉ liên hệ...">{{ $old['address'] ?? '' }}</textarea>
                                    @if(isset($errors['address']))
                                    <div class="invalid-feedback">{{ $errors['address'] }}</div>
                                    @endif
                                </div>
                            </div>

                            {{-- Cột phải: Avatar & Trạng thái --}}
                            <div class="col-md-4">
                                <div class="card bg-light border-0 h-100">
                                    <div class="card-body text-center">
                                        <label class="form-label fw-bold mb-3">Ảnh đại diện</label>

                                        <div class="mb-3 d-flex justify-content-center">
                                            <div id="imagePreview" class="rounded-circle bg-white border d-flex align-items-center justify-content-center overflow-hidden"
                                                style="width: 150px; height: 150px;">
                                                <i class="fa-solid fa-user fa-4x text-muted" id="placeholderIcon"></i>
                                                <img src="" id="previewImg" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label for="avatar" class="btn btn-outline-primary btn-sm w-100">
                                                <i class="fa-solid fa-upload me-1"></i> Chọn ảnh
                                            </label>
                                            <input type="file" class="d-none" id="avatar" name="avatar" accept="image/*">
                                        </div>

                                        <hr>

                                        <div class="text-start">
                                            <label class="form-label fw-bold">Trạng thái</label>
                                            <select class="form-select" name="status">
                                                <option value="active" {{ (isset($old['status']) && $old['status'] == 'active') ? 'selected' : '' }}>Hoạt động</option>
                                                <option value="inactive" {{ (isset($old['status']) && $old['status'] == 'inactive') ? 'selected' : '' }}>Bị khóa</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 opacity-25">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="/user" class="btn btn-light btn-lg px-4 fs-6 border">Hủy bỏ</a>
                            <button type="submit" class="btn btn-primary btn-lg px-4 fs-6 fw-bold shadow-sm">
                                <i class="fa-solid fa-save me-2"></i>Lưu Thành Viên
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
<script>
    // Preview Avatar
    const avatarInput = document.getElementById('avatar');
    const previewImg = document.getElementById('previewImg');
    const placeholderIcon = document.getElementById('placeholderIcon');

    avatarInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                previewImg.src = event.target.result;
                previewImg.style.display = 'block';
                placeholderIcon.style.display = 'none';
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection