@extends('layouts.client')

@section('title', $title ?? 'Tài khoản của tôi')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap');

    :root {
        --font-base: 'Jost', sans-serif;
        --font-heading: 'Playfair Display', serif;
        --color-dark: #111;
        --color-accent: #c9a47c;
        --transition: all 0.4s ease;
    }

    body { font-family: var(--font-base); background-color: #fcfcfc; color: var(--color-dark); }
    
    .page-title { font-family: var(--font-heading); font-size: 2.5rem; font-weight: 600; margin-bottom: 30px; }

    .btn { border-radius: 0; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; font-weight: 500; transition: var(--transition); }
    .btn-dark { background: var(--color-dark); border-color: var(--color-dark); color: white; padding: 10px 25px; }
    .btn-dark:hover { background: #333; color: white; transform: translateY(-2px); }
    .btn-outline-dark { border: 1px solid var(--color-dark); color: var(--color-dark); background: transparent; padding: 5px 15px; font-size: 0.75rem; }
    .btn-outline-dark:hover { background: var(--color-dark); color: white; }

    /* Profile Layout */
    .profile-sidebar { background: #fff; border: 1px solid #eee; padding: 30px 20px; text-align: center; }
    
    /* Avatar Styling */
    .profile-avatar-wrapper { position: relative; width: 100px; height: 100px; margin: 0 auto 15px; }
    .profile-avatar { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; border: 3px solid #f8f9fa; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
    .profile-avatar-placeholder { width: 100%; height: 100%; background: var(--color-dark); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-family: var(--font-heading); box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
    .avatar-edit-btn { position: absolute; bottom: 0; right: 0; background: var(--color-accent); color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 2px solid #fff; transition: var(--transition); font-size: 0.85rem; }
    .avatar-edit-btn:hover { background: var(--color-dark); transform: scale(1.1); }
    #avatarInput { display: none; }
    
    .nav-pills .nav-link { color: #555; text-align: left; padding: 12px 20px; border-radius: 0; margin-bottom: 5px; font-weight: 500; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; transition: var(--transition); }
    .nav-pills .nav-link.active, .nav-pills .nav-link:hover { background: #f8f9fa; color: var(--color-dark); border-left: 3px solid var(--color-dark); }
    .nav-pills .nav-link i { width: 25px; }

    .profile-content { background: #fff; border: 1px solid #eee; padding: 30px; min-height: 400px; }
    .content-title { font-family: var(--font-heading); font-size: 1.5rem; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 25px; }

    /* Forms */
    .form-control { border-radius: 0; padding: 12px 15px; border: 1px solid #ddd; }
    .form-control:focus { border-color: var(--color-dark); box-shadow: none; }
    .form-label { font-weight: 500; font-size: 0.9rem; color: #555; }

    /* Address Card */
    .address-card { border: 1px solid #eee; padding: 20px; margin-bottom: 20px; position: relative; transition: var(--transition); }
    .address-card:hover { border-color: #ddd; box-shadow: 0 5px 15px rgba(0,0,0,0.03); }
    .address-card.is-default { border-color: var(--color-dark); }
    .badge-default { position: absolute; top: 20px; right: 20px; background: var(--color-dark); color: white; font-size: 0.7rem; padding: 5px 10px; text-transform: uppercase; letter-spacing: 1px; }
    .address-name { font-weight: 600; font-size: 1.1rem; margin-bottom: 5px; }
    .address-phone { color: #555; font-size: 0.95rem; margin-bottom: 10px; }
    .address-text { color: #777; font-size: 0.9rem; line-height: 1.6; margin-bottom: 15px; }
</style>

<div class="bg-light py-3 border-bottom mb-5">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-dark">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tài khoản</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5 pb-5">
    <div class="row g-4">
        <!-- Sidebar Menu -->
        <div class="col-lg-3">
            <div class="profile-sidebar">
                
                <!-- Khu vực Cập nhật Avatar -->
                <form action="/profile/updateAvatar" method="POST" enctype="multipart/form-data" id="avatarForm">
                    <div class="profile-avatar-wrapper">
                        @php
                            $avatarImg = '';
                            // Dùng cột avatar_url trả về từ CSDL
                            $dbImage = $userInfo['avatar_url'] ?? '';
                            
                            if (!empty($dbImage)) {
                                // Nếu là link (Google/Facebook) thì giữ nguyên, nếu không thì cộng thêm đường dẫn local
                                $avatarImg = filter_var($dbImage, FILTER_VALIDATE_URL) ? $dbImage : '/storage/uploads/users/' . $dbImage;
                            }
                        @endphp

                        @if(!empty($avatarImg))
                            <img src="{{ $avatarImg }}" alt="Avatar" class="profile-avatar">
                        @else
                            <div class="profile-avatar-placeholder">
                                {{ strtoupper(substr($userInfo['name'] ?? 'U', 0, 1)) }}
                            </div>
                        @endif

                        <label for="avatarInput" class="avatar-edit-btn" title="Cập nhật ảnh đại diện">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" name="avatar" id="avatarInput" accept="image/png, image/jpeg, image/jpg">
                    </div>
                </form>

                <h5 class="mb-1">{{ $userInfo['name'] }}</h5>
                <p class="text-muted small mb-4">{{ $userInfo['email'] }}</p>

                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <button class="nav-link active" id="v-pills-info-tab" data-bs-toggle="pill" data-bs-target="#v-pills-info" type="button" role="tab">
                        <i class="far fa-user"></i> Thông tin cá nhân
                    </button>
                    <button class="nav-link" id="v-pills-address-tab" data-bs-toggle="pill" data-bs-target="#v-pills-address" type="button" role="tab">
                        <i class="far fa-address-book"></i> Sổ địa chỉ
                    </button>
                    <a class="nav-link" href="/order/history">
                        <i class="fas fa-history"></i> Lịch sử đơn hàng
                    </a>
                    <a class="nav-link text-danger mt-3 border-0" href="#" onclick="confirmLogout(event)">
                        <i class="fas fa-sign-out-alt"></i> Đăng xuất
                    </a>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="col-lg-9">
            <div class="profile-content tab-content" id="v-pills-tabContent">
                
                <!-- TAB THÔNG TIN CÁ NHÂN -->
                <div class="tab-pane fade show active" id="v-pills-info" role="tabpanel">
                    <h3 class="content-title">Thông Tin Của Bạn</h3>
                    
                    <form action="/profile/updateInfo" method="POST">
                        <div class="row g-4 max-w-700">
                            <div class="col-md-6">
                                <label class="form-label">Họ và Tên</label>
                                <input type="text" class="form-control" name="name" value="{{ $userInfo['name'] }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Số điện thoại</label>
                                <input type="tel" class="form-control" name="phone" value="{{ $userInfo['phone'] ?? '' }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Email (Không thể thay đổi)</label>
                                <input type="email" class="form-control bg-light" value="{{ $userInfo['email'] }}" readonly>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-dark">LƯU THAY ĐỔI</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- TAB SỔ ĐỊA CHỈ -->
                <div class="tab-pane fade" id="v-pills-address" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                        <h3 class="content-title border-0 pb-0 mb-0">Sổ Địa Chỉ</h3>
                        <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                            <i class="fas fa-plus me-2"></i> Thêm địa chỉ mới
                        </button>
                    </div>

                    @if(empty($addresses))
                        <div class="text-center py-4 text-muted">
                            <i class="far fa-map fa-3x mb-3 opacity-50"></i>
                            <p>Bạn chưa thêm địa chỉ nào.</p>
                        </div>
                    @else
                        @foreach($addresses as $addr)
                        <div class="address-card {{ $addr['is_default'] ? 'is-default' : '' }}">
                            @if($addr['is_default'])
                                <span class="badge-default"><i class="fas fa-check-circle me-1"></i> Mặc định</span>
                            @endif
                            <div class="address-name">{{ $addr['fullname'] }}</div>
                            <div class="address-phone">SĐT: {{ $addr['phone'] }}</div>
                            <div class="address-text">{{ $addr['address'] }}</div>
                            
                            <div class="d-flex gap-2">
                                @if(!$addr['is_default'])
                                    <a href="/profile/setDefaultAddress/{{ $addr['id'] }}" class="btn btn-outline-dark">Đặt làm mặc định</a>
                                @endif
                                <a href="/profile/deleteAddress/{{ $addr['id'] }}" class="btn btn-outline-dark text-danger border-danger" onclick="return confirm('Bạn có chắc muốn xóa địa chỉ này?');">Xóa</a>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm Địa Chỉ -->
<div class="modal fade" id="addAddressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-0 border-0">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title font-heading fs-4">Thêm Địa Chỉ Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/profile/addAddress" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Họ và tên người nhận</label>
                        <input type="text" class="form-control" name="fullname" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="tel" class="form-control" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ chi tiết (Số nhà, Đường, Phường/Xã...)</label>
                        <textarea class="form-control" name="address" rows="3" required></textarea>
                    </div>
                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="defaultCheck" name="is_default" value="1">
                        <label class="form-check-label" for="defaultCheck">Đặt làm địa chỉ mặc định</label>
                    </div>
                    <button type="submit" class="btn btn-dark w-100">LƯU ĐỊA CHỈ</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const Toast = Swal.mixin({
            toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true
        });

        <?php if (!empty($successMsg)): ?>
            Toast.fire({ icon: 'success', title: '<?php echo addslashes($successMsg); ?>' });
        <?php endif; ?>
        <?php if (!empty($errorMsg)): ?>
            Toast.fire({ icon: 'error', title: '<?php echo addslashes($errorMsg); ?>' });
        <?php endif; ?>
        
        // Tự động submit form khi chọn file avatar
        const avatarInput = document.getElementById('avatarInput');
        if(avatarInput) {
            avatarInput.addEventListener('change', function() {
                if(this.files && this.files[0]) {
                    document.getElementById('avatarForm').submit();
                }
            });
        }
    });

    function confirmLogout(event) {
        event.preventDefault();
        Swal.fire({
            title: 'Đăng xuất?', text: "Bạn có chắc chắn muốn đăng xuất tài khoản?", icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#111', cancelButtonColor: '#d33',
            confirmButtonText: 'Có, đăng xuất', cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '/auth/logout';
            }
        });
    }
</script>
@endsection