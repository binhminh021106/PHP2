@extends('layouts.client')

@section('title', 'Menswear - Thời trang nam cơ bản')

@section('content')

<!-- 1. Hero Banner (Ảnh bìa lớn) -->
<div class="row mb-5">
    <div class="col-12">
        <div class="position-relative bg-dark text-white text-center d-flex align-items-center justify-content-center"
            style="height: 500px; background: url('https://images.unsplash.com/photo-1490114538077-0a7f8cb49891?q=80&w=1470&auto=format&fit=crop') center/cover no-repeat;">
            <div class="bg-dark position-absolute w-100 h-100 opacity-25"></div> <!-- Overlay làm tối ảnh -->
            <div class="position-relative z-1">
                <h1 class="display-4 fw-bold text-uppercase ls-2">Phong Cách Tối Giản</h1>
                <p class="lead mb-4">Nâng tầm bản lĩnh phái mạnh với bộ sưu tập mới nhất.</p>
                <a href="#new-arrival" class="btn btn-light rounded-0 px-4 py-2 fw-bold text-uppercase">Mua ngay</a>
            </div>
        </div>
    </div>
</div>

<!-- 2. Danh mục nổi bật (3 cột) -->
<div class="row g-4 mb-5 text-center">
    <div class="col-md-4">
        <div class="hover-zoom overflow-hidden position-relative">
            <img src="https://images.unsplash.com/photo-1620012253295-c15cc3e65df4?q=80&w=1000&auto=format&fit=crop" class="img-fluid w-100" alt="Áo">
            <a href="#" class="position-absolute top-50 start-50 translate-middle btn btn-white bg-white rounded-0 px-4 fw-bold shadow-sm">ÁO NAM</a>
        </div>
    </div>
    <div class="col-md-4">
        <div class="hover-zoom overflow-hidden position-relative">
            <img src="https://images.unsplash.com/photo-1473966968600-fa801b869a1a?q=80&w=1000&auto=format&fit=crop" class="img-fluid w-100" alt="Quần">
            <a href="#" class="position-absolute top-50 start-50 translate-middle btn btn-white bg-white rounded-0 px-4 fw-bold shadow-sm">QUẦN NAM</a>
        </div>
    </div>
    <div class="col-md-4">
        <div class="hover-zoom overflow-hidden position-relative">
            <img src="https://images.unsplash.com/photo-1521223890158-5d669a0ee79c?q=80&w=1000&auto=format&fit=crop" class="img-fluid w-100" alt="Phụ kiện">
            <a href="#" class="position-absolute top-50 start-50 translate-middle btn btn-white bg-white rounded-0 px-4 fw-bold shadow-sm">PHỤ KIỆN</a>
        </div>
    </div>
</div>

<!-- 3. Sản phẩm mới (Grid sản phẩm full-width) -->
<div class="d-flex justify-content-between align-items-center mb-4" id="new-arrival">
    <h3 class="fw-bold text-uppercase ls-1 m-0">Sản phẩm mới</h3>
    <a href="/product" class="text-decoration-none text-dark border-bottom border-dark pb-1 small fw-bold">XEM TẤT CẢ</a>
</div>

<div class="row g-4 mb-5">
    @if(!empty($products))
    @foreach($products as $product)
    <div class="col-6 col-md-3">
        <div class="card border-0 h-100 product-card">
            <!-- Ảnh sản phẩm -->
            <div class="position-relative overflow-hidden bg-light">
                <!-- Ảnh chính -->
                @if(!empty($product['img_thumbnail']))
                <img src="/storage/uploads/products/{{ $product['img_thumbnail'] }}"
                    class="card-img-top object-fit-cover"
                    style="aspect-ratio: 3/4;"
                    alt="{{ $product['name'] }}">
                @else
                <!-- Ảnh placeholder nếu không có ảnh thật -->
                <img src="https://placehold.co/300x400?text=No+Image"
                    class="card-img-top object-fit-cover"
                    style="aspect-ratio: 3/4;"
                    alt="No Image">
                @endif

                <!-- Badge Sale -->
                @if($product['price_sale'] > 0)
                <span class="position-absolute top-0 start-0 bg-danger text-white small px-2 py-1 m-2 fw-bold">SALE</span>
                @endif

                <!-- Nút Quick View / Add to Cart (Hiện khi hover) -->
                <div class="product-action position-absolute bottom-0 start-0 w-100 p-2 d-flex gap-1 justify-content-center opacity-0 transition-opacity">
                    <button class="btn btn-light bg-white shadow-sm btn-sm rounded-0 w-100 fw-bold">THÊM VÀO GIỎ</button>
                </div>
            </div>

            <!-- Thông tin sản phẩm -->
            <div class="card-body px-0 pt-3 pb-0 text-center">
                <div class="text-muted small text-uppercase mb-1" style="font-size: 0.75rem;">
                    {{ $product['category_name'] ?? 'BST Mới' }}
                </div>
                <h6 class="card-title text-truncate fw-bold">
                    <a href="/product/detail/{{ $product['id'] }}" class="text-dark text-decoration-none">
                        {{ $product['name'] }}
                    </a>
                </h6>

                <div class="price">
                    @if($product['price_sale'] > 0)
                    <span class="text-danger fw-bold">{{ number_format($product['price_sale']) }}₫</span>
                    <span class="text-muted text-decoration-line-through small ms-2">{{ number_format($product['price_regular']) }}₫</span>
                    @else
                    <span class="fw-bold">{{ number_format($product['price_regular']) }}₫</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
    @else
    <div class="col-12 text-center py-5">
        <p class="text-muted">Chưa có sản phẩm nào được cập nhật.</p>
    </div>
    @endif
</div>

<!-- Style bổ sung cho trang Home -->
<style>
    .ls-1 {
        letter-spacing: 1px;
    }

    .ls-2 {
        letter-spacing: 3px;
    }

    /* Hiệu ứng hover sản phẩm */
    .product-card:hover .product-action {
        opacity: 1 !important;
    }

    .transition-opacity {
        transition: opacity 0.3s ease;
    }

    /* Hiệu ứng zoom ảnh danh mục */
    .hover-zoom img {
        transition: transform 0.5s ease;
    }

    .hover-zoom:hover img {
        transform: scale(1.05);
    }
</style>

@endsection