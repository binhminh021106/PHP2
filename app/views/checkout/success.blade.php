@extends('layouts.client')

@section('title', 'Đặt hàng thành công')

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
    
    .success-container {
        max-width: 650px;
        margin: 60px auto;
        padding: 50px 40px;
        background: #fff;
        border: 1px solid #eee;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        border-top: 5px solid var(--color-dark);
        text-align: center;
    }

    .icon-wrapper {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100px;
        height: 100px;
        background-color: rgba(201, 164, 124, 0.1);
        color: var(--color-accent);
        border-radius: 50%;
        margin-bottom: 25px;
    }

    .icon-wrapper i { font-size: 3rem; }

    .success-title {
        font-family: var(--font-heading);
        font-weight: 700;
        font-size: 2.2rem;
        margin-bottom: 15px;
        letter-spacing: 1px;
    }

    .success-msg { font-size: 1.1rem; color: #555; margin-bottom: 30px; line-height: 1.6; }

    .order-box {
        background: #f8f9fa;
        border: 1px dashed #ddd;
        padding: 25px;
        margin-bottom: 30px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .order-box span { text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1.5px; color: #777; margin-bottom: 10px; }
    .order-number { font-family: var(--font-heading); font-size: 2rem; font-weight: 600; color: var(--color-dark); }

    .next-steps { font-size: 0.95rem; color: #777; margin-bottom: 40px; }

    .btn-custom {
        border-radius: 0;
        padding: 14px 30px;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 1.5px;
        font-weight: 600;
        transition: var(--transition);
        display: inline-block;
        margin: 5px;
    }

    .btn-dark-custom { background: var(--color-dark); border: 1px solid var(--color-dark); color: white; }
    .btn-dark-custom:hover { background: #333; color: white; transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    
    .btn-outline-custom { background: transparent; border: 1px solid var(--color-dark); color: var(--color-dark); }
    .btn-outline-custom:hover { background: var(--color-dark); color: white; transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
</style>

<div class="container">
    <div class="success-container">
        <div class="icon-wrapper animate__animated animate__zoomIn">
            <i class="fas fa-check"></i>
        </div>
        
        <h1 class="success-title">ĐẶT HÀNG THÀNH CÔNG!</h1>
        
        <p class="success-msg">
            {{ $successMsg ?? 'Cảm ơn bạn đã lựa chọn MENSWEAR. Đơn hàng của bạn đã được hệ thống ghi nhận thành công.' }}
        </p>
        
        <div class="order-box">
            <span>Mã đơn hàng của bạn</span>
            <div class="order-number">#{{ $orderId }}</div>
        </div>

        <p class="next-steps">
            Chúng tôi sẽ sớm liên hệ với bạn qua số điện thoại đã cung cấp để xác nhận đơn hàng và tiến hành giao hàng. Bạn có thể theo dõi tiến trình đơn hàng trong phần quản lý tài khoản.
        </p>

        <div>
            <a href="/shop" class="btn-custom btn-dark-custom">TIẾP TỤC MUA SẮM</a>
            <a href="/order/history" class="btn-custom btn-outline-custom">XEM ĐƠN HÀNG</a>
        </div>
    </div>
</div>
@endsection