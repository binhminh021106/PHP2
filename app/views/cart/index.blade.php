@extends('layouts.client')

@section('title', $title ?? 'Giỏ hàng')

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

    body {
        font-family: var(--font-base);
        color: var(--color-dark);
        background-color: #fcfcfc;
    }

    .btn {
        border-radius: 0;
        padding: 12px 28px;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 1.5px;
        font-weight: 500;
        transition: var(--transition);
    }

    .btn-dark {
        background: var(--color-dark);
        border-color: var(--color-dark);
        color: white;
    }

    .btn-dark:hover {
        background: #333;
        color: white;
        transform: translateY(-2px);
    }

    .btn-outline-dark {
        border: 1px solid var(--color-dark);
        color: var(--color-dark);
        background: transparent;
    }

    .btn-outline-dark:hover {
        background: var(--color-dark);
        color: white;
    }

    .page-title {
        font-family: var(--font-heading);
        font-size: 2.5rem;
        font-weight: 600;
        text-align: center;
        margin-bottom: 40px;
    }

    /* Cart Table Styles */
    .cart-table th {
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 1px;
        color: #777;
        font-weight: 600;
        border-top: none;
        padding-bottom: 15px;
    }

    .cart-table td {
        vertical-align: middle;
        padding: 20px 0;
        border-bottom: 1px solid #eee;
    }

    .cart-item-img {
        width: 80px;
        height: 106px;
        object-fit: cover;
        border: 1px solid #f0f0f0;
    }

    .cart-product-title {
        font-weight: 500;
        font-size: 1.1rem;
        color: var(--color-dark);
        text-decoration: none;
        transition: var(--transition);
        display: block;
        margin-bottom: 5px;
    }

    .cart-product-title:hover {
        color: var(--color-accent);
    }

    .cart-variant-info {
        font-size: 0.85rem;
        color: #777;
    }

    /* Quantity Input in Cart */
    .qty-form {
        display: inline-flex;
        border: 1px solid #ddd;
        align-items: center;
    }

    .qty-form button {
        background: transparent;
        border: none;
        padding: 5px 12px;
        cursor: pointer;
        font-size: 1rem;
        color: #555;
    }

    .qty-form button:hover {
        background: #f0f0f0;
    }

    .qty-form input {
        width: 40px;
        text-align: center;
        border: none;
        font-weight: 500;
        pointer-events: none;
    }

    .btn-remove {
        color: #dc3545;
        background: none;
        border: none;
        padding: 5px;
        cursor: pointer;
        transition: var(--transition);
    }

    .btn-remove:hover {
        color: #a71d2a;
        transform: scale(1.1);
    }

    /* Cart Summary */
    .cart-summary {
        background: #fff;
        padding: 30px;
        border: 1px solid #eee;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.02);
    }

    .summary-title {
        font-family: var(--font-heading);
        font-size: 1.5rem;
        border-bottom: 1px solid #eee;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        font-size: 1rem;
    }

    .summary-total {
        font-size: 1.25rem;
        font-weight: 600;
        border-top: 1px solid #eee;
        padding-top: 15px;
        margin-top: 15px;
    }
</style>

<div class="bg-light py-3 border-bottom mb-5">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-dark">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Giỏ hàng</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5 pb-5">
    <h1 class="page-title">Giỏ Hàng Của Bạn</h1>

    @if(empty($cartItems))
    <div class="text-center py-5">
        <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
        <h3>Giỏ hàng của bạn đang trống</h3>
        <p class="text-muted mb-4">Hãy quay lại cửa hàng để chọn cho mình những sản phẩm yêu thích nhé!</p>
        <a href="/shop" class="btn btn-dark">Tiếp Tục Mua Sắm</a>
    </div>
    @else
    <div class="row g-5">
        <!-- Cột Danh sách sản phẩm -->
        <div class="col-lg-8">
            <div class="table-responsive">
                <table class="table cart-table align-middle">
                    <thead>
                        <tr>
                            <th colspan="2">Sản phẩm</th>
                            <th class="text-center">Giá</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-end">Tạm tính</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cartItems as $item)
                        @php
                        $img = !empty($item['image']) ? '/storage/uploads/variants/' . $item['image'] : 'https://placehold.co/80x106?text=No+Img';
                        $color = $item['parsed_attr']['Color'] ?? '';
                        $size = $item['parsed_attr']['Size'] ?? '';
                        @endphp
                        <tr>
                            <td style="width: 100px;">
                                <a href="/home/detail/{{ $item['product_id'] }}">
                                    <img src="{{ $img }}" alt="{{ $item['product_name'] }}" class="cart-item-img">
                                </a>
                            </td>
                            <td>
                                <a href="/home/detail/{{ $item['product_id'] }}" class="cart-product-title">{{ $item['product_name'] }}</a>
                                <div class="cart-variant-info">
                                    @if($color) Màu: {{ $color }} @endif
                                    @if($size) | Size: {{ $size }} @endif
                                </div>
                            </td>
                            <td class="text-center fw-medium">
                                {{ number_format($item['price'], 0, ',', '.') }}đ
                            </td>
                            <td class="text-center">
                                <!-- Form Update Số lượng -->
                                <form action="/cart/update" method="POST" class="qty-form">
                                    <input type="hidden" name="cart_item_id" value="{{ $item['cart_item_id'] }}">
                                    <button type="submit" name="quantity" value="{{ $item['quantity'] - 1 }}">-</button>
                                    <input type="text" value="{{ $item['quantity'] }}" readonly>
                                    <button type="submit" name="quantity" value="{{ $item['quantity'] + 1 }}">+</button>
                                </form>
                            </td>
                            <td class="text-end fw-bold text-dark">
                                {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}đ
                            </td>
                            <td class="text-end">
                                <!-- Link Xóa -->
                                <a href="/cart/delete/{{ $item['cart_item_id'] }}" class="btn-remove" title="Xóa sản phẩm" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?');">
                                    <i class="far fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                <a href="/shop" class="btn btn-outline-dark"><i class="fas fa-arrow-left me-2"></i> Tiếp tục mua sắm</a>
            </div>
        </div>

        <!-- Cột Tổng quan đơn hàng -->
        <div class="col-lg-4">
            <div class="cart-summary">
                <h3 class="summary-title">Tóm tắt đơn hàng</h3>

                <div class="summary-row">
                    <span class="text-muted">Tạm tính:</span>
                    <span class="fw-medium">{{ number_format($totalPrice, 0, ',', '.') }}đ</span>
                </div>

                <div class="summary-row">
                    <span class="text-muted">Giao hàng:</span>
                    <span>Tính ở bước thanh toán</span>
                </div>

                <div class="summary-row summary-total">
                    <span>Tổng cộng:</span>
                    <span class="text-danger">{{ number_format($totalPrice, 0, ',', '.') }}đ</span>
                </div>

                <a href="/checkout" class="btn btn-dark w-100 py-3 mt-4">Tiến Hành Thanh Toán</a>

                <div class="text-center mt-3">
                    <i class="fab fa-cc-visa fa-2x mx-1 text-muted"></i>
                    <i class="fab fa-cc-mastercard fa-2x mx-1 text-muted"></i>
                    <i class="fab fa-cc-paypal fa-2x mx-1 text-muted"></i>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- SWEETALERT2 TOAST NOTIFICATIONS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        <?php if (!empty($successMsg)): ?>
            Toast.fire({
                icon: 'success',
                title: '<?php echo addslashes($successMsg); ?>'
            });
        <?php endif; ?>

        <?php if (!empty($errorMsg)): ?>
            Toast.fire({
                icon: 'error',
                title: '<?php echo addslashes($errorMsg); ?>'
            });
        <?php endif; ?>
    });
</script>

@endsection