@extends('layouts.admin')

@section('title', $title ?? 'Cập Nhật Thành Viên')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Breadcrumb & Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mt-0 mb-2">Cập Nhật Thành Viên</h1>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/user/index" class="text-decoration-none text-muted">Thành viên</a></li>
                <li class="breadcrumb-item active">Chỉnh sửa</li>
            </ol>
        </div>
        <a href="/user/index" class="btn btn-outline-dark shadow-sm px-4" style="text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">
            <i class="fa-solid fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>

    <!-- Hiển thị lỗi hệ thống nếu có -->
    @if(isset($errors['system']))
        <div class="alert alert-danger rounded-0 border-0 shadow-sm mb-4">
            <i class="fa-solid fa-circle-exclamation me-2"></i> {{ $errors['system'] }}
        </div>
    @endif

    <form action="/user/update/{{ $user['id'] }}" method="POST" enctype="multipart/form-data">
        <div class="row g-4">
            <!-- CỘT TRÁI: THÔNG TIN TÀI KHOẢN -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 fw-bold">
                        <i class="fa-solid fa-id-card me-2 text-muted"></i>Thông tin cơ bản tài khoản #{{ $user['id'] }}
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold small text-uppercase">Họ và Tên <span class="text-danger">*</span></label>
                                <input type="text" name="name" 
                                       class="form-control rounded-0 py-2 @isset($errors['name']) is-invalid @endisset" 
                                       value="{{ $user['name'] }}" 
                                       placeholder="Nhập tên người dùng...">
                                @isset($errors['name']) <div class="invalid-feedback">{{ $errors['name'] }}</div> @endisset
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold small text-uppercase">Địa chỉ Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" 
                                       class="form-control rounded-0 py-2 @isset($errors['email']) is-invalid @endisset" 
                                       value="{{ $user['email'] }}" 
                                       placeholder="example@gmail.com">
                                @isset($errors['email']) <div class="invalid-feedback">{{ $errors['email'] }}</div> @endisset
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold small text-uppercase">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" name="phone" 
                                       class="form-control rounded-0 py-2 @isset($errors['phone']) is-invalid @endisset" 
                                       value="{{ $user['phone'] }}" 
                                       placeholder="09xx xxx xxx">
                                @isset($errors['phone']) <div class="invalid-feedback">{{ $errors['phone'] }}</div> @endisset
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold small text-uppercase">Mật khẩu mới</label>
                                <input type="password" name="password" 
                                       class="form-control rounded-0 py-2 @isset($errors['password']) is-invalid @endisset" 
                                       placeholder="Để trống nếu không muốn thay đổi...">
                                @isset($errors['password']) <div class="invalid-feedback">{{ $errors['password'] }}</div> @endisset
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold small text-uppercase">Địa chỉ liên hệ</label>
                            <textarea name="address" class="form-control rounded-0 @isset($errors['address']) is-invalid @endisset" 
                                      rows="3" placeholder="Số nhà, tên đường, phường/xã...">{{ $user['address'] }}</textarea>
                            @isset($errors['address']) <div class="invalid-feedback">{{ $errors['address'] }}</div> @endisset
                        </div>
                    </div>
                </div>
            </div>

            <!-- CỘT PHẢI: THIẾT LẬP VÀ AVATAR -->
            <div class="col-lg-4">
                <!-- Vai trò & Trạng thái -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 fw-bold">
                        <i class="fa-solid fa-user-gear me-2 text-muted"></i>Thiết lập tài khoản
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase">Vai trò hệ thống</label>
                            <select name="role" class="form-select rounded-0 py-2">
                                <option value="0" {{ $user['role'] == 0 ? 'selected' : '' }}>Khách hàng (Member)</option>
                                <option value="1" {{ $user['role'] == 1 ? 'selected' : '' }}>Quản trị viên (Admin)</option>
                            </select>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold small text-uppercase">Trạng thái hoạt động</label>
                            <select name="status" class="form-select rounded-0 py-2">
                                <option value="active" {{ $user['status'] == 'active' ? 'selected' : '' }}>Kích hoạt (Active)</option>
                                <option value="blocked" {{ $user['status'] == 'blocked' ? 'selected' : '' }}>Khóa tài khoản (Blocked)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Ảnh đại diện -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 fw-bold">
                        <i class="fa-solid fa-camera me-2 text-muted"></i>Ảnh đại diện
                    </div>
                    <div class="card-body p-4 text-center">
                        <div class="border border-dashed p-4 bg-light">
                            <!-- Ảnh hiện tại -->
                            <div id="currentAvatarWrapper" class="mb-3">
                                <p class="text-muted small mb-2 text-uppercase" style="letter-spacing: 0.5px;">Ảnh hiện tại</p>
                                @if($user['avatar_url'])
                                    <img src="/storage/uploads/users/{{ $user['avatar_url'] }}" class="rounded-circle border shadow-sm p-1" style="width: 100px; height: 100px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 100px; height: 100px; font-size: 2rem; font-family: var(--font-heading);">
                                        {{ strtoupper(substr($user['name'], 0, 1)) }}
                                    </div>
                                @endif
                            </div>

                            <!-- Khu vực xem trước ảnh mới -->
                            <div id="avatarPreviewContainer" class="mb-3 d-none">
                                <p class="text-success small mb-2 fw-bold text-uppercase" style="letter-spacing: 0.5px;">Ảnh mới chọn</p>
                                <img id="avatarPreview" src="#" alt="Preview" class="rounded-circle border border-success shadow-sm p-1" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>

                            <label class="form-label small fw-bold text-dark text-uppercase mt-2">Thay đổi ảnh đại diện:</label>
                            <input type="file" name="avatar" id="avatarInput" class="form-control rounded-0 mt-1" accept="image/*">
                        </div>
                    </div>
                </div>

                <!-- Nút cập nhật đơn hàng -->
                <div class="sticky-top" style="top: 90px; z-index: 10;">
                    <div class="card shadow border-0 bg-dark text-white">
                        <div class="card-body p-3 text-center">
                            <button type="submit" class="btn btn-warning w-100 py-3 fw-bold rounded-0" style="letter-spacing: 1.5px; text-transform: uppercase; color: #111;">
                                <i class="fa-solid fa-user-check me-2"></i>Cập Nhật Thành Viên
                            </button>
                            <a href="/user/index" class="btn btn-outline-light w-100 mt-2 py-2 border-0 small text-uppercase" style="font-size: 0.75rem;">Hủy chỉnh sửa</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .border-dashed { border: 2px dashed #ddd !important; }
    .form-control:focus, .form-select:focus { border-color: #111; box-shadow: none; }
</style>
@endsection

@section('scripts')
<script>
    // Xử lý xem trước ảnh đại diện ngay khi chọn file mới
    const avatarInput = document.getElementById('avatarInput');
    const avatarPreviewContainer = document.getElementById('avatarPreviewContainer');
    const avatarPreview = document.getElementById('avatarPreview');
    const currentAvatarWrapper = document.getElementById('currentAvatarWrapper');

    if (avatarInput) {
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    avatarPreview.src = event.target.result;
                    avatarPreviewContainer.classList.remove('d-none');
                    if (currentAvatarWrapper) currentAvatarWrapper.style.opacity = '0.5';
                };
                reader.readAsDataURL(file);
            } else {
                avatarPreviewContainer.classList.add('d-none');
                if (currentAvatarWrapper) currentAvatarWrapper.style.opacity = '1';
            }
        });
    }
</script>
@endsection