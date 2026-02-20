@extends('layouts.client')

@section('title', 'Đăng ký tài khoản')

@section('content')

<!-- CUSTOM STYLE ĐỒNG BỘ VỚI TRANG CHỦ -->
<style>
    /* Import Font thời trang giống trang chủ */
    @import url('https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap');

    :root {
        --font-base: 'Jost', sans-serif;
        --font-heading: 'Playfair Display', serif;
        --color-dark: #111;
        --color-accent: #c9a47c; /* Màu vàng kim nhẹ */
        --transition: all 0.4s ease;
    }

    body {
        font-family: var(--font-base);
        color: var(--color-dark);
        background-color: #f8f9fa;
    }

    /* Override Bootstrap Buttons */
    .btn-auth {
        border-radius: 0; 
        padding: 14px 28px;
        text-transform: uppercase;
        font-size: 0.9rem;
        letter-spacing: 2px;
        font-weight: 500;
        transition: var(--transition);
        width: 100%;
        background: var(--color-dark);
        color: white;
        border: 1px solid var(--color-dark);
    }

    .btn-auth:hover {
        background: #333;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    /* Override Form Inputs */
    .form-control {
        border-radius: 0;
        border: 1px solid #ddd;
        padding: 12px 15px;
        font-size: 1rem;
    }

    .form-control:focus {
        border-color: var(--color-dark);
        box-shadow: none;
    }

    .form-floating label {
        color: #888;
    }

    .auth-title {
        font-family: var(--font-heading);
        font-size: 2.5rem;
        margin-bottom: 10px;
    }

    .auth-subtitle {
        color: #666;
        margin-bottom: 30px;
        font-size: 1rem;
    }

    .auth-link {
        color: var(--color-dark);
        text-decoration: none;
        border-bottom: 1px solid transparent;
        transition: var(--transition);
        font-weight: 500;
    }

    .auth-link:hover {
        color: var(--color-accent);
        border-bottom-color: var(--color-accent);
    }

    /* Layout chia đôi */
    .auth-wrapper {
        background: white;
        box-shadow: 0 10px 40px rgba(0,0,0,0.05);
        margin: 40px auto;
    }

    .auth-image {
        height: 100%;
        min-height: 750px; /* Dài hơn form login một chút vì form đăng ký nhiều field hơn */
        background-image: url('https://images.unsplash.com/photo-1617137968427-85924c800a22?q=80&w=1974&auto=format&fit=crop'); /* Đổi ảnh khác nhưng vẫn chất nam tính */
        background-position: center;
        background-size: cover;
    }
</style>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-11">
            <div class="row g-0 auth-wrapper">
                
                {{-- Cột hình ảnh (Ẩn trên mobile, hiện trên màn hình lớn) --}}
                <div class="col-md-5 d-none d-md-block">
                    <div class="auth-image"></div>
                </div>

                {{-- Cột Form đăng ký --}}
                <div class="col-md-7 d-flex align-items-center">
                    <div class="w-100 p-4 p-md-5 p-xl-5">
                        <div class="text-center mb-4">
                            <h2 class="auth-title">Đăng Ký</h2>
                            <p class="auth-subtitle">Trở thành thành viên để nhận những ưu đãi đặc quyền.</p>
                        </div>

                        {{-- Thông báo thành công --}}
                        @if(isset($success) && !empty($success))
                            <div class="alert alert-success rounded-0 border-0 bg-success text-white mb-4">
                                <i class="fas fa-check-circle me-2"></i> {{ $success }}
                            </div>
                        @endif

                        {{-- Thông báo lỗi chung --}}
                        @if(isset($error) && !empty($error))
                            <div class="alert alert-danger rounded-0 border-0 bg-danger text-white mb-4">
                                <i class="fas fa-exclamation-circle me-2"></i> {{ $error }}
                            </div>
                        @endif

                        <form action="/auth/handleRegister" method="POST">
                            <div class="row g-3">
                                
                                {{-- Họ và tên --}}
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="text" class="form-control {{ isset($errors['name']) ? 'is-invalid' : '' }}" 
                                               id="name" name="name" 
                                               value="{{ $old['name'] ?? '' }}" 
                                               placeholder="Họ và tên">
                                        <label for="name">Họ và tên *</label>
                                        @if(isset($errors['name']))
                                            <div class="invalid-feedback rounded-0 mt-1">{{ $errors['name'] }}</div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Số điện thoại & Email (Chia 2 cột trên màn lớn) --}}
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control {{ isset($errors['phone']) ? 'is-invalid' : '' }}" 
                                               id="phone" name="phone" 
                                               value="{{ $old['phone'] ?? '' }}" 
                                               placeholder="09xxxxxxxxx">
                                        <label for="phone">Số điện thoại *</label>
                                        @if(isset($errors['phone']))
                                            <div class="invalid-feedback rounded-0 mt-1">{{ $errors['phone'] }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email" class="form-control {{ isset($errors['email']) ? 'is-invalid' : '' }}" 
                                               id="email" name="email" 
                                               value="{{ $old['email'] ?? '' }}" 
                                               placeholder="name@example.com">
                                        <label for="email">Email *</label>
                                        @if(isset($errors['email']))
                                            <div class="invalid-feedback rounded-0 mt-1">{{ $errors['email'] }}</div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Mật khẩu & Nhập lại mật khẩu (Chia 2 cột) --}}
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="password" class="form-control {{ isset($errors['password']) ? 'is-invalid' : '' }}" 
                                               id="password" name="password" placeholder="******">
                                        <label for="password">Mật khẩu *</label>
                                        @if(isset($errors['password']))
                                            <div class="invalid-feedback rounded-0 mt-1">{{ $errors['password'] }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="password" class="form-control {{ isset($errors['confirm_password']) ? 'is-invalid' : '' }}" 
                                               id="confirm_password" name="confirm_password" placeholder="******">
                                        <label for="confirm_password">Nhập lại mật khẩu *</label>
                                        @if(isset($errors['confirm_password']))
                                            <div class="invalid-feedback rounded-0 mt-1">{{ $errors['confirm_password'] }}</div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Nút Submit --}}
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-auth">
                                        Tạo Tài Khoản <i class="fas fa-user-plus ms-2"></i>
                                    </button>
                                </div>

                                {{-- Social Login --}}
                                <div class="col-12 mt-3 text-center">
                                    <div class="position-relative mb-4 mt-2">
                                        <hr style="border-color: #ddd;">
                                        <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted" style="font-size: 0.85rem;">Hoặc đăng ký bằng</span>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <a href="/auth/googleLogin" class="btn btn-outline-dark w-100 rounded-0 py-2 d-flex justify-content-center align-items-center">
                                                <i class="fab fa-google text-danger me-2"></i> Google
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a href="/auth/facebookLogin" class="btn btn-outline-dark w-100 rounded-0 py-2 d-flex justify-content-center align-items-center">
                                                <i class="fab fa-facebook text-primary me-2"></i> Facebook
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                {{-- Chuyển sang đăng nhập --}}
                                <div class="col-12 text-center mt-4">
                                    <p class="mb-0 text-muted small">
                                        Bạn đã có tài khoản? 
                                        <a href="/auth/login" class="auth-link fw-bold">Đăng nhập tại đây</a>
                                    </p>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection