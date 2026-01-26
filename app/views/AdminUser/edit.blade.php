@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container-fluid py-2">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 shadow-lg rounded-3">
                
                <div class="card-header bg-warning text-dark py-3 rounded-top-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 fw-bold">
                            <i class="fa-solid fa-user-pen me-2"></i>Cập Nhật Thành Viên
                        </h5>
                        <a href="/user" class="btn btn-outline-dark btn-sm border-0 bg-white bg-opacity-25">
                            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
                        </a>
                    </div>
                </div>

                <div class="card-body p-4 bg-white">
                    <form method="POST" action="/user/update/{{ $user['id'] }}" enctype="multipart/form-data">
                        
                        <div class="row g-4">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Họ và Tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control {{ isset($errors['name']) ? 'is-invalid' : '' }}" 
                                           name="name" value="{{ $user['name'] }}">
                                    @if(isset($errors['name']))
                                        <div class="invalid-feedback">{{ $errors['name'] }}</div>
                                    @endif
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control {{ isset($errors['email']) ? 'is-invalid' : '' }}" 
                                               name="email" value="{{ $user['email'] }}">
                                        @if(isset($errors['email']))
                                            <div class="invalid-feedback">{{ $errors['email'] }}</div>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Số điện thoại</label>
                                        <input type="text" class="form-control" name="phone" value="{{ $user['phone'] }}">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Mật khẩu mới</label>
                                    <input type="password" class="form-control" name="password" placeholder="Để trống nếu không muốn thay đổi">
                                    <small class="text-muted fst-italic">Chỉ nhập khi bạn muốn đổi mật khẩu.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Địa chỉ</label>
                                    <textarea class="form-control" name="address" rows="2">{{ $user['address'] }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card bg-light border-0 h-100">
                                    <div class="card-body text-center">
                                        <label class="form-label fw-bold mb-3">Ảnh đại diện</label>
                                        
                                        <div class="mb-3 d-flex justify-content-center">
                                            <div id="imagePreview" class="rounded-circle bg-white border d-flex align-items-center justify-content-center overflow-hidden" 
                                                 style="width: 150px; height: 150px;">
                                                @if(!empty($user['avatar_url']))
                                                    <img src="/storage/uploads/users/{{ $user['avatar_url'] }}" id="previewImg" style="width: 100%; height: 100%; object-fit: cover;">
                                                @else
                                                    <i class="fa-solid fa-user fa-4x text-muted" id="placeholderIcon"></i>
                                                    <img src="" id="previewImg" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="mb-4">
                                            <label for="avatar" class="btn btn-outline-primary btn-sm w-100">
                                                <i class="fa-solid fa-camera me-1"></i> Thay đổi ảnh
                                            </label>
                                            <input type="file" class="d-none" id="avatar" name="avatar" accept="image/*">
                                        </div>

                                        <hr>

                                        <div class="text-start">
                                            <label class="form-label fw-bold">Trạng thái</label>
                                            <select class="form-select" name="status">
                                                <option value="active" {{ $user['status'] == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                                <option value="inactive" {{ $user['status'] == 'inactive' ? 'selected' : '' }}>Bị khóa</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 opacity-25">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="/user" class="btn btn-light btn-lg px-4 fs-6 border">Hủy bỏ</a>
                            <button type="submit" class="btn btn-warning btn-lg px-4 fs-6 fw-bold shadow-sm">
                                <i class="fa-solid fa-check me-2"></i>Cập Nhật
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
                if(placeholderIcon) placeholderIcon.style.display = 'none';
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection