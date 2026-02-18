@extends('layouts.client')

@section('title', $product['name'] ?? 'Chi tiết sản phẩm')

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-dark">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product['name'] }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Cột Ảnh Sản Phẩm -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm mb-3">
                @php
                    $mainImg = !empty($product['img_thumbnail']) ? '/storage/uploads/products/' . $product['img_thumbnail'] : 'https://placehold.co/600x600?text=No+Image';
                @endphp
                <img id="mainImage" src="{{ $mainImg }}" 
                     class="card-img-top rounded" 
                     alt="{{ $product['name'] }}" 
                     style="max-height: 500px; object-fit: contain; background: #f8f9fa;">
            </div>
            
            <!-- Thumbnails (Ảnh nhỏ bên dưới) -->
            @if(!empty($product['gallery']) && count($product['gallery']) > 0)
            <div class="row g-2">
                <!-- Ảnh chính làm thumb đầu tiên -->
                <div class="col-3">
                     <img src="{{ $mainImg }}" 
                          class="img-fluid rounded border cursor-pointer opacity-100 gallery-item" 
                          onclick="changeImage(this, '{{ $mainImg }}')" 
                          style="cursor: pointer; aspect-ratio: 1/1; object-fit: cover;">
                </div>
                <!-- Loop Gallery -->
                @foreach($product['gallery'] as $img)
                    @php $galleryUrl = '/storage/uploads/products/' . $img['image_path']; @endphp
                    <div class="col-3">
                         <img src="{{ $galleryUrl }}" 
                              class="img-fluid rounded border cursor-pointer opacity-75 gallery-item" 
                              onclick="changeImage(this, '{{ $galleryUrl }}')" 
                              style="cursor: pointer; aspect-ratio: 1/1; object-fit: cover;">
                    </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Cột Thông Tin Sản Phẩm -->
        <div class="col-md-6">
            <h1 class="display-6 fw-bold text-dark">{{ $product['name'] }}</h1>
            
            <div class="d-flex align-items-center mb-3">
                <div class="text-warning me-2">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <span class="text-muted small">(Xem đánh giá)</span>
                <span class="mx-2 text-muted">|</span>
                <span class="text-success fw-bold">Còn hàng</span>
            </div>

            <!-- Giá bán -->
            <div class="mb-3">
                @if($product['price_sale'] > 0 && $product['price_sale'] < $product['price_regular'])
                    <h2 class="text-danger fw-bold d-inline me-2">{{ number_format($product['price_sale'], 0, ',', '.') }} đ</h2>
                    <del class="text-muted fs-5">{{ number_format($product['price_regular'], 0, ',', '.') }} đ</del>
                @else
                    <h2 class="text-danger fw-bold">{{ number_format($product['price_regular'], 0, ',', '.') }} đ</h2>
                @endif
            </div>
            
            <p class="text-muted mb-4">
                {{ $product['description'] ?? 'Sản phẩm chính hãng, chất lượng cao.' }}
            </p>

            <div class="card bg-light border-0 mb-4">
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Tình trạng: 
                            <span class="fw-bold">{{ isset($product['variants']) && count($product['variants']) > 0 ? 'Sẵn sàng giao hàng' : 'Liên hệ' }}</span>
                        </li>
                        <li class="mb-2"><i class="fas fa-tag text-secondary me-2"></i> Danh mục: 
                            <span class="fw-bold text-dark">{{ $product['category_name'] ?? 'Chưa cập nhật' }}</span>
                        </li>
                        <li class="mb-0"><i class="fas fa-barcode text-secondary me-2"></i> Mã SP: <strong>#{{ $product['id'] }}</strong></li>
                    </ul>
                </div>
            </div>

            <!-- Form Thêm vào giỏ hàng -->
            <form action="/cart/add" method="POST">
                <input type="hidden" name="product_id" value="{{ $product['id'] }}">

                <!-- Chọn Biến thể (Variants) -->
                @if(!empty($product['variants']) && count($product['variants']) > 0)
                <div class="mb-4">
                    <label class="fw-bold mb-2 d-block">Chọn phân loại:</label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($product['variants'] as $index => $variant)
                            <input type="radio" class="btn-check" name="variant_id" id="variant_{{ $variant['id'] }}" value="{{ $variant['id'] }}" autocomplete="off" {{ $index == 0 ? 'checked' : '' }}>
                            <label class="btn btn-outline-dark" for="variant_{{ $variant['id'] }}">
                                {{ $variant['attributes'] }} <br>
                                <small>{{ number_format($variant['price'], 0, ',', '.') }}đ</small>
                            </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <hr class="my-4">
                
                <!-- Chọn Số Lượng và Nút Mua -->
                <div class="d-flex align-items-center mb-4">
                    <div class="input-group me-3" style="width: 130px;">
                        <button class="btn btn-outline-secondary" type="button" onclick="if(document.getElementById('qty').value > 1) document.getElementById('qty').stepDown()">-</button>
                        <input type="number" class="form-control text-center border-secondary" name="quantity" id="qty" value="1" min="1">
                        <button class="btn btn-outline-secondary" type="button" onclick="document.getElementById('qty').stepUp()">+</button>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg flex-grow-1 shadow-sm py-2">
                        <i class="fas fa-shopping-cart me-2"></i> Thêm vào giỏ
                    </button>
                </div>
            </form>

            <div class="d-flex gap-2">
                <button class="btn btn-outline-danger btn-sm rounded-pill px-3"><i class="far fa-heart me-1"></i> Yêu thích</button>
                <button class="btn btn-outline-dark btn-sm rounded-pill px-3"><i class="fas fa-share-alt me-1"></i> Chia sẻ</button>
            </div>
        </div>
    </div>

    <!-- Tabs: Chi tiết & Đánh giá -->
    <div class="row mt-5">
        <div class="col-12">
            <ul class="nav nav-tabs" id="productTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc" type="button" role="tab">Mô tả chi tiết</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="review-tab" data-bs-toggle="tab" data-bs-target="#review" type="button" role="tab">Đánh giá khách hàng</button>
                </li>
            </ul>
            <div class="tab-content p-4 border border-top-0 rounded-bottom bg-white shadow-sm" id="productTabContent">
                <div class="tab-pane fade show active" id="desc" role="tabpanel">
                    <div class="content-body">
                        @if(isset($product['content']) && !empty($product['content']))
                            {!! $product['content'] !!}
                        @else
                            <p class="text-center text-muted py-3">Đang cập nhật nội dung chi tiết cho sản phẩm này.</p>
                        @endif
                    </div>
                </div>
                <div class="tab-pane fade" id="review" role="tabpanel">
                    <div class="text-center py-4">
                        <i class="far fa-comment-dots fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Chưa có đánh giá nào cho sản phẩm này.</p>
                        <button class="btn btn-primary">Viết đánh giá đầu tiên</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sản phẩm liên quan (Gợi ý) -->
    @if(!empty($relatedProducts) && count($relatedProducts) > 0)
    <div class="row mt-5">
        <h3 class="fw-bold mb-4 border-bottom pb-2">Sản phẩm liên quan</h3>
        @foreach($relatedProducts as $item)
        <div class="col-6 col-md-3 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <a href="/detail/index/{{ $item['id'] }}">
                    @php $rImg = !empty($item['img_thumbnail']) ? '/storage/uploads/products/' . $item['img_thumbnail'] : 'https://placehold.co/300x300'; @endphp
                    <img src="{{ $rImg }}" class="card-img-top" alt="{{ $item['name'] }}" style="aspect-ratio: 1/1; object-fit: cover;">
                </a>
                <div class="card-body">
                    <h5 class="card-title fs-6 text-truncate">
                        <a href="/detail/index/{{ $item['id'] }}" class="text-decoration-none text-dark">{{ $item['name'] }}</a>
                    </h5>
                    @if($item['price_sale'] > 0 && $item['price_sale'] < $item['price_regular'])
                        <span class="text-danger fw-bold me-2">{{ number_format($item['price_sale'], 0, ',', '.') }}đ</span>
                        <small class="text-decoration-line-through text-muted">{{ number_format($item['price_regular'], 0, ',', '.') }}đ</small>
                    @else
                        <span class="text-danger fw-bold">{{ number_format($item['price_regular'], 0, ',', '.') }}đ</span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<script>
    function changeImage(element, src) {
        document.getElementById('mainImage').src = src;
        // Reset opacity all items
        document.querySelectorAll('.gallery-item').forEach(el => {
            el.classList.remove('opacity-100');
            el.classList.add('opacity-75');
        });
        // Set opacity active item
        element.classList.remove('opacity-75');
        element.classList.add('opacity-100');
    }
</script>
@endsection