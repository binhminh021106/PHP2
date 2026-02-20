@extends('layouts.client')

@section('title', 'Quên mật khẩu')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap');
    :root { --font-base: 'Jost', sans-serif; --font-heading: 'Playfair Display', serif; --color-dark: #111; }
    body { font-family: var(--font-base); background-color: #f8f9fa; }
    .auth-wrapper { background: white; box-shadow: 0 10px 40px rgba(0,0,0,0.05); margin: 60px auto; max-width: 500px; padding: 40px; }
    .form-control { border-radius: 0; border: 1px solid #ddd; padding: 12px 15px; font-size: 1rem; }
    .form-control:focus { border-color: var(--color-dark); box-shadow: none; }
    .btn-auth { border-radius: 0; padding: 14px 28px; text-transform: uppercase; font-size: 0.9rem; letter-spacing: 2px; font-weight: 500; width: 100%; background: var(--color-dark); color: white; transition: 0.3s; border: none;}
    .btn-auth:hover { background: #333; color: white; transform: translateY(-2px); }
</style>

<div class="container">
    <div class="auth-wrapper">
        <div class="text-center mb-4">
            <h2 style="font-family: var(--font-heading); font-size: 2rem;">Quên Mật Khẩu</h2>
            <p class="text-muted small mt-2">Vui lòng nhập email bạn đã dùng để đăng ký. Chúng tôi sẽ gửi một đường dẫn để bạn đặt lại mật khẩu.</p>
        </div>

        @if(isset($success))
            <div class="alert alert-success rounded-0 border-0 bg-success text-white small"><i class="fas fa-check-circle me-2"></i> {{ $success }}</div>
        @endif
        @if(isset($error))
            <div class="alert alert-danger rounded-0 border-0 bg-danger text-white small"><i class="fas fa-exclamation-circle me-2"></i> {{ $error }}</div>
        @endif

        <form action="/auth/handleForgotPassword" method="POST">
            <div class="form-floating mb-4">
                <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                <label for="email" class="text-muted">Nhập Email của bạn</label>
            </div>
            <button type="submit" class="btn btn-auth">Gửi Link Khôi Phục <i class="fas fa-paper-plane ms-2"></i></button>
        </form>

        <div class="text-center mt-4">
            <a href="/auth/login" class="text-dark fw-bold text-decoration-none small"><i class="fas fa-arrow-left me-1"></i> Quay lại đăng nhập</a>
        </div>
    </div>
</div>
@endsection