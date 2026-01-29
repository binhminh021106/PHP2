@extends('layouts.client')

@section('title', 'Đăng ký tài khoản')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        {{-- Thu hẹp form lại (col-lg-5) để nhìn gọn gàng, chuyên nghiệp như các trang lớn --}}
        <div class="col-md-8 col-lg-6 col-xl-5">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-gradient bg-primary text-white text-center py-4">
                    <h3 class="mb-0 fw-bold text-uppercase fs-4">Đăng Ký Thành Viên</h3>
                </div>
                <div class="card-body p-4 p-md-5 bg-white">
                    
                    {{-- Hiển thị thông báo lỗi chung --}}
                    @if(isset($error) && !empty($error))
                        <div class="alert alert-danger text-center mb-4 rounded-3 shadow-sm">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $error }}
                        </div>
                    @endif

                    <form action="/auth/handleRegister" method="POST">
                        {{-- Xếp tất cả vào 1 cột dọc (col-12) --}}
                        <div class="row g-3">
                            
                            {{-- Họ và tên --}}
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control {{ isset($errors['name']) ? 'is-invalid' : '' }}" 
                                           id="name" name="name" 
                                           value="{{ $old['name'] ?? '' }}" 
                                           placeholder="Họ và tên">
                                    <label for="name">Họ và tên <span class="text-danger">*</span></label>
                                    @if(isset($errors['name']))
                                        <div class="invalid-feedback">{{ $errors['name'] }}</div>
                                    @endif
                                </div>
                            </div>

                            {{-- Số điện thoại --}}
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control {{ isset($errors['phone']) ? 'is-invalid' : '' }}" 
                                           id="phone" name="phone" 
                                           value="{{ $old['phone'] ?? '' }}" 
                                           placeholder="09xxxxxxxxx">
                                    <label for="phone">Số điện thoại <span class="text-danger">*</span></label>
                                    @if(isset($errors['phone']))
                                        <div class="invalid-feedback">{{ $errors['phone'] }}</div>
                                    @endif
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="email" class="form-control {{ isset($errors['email']) ? 'is-invalid' : '' }}" 
                                           id="email" name="email" 
                                           value="{{ $old['email'] ?? '' }}" 
                                           placeholder="name@example.com">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    @if(isset($errors['email']))
                                        <div class="invalid-feedback">{{ $errors['email'] }}</div>
                                    @endif
                                </div>
                            </div>

                            {{-- Mật khẩu --}}
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="password" class="form-control {{ isset($errors['password']) ? 'is-invalid' : '' }}" 
                                           id="password" name="password" placeholder="******">
                                    <label for="password">Mật khẩu <span class="text-danger">*</span></label>
                                    @if(isset($errors['password']))
                                        <div class="invalid-feedback">{{ $errors['password'] }}</div>
                                    @endif
                                </div>
                            </div>

                            {{-- Nhập lại mật khẩu --}}
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="password" class="form-control {{ isset($errors['confirm_password']) ? 'is-invalid' : '' }}" 
                                           id="confirm_password" name="confirm_password" placeholder="******">
                                    <label for="confirm_password">Nhập lại mật khẩu <span class="text-danger">*</span></label>
                                    @if(isset($errors['confirm_password']))
                                        <div class="invalid-feedback">{{ $errors['confirm_password'] }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold text-uppercase py-3 rounded-pill shadow-sm">
                                <i class="bi bi-person-plus-fill me-2"></i> Đăng Ký Ngay
                            </button>
                        </div>

                    </form>
                </div>
                <div class="card-footer bg-light text-center py-4 border-top">
                    <p class="mb-0 text-muted">Bạn đã có tài khoản? <a href="/auth/login" class="text-decoration-none fw-bold text-primary">Đăng nhập tại đây</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection