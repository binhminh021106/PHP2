@extends('layouts.client')

@section('title', $title)

@section('content')
<div class="row justify-content-center py-4">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h3 class="text-center fw-bold mb-4 text-success">Đăng Ký Tài Khoản</h3>

                @if(isset($_SESSION['error']))
                    <div class="alert alert-danger">
                        {{ $_SESSION['error'] }}
                        @php unset($_SESSION['error']); @endphp
                    </div>
                @endif

                <form action="/auth/handleRegister" method="POST">
                    <!-- Họ tên -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Họ và Tên <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control {{ isset($errors['name']) ? 'is-invalid' : '' }}" 
                               value="{{ $old['name'] ?? '' }}" placeholder="Nguyễn Văn A">
                        @if(isset($errors['name']))
                            <div class="invalid-feedback">{{ $errors['name'] }}</div>
                        @endif
                    </div>

                    <div class="row">
                        <!-- Email -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control {{ isset($errors['email']) ? 'is-invalid' : '' }}" 
                                   value="{{ $old['email'] ?? '' }}" placeholder="email@vidu.com">
                            @if(isset($errors['email']))
                                <div class="invalid-feedback">{{ $errors['email'] }}</div>
                            @endif
                        </div>
                        <!-- SĐT -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control {{ isset($errors['phone']) ? 'is-invalid' : '' }}" 
                                   value="{{ $old['phone'] ?? '' }}" placeholder="09xxxxxxxx">
                            @if(isset($errors['phone']))
                                <div class="invalid-feedback">{{ $errors['phone'] }}</div>
                            @endif
                        </div>
                    </div>

                    <!-- Mật khẩu -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control {{ isset($errors['password']) ? 'is-invalid' : '' }}">
                            @if(isset($errors['password']))
                                <div class="invalid-feedback">{{ $errors['password'] }}</div>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nhập lại mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" name="confirm_password" class="form-control {{ isset($errors['confirm_password']) ? 'is-invalid' : '' }}">
                            @if(isset($errors['confirm_password']))
                                <div class="invalid-feedback">{{ $errors['confirm_password'] }}</div>
                            @endif
                        </div>
                    </div>

                    <!-- Địa chỉ -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Địa chỉ</label>
                        <textarea name="address" class="form-control" rows="2">{{ $old['address'] ?? '' }}</textarea>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-success btn-lg fw-bold">Đăng Ký</button>
                    </div>

                    <div class="text-center">
                        <p class="text-muted">Đã có tài khoản? <a href="/auth/login" class="text-decoration-none fw-bold">Đăng nhập</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection