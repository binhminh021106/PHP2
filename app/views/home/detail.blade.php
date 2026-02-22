@extends('layouts.client')

@section('title', $title ?? 'Chi tiết sản phẩm')

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

    .product-detail-title {
        font-family: var(--font-heading);
        font-size: 2.5rem;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .product-detail-price {
        font-size: 1.5rem;
        font-weight: 500;
        margin-bottom: 20px;
    }

    .product-main-img {
        width: 100%;
        height: auto;
        object-fit: cover;
        aspect-ratio: 3/4;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        transition: opacity 0.3s ease;
    }

    /* Meta (Brand, Category) */
    .product-meta { 
        font-size: 0.85rem; 
        letter-spacing: 0.5px; 
        color: #777; 
        margin-bottom: 8px; 
        text-transform: uppercase; 
    }
    .product-meta a, .product-meta strong { 
        color: var(--color-dark); 
        text-decoration: none; 
    }

    /* Gallery Thumbs */
    .gallery-thumbs { 
        display: flex; 
        gap: 10px; 
        overflow-x: auto; 
        padding: 10px 0; 
        scrollbar-width: thin; 
    }
    .gallery-thumbs::-webkit-scrollbar { height: 6px; }
    .gallery-thumbs::-webkit-scrollbar-thumb { background: #ddd; border-radius: 10px; }
    .thumb-item { 
        width: 80px; 
        height: 106px; 
        flex-shrink: 0; 
        cursor: pointer; 
        border: 2px solid transparent; 
        opacity: 0.6; 
        transition: var(--transition); 
    }
    .thumb-item img { width: 100%; height: 100%; object-fit: cover; }
    .thumb-item:hover, .thumb-item.active { opacity: 1; border-color: var(--color-dark); }

    .variant-group { margin-bottom: 20px; }
    
    .variant-label {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 1px;
        margin-bottom: 10px;
        display: block;
    }

    .variant-options { display: flex; flex-wrap: wrap; gap: 10px; }
    .variant-options input[type="radio"] { display: none; }
    .variant-options label {
        display: inline-block;
        padding: 8px 16px;
        border: 1px solid #ddd;
        cursor: pointer;
        transition: var(--transition);
        font-size: 0.9rem;
    }

    .variant-options input[type="radio"]:checked + label {
        border-color: var(--color-dark);
        background: var(--color-dark);
        color: white;
    }

    .disabled-label {
        opacity: 0.3;
        pointer-events: none;
        text-decoration: line-through;
        background: #f8f9fa !important;
        border-color: #e9ecef !important;
        color: #6c757d !important;
    }

    .quantity-selector { display: inline-flex; border: 1px solid #ddd; align-items: center; }
    .quantity-selector button { background: transparent; border: none; padding: 10px 15px; cursor: pointer; font-size: 1rem; transition: var(--transition); }
    .quantity-selector button:hover { background: #f0f0f0; }
    .quantity-selector input { width: 50px; text-align: center; border: none; font-weight: 500; pointer-events: none; }
    .product-description { line-height: 1.8; color: #555; margin-bottom: 30px; padding-top: 20px; border-top: 1px solid #eee; }

    .section-title { font-family: var(--font-heading); font-size: 2.2rem; text-align: center; margin-bottom: 30px; }
    .product-card { background: transparent; border: none; margin-bottom: 30px; }
    .product-thumb { position: relative; overflow: hidden; aspect-ratio: 3/4; }
    .product-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .product-actions { position: absolute; bottom: 15px; left: 0; right: 0; text-align: center; opacity: 0; transform: translateY(20px); transition: var(--transition); }
    .product-card:hover .product-actions { opacity: 1; transform: translateY(0); }
    .action-btn { background: white; color: var(--color-dark); width: 45px; height: 45px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin: 0 5px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); transition: var(--transition); text-decoration: none; }
    .action-btn:hover { background: var(--color-dark); color: white; }
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
                <li class="breadcrumb-item"><a href="/shop" class="text-decoration-none text-dark">Sản phẩm</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product['name'] ?? '' }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-5">
        <div class="col-md-6">
            @php
                $mainImg = !empty($product['img_thumbnail']) 
                    ? '/storage/uploads/products/' . $product['img_thumbnail'] 
                    : 'https://placehold.co/800x1066?text=No+Image';
            @endphp
            
            <div class="mb-2">
                <img src="{{ $mainImg }}" id="main-product-image" data-original="{{ $mainImg }}" alt="{{ $product['name'] ?? 'Product' }}" class="product-main-img img-fluid">
            </div>

            <div class="gallery-thumbs">
                <div class="thumb-item active" onclick="changeGalleryImage('{{ $mainImg }}', this)">
                    <img src="{{ $mainImg }}" alt="Thumbnail">
                </div>
                
                @if(!empty($gallery))
                    @foreach($gallery as $gImage)
                        @php 
                            // Lấy tên ảnh từ cột image_path
                            $gPath = '/storage/uploads/gallery/' . $gImage['image_path']; 
                        @endphp
                        <div class="thumb-item" onclick="changeGalleryImage('{{ $gPath }}', this)">
                            <img src="{{ $gPath }}" alt="Gallery">
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="product-meta">
                <span>Danh mục: <a href="#">{{ $product['category_name'] ?? 'Chưa phân loại' }}</a></span> &nbsp;|&nbsp; 
                <span>Thương hiệu: <strong>{{ $product['brand_name'] ?? 'Đang cập nhật' }}</strong></span>
            </div>
            
            <h1 class="product-detail-title">{{ $product['name'] ?? '' }}</h1>
            
            <div class="product-detail-price" id="display-price">
                @if(isset($product['price_sale']) && $product['price_sale'] > 0 && $product['price_sale'] < $product['price_regular'])
                    <span class="text-danger">{{ number_format($product['price_sale'], 0, ',', '.') }}đ</span>
                    <del class="text-muted ms-2 fs-6 fw-normal">{{ number_format($product['price_regular'], 0, ',', '.') }}đ</del>
                @else
                    <span>{{ number_format($product['price_regular'] ?? 0, 0, ',', '.') }}đ</span>
                @endif
            </div>
            
            <p class="text-muted small mb-4">Tồn kho: <span id="display-stock" class="fw-bold text-danger">Vui lòng chọn Màu và Kích thước</span></p>

            <form action="/cart/add" method="POST" class="mt-4">
                <input type="hidden" name="product_id" value="{{ $product['id'] ?? '' }}">
                <input type="hidden" name="variant_id" id="selected_variant_id" value="">

                @if(!empty($colors))
                <div class="variant-group">
                    <span class="variant-label">Màu sắc</span>
                    <div class="variant-options">
                        @foreach($colors as $index => $color)
                            <input type="radio" class="color-select" name="color" id="color_{{ $index }}" value="{{ $color }}" required>
                            <label for="color_{{ $index }}">{{ $color }}</label>
                        @endforeach
                    </div>
                </div>
                @endif

                @if(!empty($sizes))
                <div class="variant-group">
                    <span class="variant-label">Kích thước</span>
                    <div class="variant-options">
                        @foreach($sizes as $index => $size)
                            <input type="radio" class="size-select" name="size" id="size_{{ $index }}" value="{{ $size }}" required>
                            <label for="size_{{ $index }}" id="label_size_{{ $index }}">{{ $size }}</label>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="d-flex align-items-center gap-3 mt-4 mb-4">
                    <div class="quantity-selector">
                        <button type="button" onclick="decreaseQty()">-</button>
                        <input type="text" name="quantity" id="qty-input" value="1" readonly>
                        <button type="button" onclick="increaseQty()">+</button>
                    </div>
                    <button type="submit" class="btn btn-dark flex-grow-1 py-3" id="btn-submit" disabled>
                        <i class="fas fa-shopping-cart me-2"></i> Chọn Màu / Size
                    </button>
                </div>
            </form>

            <div class="product-description">
                <?php echo isset($product['description']) ? $product['description'] : 'Chưa có mô tả cho sản phẩm này.'; ?>
            </div>
            
            <ul class="list-unstyled text-muted small mt-4 pt-3 border-top">
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Cam kết chính hãng 100%</li>
                <li class="mb-2"><i class="fas fa-truck text-success me-2"></i> Giao hàng toàn quốc từ 2-4 ngày</li>
                <li><i class="fas fa-sync text-success me-2"></i> Hỗ trợ đổi size trong vòng 7 ngày</li>
            </ul>
        </div>
    </div>
</div>

@if(!empty($relatedProducts) && count($relatedProducts) > 0)
<section class="py-5 bg-white border-top">
    <div class="container">
        <h2 class="section-title">Sản Phẩm Tương Tự</h2>
        <div class="row">
            @foreach($relatedProducts as $related)
            <div class="col-6 col-md-3">
                <div class="product-card">
                    <div class="product-thumb">
                        @php
                            $relImg = !empty($related['img_thumbnail']) 
                                ? '/storage/uploads/products/' . $related['img_thumbnail'] 
                                : 'https://placehold.co/400x533?text=No+Image';
                        @endphp
                        <img src="{{ $relImg }}" alt="{{ $related['name'] ?? '' }}">
                        
                        @if(isset($related['price_sale']) && $related['price_sale'] > 0)
                            <span class="position-absolute top-0 start-0 bg-dark text-white small px-2 py-1 m-2">SALE</span>
                        @endif

                        <div class="product-actions">
                            <a href="/home/detail/{{ $related['id'] ?? '' }}" class="action-btn" title="Xem nhanh">
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="#" class="action-btn" title="Yêu thích">
                                <i class="far fa-heart"></i>
                            </a>
                        </div>
                    </div>
                    <div class="product-info">
                        <div class="product-category">{{ $related['category_name'] ?? 'Fashion' }}</div>
                        <h3 class="product-title">
                            <a href="/home/detail/{{ $related['id'] ?? '' }}">{{ $related['name'] ?? '' }}</a>
                        </h3>
                        <div class="product-price">
                            @if(isset($related['price_sale']) && $related['price_sale'] > 0 && $related['price_sale'] < $related['price_regular'])
                                <span class="text-danger">{{ number_format($related['price_sale'], 0, ',', '.') }}đ</span>
                                <del class="text-muted small ms-2 fw-normal">{{ number_format($related['price_regular'], 0, ',', '.') }}đ</del>
                            @else
                                <span>{{ number_format($related['price_regular'] ?? 0, 0, ',', '.') }}đ</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<script>
    // Xử lý khi click vào ảnh Gallery
    window.changeGalleryImage = function(src, element) {
        const mainImgEl = document.getElementById('main-product-image');
        
        mainImgEl.style.opacity = 0.5;
        setTimeout(() => {
            mainImgEl.src = src;
            mainImgEl.style.opacity = 1;
        }, 150);

        document.querySelectorAll('.thumb-item').forEach(el => el.classList.remove('active'));
        if(element) element.classList.add('active');
    };

    document.addEventListener("DOMContentLoaded", function() {
        const variants = <?php echo isset($variantsJson) ? $variantsJson : '[]'; ?>;
        
        const colorInputs = document.querySelectorAll('.color-select');
        const sizeInputs = document.querySelectorAll('.size-select');
        const displayPrice = document.getElementById('display-price');
        const displayStock = document.getElementById('display-stock');
        const mainImgEl = document.getElementById('main-product-image');
        const btnSubmit = document.getElementById('btn-submit');
        const variantIdInput = document.getElementById('selected_variant_id');

        const formatMoney = (amount) => new Intl.NumberFormat('vi-VN').format(amount) + 'đ';

        colorInputs.forEach(input => {
            input.addEventListener('change', function() {
                const selectedColor = this.value;
                
                const availableSizes = variants
                    .filter(v => v.color === selectedColor)
                    .map(v => v.size);

                let firstValidSize = null;

                sizeInputs.forEach(sizeInput => {
                    const sizeLabel = document.querySelector(`label[for="${sizeInput.id}"]`);
                    
                    if (availableSizes.includes(sizeInput.value)) {
                        sizeLabel.classList.remove('disabled-label');
                        sizeInput.disabled = false;
                        if (!firstValidSize) firstValidSize = sizeInput;
                    } else {
                        sizeLabel.classList.add('disabled-label');
                        sizeInput.disabled = true;
                        sizeInput.checked = false;
                    }
                });

                const currentCheckedSize = document.querySelector('.size-select:checked');
                if (!currentCheckedSize && firstValidSize) {
                    firstValidSize.checked = true;
                }

                updateProductInfo();
            });
        });

        sizeInputs.forEach(input => {
            input.addEventListener('change', updateProductInfo);
        });

        function updateProductInfo() {
            const selectedColor = document.querySelector('.color-select:checked')?.value;
            const selectedSize = document.querySelector('.size-select:checked')?.value;

            if (!selectedColor || !selectedSize) return;

            const matchedVariant = variants.find(v => v.color === selectedColor && v.size === selectedSize);

            if (matchedVariant) {
                if (matchedVariant.price > 0) {
                    displayPrice.innerHTML = `<span class="text-danger fw-bold">${formatMoney(matchedVariant.price)}</span>`;
                }
                
                displayStock.className = "fw-bold text-success";
                displayStock.innerText = matchedVariant.stock + " sản phẩm";
                
                if (matchedVariant.image) {
                    mainImgEl.style.opacity = 0.5;
                    setTimeout(() => {
                        mainImgEl.src = '/storage/uploads/variants/' + matchedVariant.image;
                        mainImgEl.style.opacity = 1;
                    }, 150);
                    // Bỏ highlight ở phần gallery khi chọn biến thể có ảnh
                    document.querySelectorAll('.thumb-item').forEach(el => el.classList.remove('active'));
                } else {
                    mainImgEl.src = mainImgEl.getAttribute('data-original');
                }
                
                variantIdInput.value = matchedVariant.id;

                if (matchedVariant.stock > 0) {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="fas fa-shopping-cart me-2"></i> Thêm Vào Giỏ';
                } else {
                    btnSubmit.disabled = true;
                    btnSubmit.innerHTML = 'Hết hàng';
                    displayStock.className = "fw-bold text-danger";
                    displayStock.innerText = "0 sản phẩm (Hết hàng)";
                }

                document.getElementById('qty-input').value = 1;
            }
        }

        window.increaseQty = function() {
            let input = document.getElementById('qty-input');
            let maxStock = parseInt(displayStock.innerText) || 0; 
            let value = parseInt(input.value, 10);
            
            if (!document.querySelector('.color-select:checked') || !document.querySelector('.size-select:checked')) {
                alert("Vui lòng chọn Màu và Kích thước trước!");
                return;
            }

            if(value < maxStock) {
                input.value = value + 1;
            } else if (maxStock > 0) {
                alert("Số lượng bạn chọn vượt quá tồn kho hiện tại!");
            }
        };

        window.decreaseQty = function() {
            let input = document.getElementById('qty-input');
            let value = parseInt(input.value, 10);
            if (value > 1) {
                input.value = value - 1;
            }
        };
    });
</script>

@endsection