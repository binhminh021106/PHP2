@extends('layouts.client')

@section('title', $pageTitle ?? 'Trang chủ')

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
    }

    .btn-dark:hover {
        background: #333;
        transform: translateY(-2px);
    }

    .btn-outline-light:hover {
        background: white;
        color: black;
    }

    .hero-section {
        height: 85vh;
        min-height: 550px;
        background-position: center;
        background-size: cover;
        position: relative;
        display: flex;
        align-items: center;
    }

    .hero-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.3);
    }

    .hero-content {
        position: relative;
        z-index: 2;
        color: white;
        max-width: 700px;
        padding-left: 5%;
        opacity: 0;
        animation: fadeUp 1s ease forwards 0.5s;
    }

    .hero-subtitle {
        text-transform: uppercase;
        letter-spacing: 3px;
        font-size: 0.9rem;
        margin-bottom: 15px;
        display: block;
    }

    .hero-title {
        font-family: var(--font-heading);
        font-size: 4rem;
        line-height: 1.1;
        margin-bottom: 25px;
    }

    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .cat-grid-item {
        position: relative;
        display: block;
        height: 350px;
        overflow: hidden;
        margin-bottom: 20px;
    }

    .cat-grid-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.8s ease;
    }

    .cat-grid-item:hover img {
        transform: scale(1.1);
    }

    .cat-content {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background: rgba(0, 0, 0, 0.2);
        transition: var(--transition);
    }

    .cat-grid-item:hover .cat-content {
        background: rgba(0, 0, 0, 0.4);
    }

    .cat-name {
        color: white;
        font-family: var(--font-heading);
        font-size: 2rem;
        margin-bottom: 10px;
        transform: translateY(10px);
        transition: var(--transition);
    }

    .cat-link {
        color: white;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 2px;
        border-bottom: 1px solid white;
        padding-bottom: 3px;
        opacity: 0;
        transform: translateY(10px);
        transition: var(--transition);
    }

    .cat-grid-item:hover .cat-name,
    .cat-grid-item:hover .cat-link {
        transform: translateY(0);
        opacity: 1;
    }

    .product-card {
        background: transparent;
        border: none;
        margin-bottom: 30px;
    }

    .product-thumb {
        position: relative;
        overflow: hidden;
        aspect-ratio: 3/4;
    }

    .product-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-actions {
        position: absolute;
        bottom: 15px;
        left: 0;
        right: 0;
        text-align: center;
        opacity: 0;
        transform: translateY(20px);
        transition: var(--transition);
    }

    .product-card:hover .product-actions {
        opacity: 1;
        transform: translateY(0);
    }

    .action-btn {
        background: white;
        color: var(--color-dark);
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 0 5px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: var(--transition);
        text-decoration: none;
    }

    .action-btn:hover {
        background: var(--color-dark);
        color: white;
    }

    .product-info {
        padding-top: 15px;
        text-align: center;
    }

    .product-category {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #888;
        letter-spacing: 1px;
    }

    .product-title {
        font-family: var(--font-base);
        font-size: 1rem;
        font-weight: 500;
        margin: 5px 0;
    }

    .product-title a {
        color: var(--color-dark);
        text-decoration: none;
        transition: color 0.3s;
    }

    .product-title a:hover {
        color: var(--color-accent);
    }

    .product-price {
        font-weight: 600;
        font-size: 1rem;
    }

    .section-title {
        font-family: var(--font-heading);
        font-size: 2.5rem;
        text-align: center;
        margin-bottom: 10px;
    }

    .section-subtitle {
        text-align: center;
        color: #666;
        margin-bottom: 40px;
        font-size: 1rem;
    }
</style>

<section class="hero-section" style="background-image: url('https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?q=80&w=2070&auto=format&fit=crop');">
    <div class="hero-overlay"></div>
    <div class="container-fluid">
        <div class="hero-content">
            <span class="hero-subtitle">Bộ sưu tập mùa hè 2024</span>
            <h1 class="hero-title">Đẳng cấp <br>Thời trang Phái mạnh</h1>
            <p class="mb-4 text-light opacity-75 d-none d-md-block" style="max-width: 500px;">
                Khám phá phong cách tối giản nhưng đầy tinh tế. Chất liệu cao cấp, thiết kế hiện đại dành cho quý ông thành đạt.
            </p>
            <a href="#shop-now" class="btn btn-light px-5">Mua ngay</a>
            <a href="#collections" class="btn btn-outline-light px-4 ms-2">Xem BST</a>
        </div>
    </div>
</section>

<section class="py-5 bg-white border-bottom">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-md-3">
                <i class="fas fa-shipping-fast fa-2x mb-3 text-secondary"></i>
                <h6 class="text-uppercase fw-bold ls-1">Miễn phí giao hàng</h6>
                <p class="small text-muted mb-0">Cho đơn hàng trên 500k</p>
            </div>
            <div class="col-md-3">
                <i class="fas fa-undo-alt fa-2x mb-3 text-secondary"></i>
                <h6 class="text-uppercase fw-bold ls-1">Đổi trả 30 ngày</h6>
                <p class="small text-muted mb-0">Hoàn tiền nếu lỗi NSX</p>
            </div>
            <div class="col-md-3">
                <i class="fas fa-headset fa-2x mb-3 text-secondary"></i>
                <h6 class="text-uppercase fw-bold ls-1">Hỗ trợ 24/7</h6>
                <p class="small text-muted mb-0">Hotline: 1900 1234</p>
            </div>
            <div class="col-md-3">
                <i class="fas fa-credit-card fa-2x mb-3 text-secondary"></i>
                <h6 class="text-uppercase fw-bold ls-1">Thanh toán an toàn</h6>
                <p class="small text-muted mb-0">Bảo mật tuyệt đối</p>
            </div>
        </div>
    </div>
</section>

@if(!empty($categories))
<section class="py-5" id="collections">
    <div class="container">
        <h2 class="section-title">Danh Mục</h2>
        <p class="section-subtitle">Lựa chọn phong cách phù hợp với bạn</p>

        <div class="row g-3">
            @foreach(array_slice($categories, 0, 3) as $cat)
            <div class="col-md-4">
                <a href="/category/{{ $cat['id'] }}" class="cat-grid-item">
                    @php $catImg = !empty($cat['image']) ? '/public/uploads/'.$cat['image'] : 'https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=500&q=80'; @endphp
                    <img src="{{ $catImg }}" alt="{{ $cat['name'] }}">
                    <div class="cat-content">
                        <span class="cat-name">{{ $cat['name'] }}</span>
                        <span class="cat-link">Xem Ngay</span>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="py-5 bg-light" id="shop-now">
    <div class="container">
        <h2 class="section-title">Sản Phẩm Mới</h2>
        <p class="section-subtitle">Cập nhật những xu hướng mới nhất tuần này</p>

        <div class="row">
            @if(!empty($products) && count($products) > 0)
            @foreach($products as $product)
            <div class="col-6 col-md-3">
                <div class="product-card">
                    <div class="product-thumb">
                        @php
                        $img = !empty($product['img_thumbnail'])
                        ? '/storage/uploads/products/' . $product['img_thumbnail']
                        : 'https://placehold.co/400x533?text=No+Image';
                        @endphp
                        <img src="{{ $img }}" alt="{{ $product['name'] }}">

                        @if($product['price_sale'] > 0)
                        <span class="position-absolute top-0 start-0 bg-dark text-white small px-2 py-1 m-2">SALE</span>
                        @endif

                        <div class="product-actions">
                            <a href="/detail/index/{{ $product['id'] }}" class="action-btn" title="Xem nhanh">
                                <i class="far fa-eye"></i>
                            </a>
                            <form action="/cart/add" method="POST" class="d-inline">
                                <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="action-btn" title="Thêm vào giỏ">
                                    <i class="fas fa-shopping-bag"></i>
                                </button>
                            </form>
                            <a href="#" class="action-btn" title="Yêu thích">
                                <i class="far fa-heart"></i>
                            </a>
                        </div>
                    </div>

                    <div class="product-info">
                        <div class="product-category">{{ $product['category_name'] ?? 'Fashion' }}</div>
                        <h3 class="product-title">
                            <a href="/detail/index/{{ $product['id'] }}">{{ $product['name'] }}</a>
                        </h3>
                        <div class="product-price">
                            @if($product['price_sale'] > 0 && $product['price_sale'] < $product['price_regular'])
                                <span class="text-danger">{{ number_format($product['price_sale'], 0, ',', '.') }}đ</span>
                                <del class="text-muted small ms-2 fw-normal">{{ number_format($product['price_regular'], 0, ',', '.') }}đ</del>
                                @else
                                <span>{{ number_format($product['price_regular'], 0, ',', '.') }}đ</span>
                                @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            @else
            <div class="col-12 text-center">
                <p>Chưa có sản phẩm nào.</p>
            </div>
            @endif
        </div>

        <div class="text-center mt-4">
            <a href="/shop" class="btn btn-outline-dark px-5">Xem Tất Cả Sản Phẩm</a>
        </div>
    </div>
</section>

<section class="py-5 bg-dark text-white text-center d-flex align-items-center justify-content-center"
    style="min-height: 400px; background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&w=1600&q=80') center/cover fixed;">
    <div>
        <h2 class="display-4 fw-bold mb-3" style="font-family: var(--font-heading);">Summer Sale 2024</h2>
        <p class="lead mb-4">Giảm giá lên đến 50% cho tất cả sản phẩm áo thun & quần shorts.</p>
        <a href="#" class="btn btn-light px-5 py-3">Săn Sale Ngay</a>
    </div>
</section>

<section class="py-5">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <i class="far fa-envelope fa-2x mb-3 text-muted"></i>
                <h3 class="fw-bold mb-3">Đăng ký nhận tin</h3>
                <p class="text-muted mb-4">Nhận thông tin về các sản phẩm mới và khuyến mãi đặc biệt.</p>
                <form class="d-flex">
                    <input type="email" class="form-control rounded-0 border-dark" placeholder="Email của bạn...">
                    <button class="btn btn-dark rounded-0 px-4">Đăng ký</button>
                </form>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        <?php if (isset($_SESSION['success'])): ?>
            Toast.fire({
                icon: 'success',
                title: '<?= addslashes($_SESSION['success']) ?>'
            });
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            Toast.fire({
                icon: 'error',
                title: '<?= addslashes($_SESSION['error']) ?>'
            });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    });
</script>

@endsection