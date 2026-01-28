@extends('layouts.client')

@section('title', $title)

@section('content')
<div class="row justify-content-center py-5">
    <div class="col-md-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h3 class="text-center fw-bold mb-4 text-primary">Đăng Nhập</h3>

                <!-- Hiển thị thông báo thành công (ví dụ từ trang đăng ký qua) -->
                @if(isset($_SESSION['success']))
                    <div class="alert alert-success">
                        {{ $_SESSION['success'] }}
                        @php unset($_SESSION['success']); @endphp
                    </div>
                @endif

                <!-- Hiển thị lỗi -->
                @if(!empty($error))
                    <div class="alert alert-danger">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $error }}
                    </div>
                @endif

                <form action="/auth/handleLogin" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control form-control-lg" placeholder="nhapemail@example.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mật khẩu</label>
                        <input type="password" name="password" class="form-control form-control-lg" placeholder="********" required>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold">Đăng Nhập</button>
                    </div>

                    <div class="text-center">
                        <p class="text-muted">Chưa có tài khoản? <a href="/auth/register" class="text-decoration-none fw-bold">Đăng ký ngay</a></p>
                        <a href="#" class="small text-muted">Quên mật khẩu?</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection