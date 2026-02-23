@extends('layouts.client')

@section('title', $title ?? 'Danh sách yêu thích')

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

    body { font-family: var(--font-base); color: var(--color-dark); background-color: #fcfcfc; }

    .page-title { font-family: var(--font-heading); font-size: 2.5rem; font-weight: 600; text-align: center; margin-bottom: 40px; }

    .btn { border-radius: 0; padding: 12px 28px; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1.5px; font-weight: 500; transition: var(--transition); }
    .btn-dark { background: var(--color-dark); border-color: var(--color-dark); color: white; }
    .btn-dark:hover { background: #333; color: white; transform: translateY(-2px); }

    /* Product Card Styles (Tái sử dụng từ trang chủ) */
    .product-card { background: transparent; border: none; margin-bottom: 30px; }
    .product-thumb { position: relative; overflow: hidden; aspect-ratio: 3/4; }
    .product-thumb img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.8s ease; }
    .product-card:hover .product-thumb img { transform: scale(1.05); }
    
    .product-actions { position: absolute; bottom: 15px; left: 0; right: 0; text-align: center; opacity: 0; transform: translateY(20px); transition: var(--transition); }
    .product-card:hover .product-actions { opacity: 1; transform: translateY(0); }
    
    .action-btn { background: white; color: var(--color-dark); width: 45px; height: 45px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin: 0 5px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); transition: var(--transition); text-decoration: none; }
    .action-btn:hover { background: var(--color-dark); color: white; }
    .action-btn.remove-btn:hover { background: #dc3545; color: white; }

    .product-info { padding-top: 15px; text-align: center; }
    .product-category { font-size: 0.75rem; text-transform: uppercase; color: #888; letter-spacing: 1px; }
    .product-title { font-family: var(--font-base); font-size: 1rem; font-weight: 500; margin: 5px 0; }
    .product-title a { color: var(--color-dark); text-decoration: none; transition: color 0.3s; }
    .product-title a:hover { color: var(--color-accent); }
    .product-price { font-weight: 600; font-size: 1rem; }
</style>

<div class="bg-light py-3 border-bottom mb-5">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-dark">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Danh sách yêu thích</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5 pb-5">
    <h1 class="page-title">Sản Phẩm Yêu Thích</h1>

    @if(empty($wishlistItems))
        <div class="text-center py-5">
            <i class="far fa-heart fa-4x text-muted mb-3"></i>
            <h3>Chưa có sản phẩm nào</h3>
            <p class="text-muted mb-4">Lưu lại những sản phẩm bạn yêu thích để dễ dàng mua sắm sau nhé!</p>
            <a href="/shop" class="btn btn-dark">Khám Phá Ngay</a>
        </div>
    @else
        <div class="row">
            @foreach($wishlistItems as $item)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="product-card">
                    <div class="product-thumb">
                        @php
                            $img = !empty($item['img_thumbnail']) 
                                ? '/storage/uploads/products/' . $item['img_thumbnail'] 
                                : 'https://placehold.co/400x533?text=No+Image';
                        @endphp
                        <img src="{{ $img }}" alt="{{ $item['name'] }}">
                        
                        @if($item['price_sale'] > 0)
                            <span class="position-absolute top-0 start-0 bg-dark text-white small px-2 py-1 m-2">SALE</span>
                        @endif

                        <div class="product-actions">
                            <a href="/home/detail/{{ $item['id'] }}" class="action-btn" title="Xem chi tiết">
                                <i class="far fa-eye"></i>
                            </a>
                            <!-- Nút xóa khỏi wishlist -->
                            <a href="/wishlist/remove/{{ $item['id'] }}" class="action-btn remove-btn" title="Xóa khỏi danh sách" onclick="return confirm('Xóa sản phẩm này khỏi mục yêu thích?');">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>

                    <div class="product-info">
                        <div class="product-category">{{ $item['category_name'] ?? 'Fashion' }}</div>
                        <h3 class="product-title">
                            <a href="/home/detail/{{ $item['id'] }}">{{ $item['name'] }}</a>
                        </h3>
                        <div class="product-price">
                            @if($item['price_sale'] > 0 && $item['price_sale'] < $item['price_regular'])
                                <span class="text-danger">{{ number_format($item['price_sale'], 0, ',', '.') }}đ</span>
                                <del class="text-muted small ms-2 fw-normal">{{ number_format($item['price_regular'], 0, ',', '.') }}đ</del>
                            @else
                                <span>{{ number_format($item['price_regular'], 0, ',', '.') }}đ</span>
                            @endif
                        </div>
                        <div class="mt-2">
                            <a href="/home/detail/{{ $item['id'] }}" class="btn btn-outline-dark w-100 d-block" style="padding: 8px 0; font-size: 0.8rem;">Thêm Vào Giỏ</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

<!-- SWEETALERT2 TOAST NOTIFICATIONS (Tái sử dụng code bạn vừa chọn ở Canvas) -->
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