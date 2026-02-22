@extends('layouts.client')

@section('title', $product['name'] ?? 'Chi tiết sản phẩm')

@section('content')

<style>
    /* Kế thừa bộ biến màu sắc và font chữ từ trang chủ của bạn */
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

    .product-title {
        font-family: var(--font-heading);
        font-size: 2.5rem;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .price-block {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .price-sale {
        color: #d9534f; /* Màu đỏ nổi bật cho giá sale */
    }

    .price-regular {
        font-size: 1.1rem;
        color: #888;
        text-decoration: line-through;
        margin-left: 10px;
        font-weight: 400;
    }

    /* Style cho các nút bấm thuộc tính (Size, Color) */
    .attr-btn {
        border-radius: 0;
        padding: 8px 20px;
        margin-right: 10px;
        margin-bottom: 10px;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 1px;
        transition: var(--transition);
    }

    /* Style khung ảnh */
    .main-image-wrapper {
        position: relative;
        overflow: hidden;
        aspect-ratio: 3/4;
        background: #f8f9fa;
        margin-bottom: 15px;
    }

    .main-image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .gallery-thumb {
        width: 100%;
        aspect-ratio: 3/4;
        object-fit: cover;
        cursor: pointer;
        opacity: 0.6;
        transition: var(--transition);
        border: 1px solid transparent;
    }

    .gallery-thumb:hover, .gallery-thumb.active {
        opacity: 1;
        border-color: var(--color-dark);
    }

    /* Kế thừa Product Card từ trang chủ cho phần Sản phẩm liên quan */
    .product-card { background: transparent; border: none; margin-bottom: 30px; }
    .product-thumb { position: relative; overflow: hidden; aspect-ratio: 3/4; }
    .product-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .product-actions { position: absolute; bottom: 15px; left: 0; right: 0; text-align: center; opacity: 0; transform: translateY(20px); transition: var(--transition); }
    .product-card:hover .product-actions { opacity: 1; transform: translateY(0); }
    .action-btn { background: white; color: var(--color-dark); width: 45px; height: 45px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin: 0 5px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); transition: var(--transition); text-decoration: none; }
    .action-btn:hover { background: var(--color-dark); color: white; }
</style>

<!-- BREADCRUMB -->
<div class="bg-light py-3 border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/" class="text-dark text-decoration-none">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-dark text-decoration-none"><?= $product['category_name'] ?? 'Danh mục' ?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= $product['name'] ?></li>
            </ol>
        </nav>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row gx-5">
            <!-- CỘT TRÁI: HÌNH ẢNH -->
            <div class="col-md-6 mb-4 mb-md-0">
                <div class="main-image-wrapper">
                    <?php 
                        $mainImg = !empty($product['img_thumbnail']) ? '/storage/uploads/products/' . $product['img_thumbnail'] : 'https://placehold.co/600x800?text=No+Image';
                    ?>
                    <img id="mainImage" src="<?= $mainImg ?>" alt="<?= $product['name'] ?>">
                </div>
                
                <!-- Thư viện ảnh nhỏ -->
                @if(!empty($product['gallery']))
                <div class="row g-2 mt-2">
                    <!-- Ảnh thumbnail chính -->
                    <div class="col-3">
                        <img src="<?= $mainImg ?>" class="gallery-thumb active" onclick="changeMainImage(this.src)">
                    </div>
                    <!-- Các ảnh gallery -->
                    @foreach($product['gallery'] as $img)
                    <div class="col-3">
                        <img src="/storage/uploads/gallery/<?= $img['image_path'] ?>" class="gallery-thumb" onclick="changeMainImage(this.src)">
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- CỘT PHẢI: THÔNG TIN SẢN PHẨM -->
            <div class="col-md-6">
                <h1 class="product-title"><?= $product['name'] ?></h1>
                
                <div class="price-block">
                    <span id="productPrice" class="price-sale"><?= number_format($product['price_sale'], 0, ',', '.') ?> đ</span>
                    @if($product['price_regular'] > $product['price_sale'])
                        <span id="productRegularPrice" class="price-regular"><?= number_format($product['price_regular'], 0, ',', '.') ?> đ</span>
                    @endif
                </div>

                <div class="mb-4 text-muted">
                    <p><?= $product['description'] ?></p>
                </div>

                <!-- Khu vực render các nút chọn Size, Color bằng JS -->
                <div id="attributesContainer" class="mb-4"></div>

                <div class="mb-3">
                    <span class="text-muted small text-uppercase">Tình trạng: </span>
                    <span id="productQuantityText" class="fw-bold">Vui lòng chọn phân loại</span>
                </div>

                <!-- FORM ĐẶT HÀNG -->
                <form action="/cart/add" method="POST" class="mb-5 border-top pt-4">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <!-- JS sẽ tự động điền ID của biến thể vào đây -->
                    <input type="hidden" name="variant_id" id="selectedVariantId" value="">

                    <div class="d-flex align-items-center mb-4">
                        <span class="me-3 text-uppercase small fw-bold">Số lượng:</span>
                        <div class="input-group" style="width: 130px;">
                            <button class="btn btn-outline-dark rounded-0 px-3" type="button" onclick="decreaseQty()">-</button>
                            <input type="text" name="quantity" id="qtyInput" class="form-control text-center border-dark rounded-0" value="1" readonly>
                            <button class="btn btn-outline-dark rounded-0 px-3" type="button" onclick="increaseQty()">+</button>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" id="btnAddToCart" class="btn btn-dark btn-lg flex-grow-1" disabled>
                            <i class="fas fa-shopping-bag me-2"></i> THÊM VÀO GIỎ
                        </button>
                        <button type="button" class="btn btn-outline-dark btn-lg px-4">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>
                </form>

                <!-- Tab Chi tiết / Nội dung -->
                <ul class="nav nav-tabs rounded-0 mb-3" id="productTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active text-dark rounded-0 fw-bold text-uppercase" data-bs-toggle="tab" data-bs-target="#desc">Mô tả chi tiết</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-dark rounded-0 fw-bold text-uppercase" data-bs-toggle="tab" data-bs-target="#shipping">Giao hàng & Đổi trả</button>
                    </li>
                </ul>
                <div class="tab-content" id="productTabContent">
                    <div class="tab-pane fade show active text-muted" id="desc">
                        <?= $product['content'] ?>
                    </div>
                    <div class="tab-pane fade text-muted" id="shipping">
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-truck me-2"></i> Miễn phí giao hàng cho đơn từ 500k.</li>
                            <li class="mb-2"><i class="fas fa-box-open me-2"></i> Đổi trả dễ dàng trong vòng 30 ngày.</li>
                            <li><i class="fas fa-shield-alt me-2"></i> Đảm bảo hàng chính hãng 100%.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SẢN PHẨM LIÊN QUAN -->
@if(!empty($relatedProducts) && count($relatedProducts) > 0)
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5" style="font-family: var(--font-heading);">Có thể bạn sẽ thích</h2>
        <div class="row">
            @foreach($relatedProducts as $rel)
            <div class="col-6 col-md-3">
                <div class="product-card text-center">
                    <div class="product-thumb">
                        <?php $relImg = !empty($rel['img_thumbnail']) ? '/storage/uploads/products/' . $rel['img_thumbnail'] : 'https://placehold.co/400x533'; ?>
                        <img src="<?= $relImg ?>" alt="<?= $rel['name'] ?>">
                        <div class="product-actions">
                            <a href="/product/detail/<?= $rel['id'] ?>" class="action-btn" title="Xem nhanh"><i class="far fa-eye"></i></a>
                        </div>
                    </div>
                    <div class="product-info mt-3">
                        <h3 class="product-title" style="font-size: 1rem;"><a href="/product/detail/<?= $rel['id'] ?>" class="text-dark text-decoration-none"><?= $rel['name'] ?></a></h3>
                        <div class="fw-bold"><?= number_format($rel['price_sale'] ?: $rel['price_regular'], 0, ',', '.') ?> đ</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- JAVASCRIPT XỬ LÝ BIẾN THỂ (SIZE/COLOR) -->
<script>
    const variantsData = <?= json_encode($product['variants'] ?? []) ?>;
    const defaultImage = "<?= $mainImg ?>";
    
    let availableAttributes = {};
    let selectedAttributes = {};

    function initVariants() {
        if (variantsData && variantsData.length > 0) {
            extractAttributes();
            renderAttributesUI();
        } else {
            // Không có biến thể -> Sản phẩm đơn giản
            document.getElementById('productQuantityText').innerText = "Sẵn hàng (Sản phẩm tiêu chuẩn)";
            document.getElementById('productQuantityText').className = "text-success fw-bold";
            document.getElementById('btnAddToCart').disabled = false;
        }
    }

    // Trích xuất các thuộc tính (Color, Size) từ JSON của Database
    function extractAttributes() {
        variantsData.forEach(variant => {
            const attrs = variant.attributes; 
            for (const key in attrs) {
                if (!availableAttributes[key]) {
                    availableAttributes[key] = new Set();
                }
                availableAttributes[key].add(attrs[key]);
            }
        });

        // Set mặc định giá trị ban đầu là null
        for (const key in availableAttributes) {
            selectedAttributes[key] = null;
        }
    }

    // Vẽ giao diện các nút bấm (S, M, L, Xanh, Đỏ...)
    function renderAttributesUI() {
        const container = document.getElementById('attributesContainer');
        container.innerHTML = '';

        for (const attrName in availableAttributes) {
            const valuesArray = Array.from(availableAttributes[attrName]);
            
            let buttonsHTML = valuesArray.map(val => {
                // Mặc định là btn-outline-dark (chưa chọn)
                return `<button type="button" class="btn btn-outline-dark attr-btn" 
                            onclick="handleSelect('${attrName}', '${val}')" 
                            data-attr="${attrName}" data-val="${val}">
                            ${val}
                        </button>`;
            }).join('');

            container.innerHTML += `
                <div class="mb-3">
                    <div class="text-uppercase small fw-bold mb-2">${attrName}:</div>
                    <div class="d-flex flex-wrap">${buttonsHTML}</div>
                </div>
            `;
        }
    }

    // Xử lý khi user click vào 1 nút thuộc tính
    function handleSelect(attrName, attrValue) {
        selectedAttributes[attrName] = attrValue;
        
        // Cập nhật UI: Thêm class btn-dark cho nút được chọn, gỡ đi ở nút khác
        const buttons = document.querySelectorAll(`button[data-attr="${attrName}"]`);
        buttons.forEach(btn => {
            if (btn.getAttribute('data-val') === attrValue) {
                btn.classList.remove('btn-outline-dark');
                btn.classList.add('btn-dark'); // Màu đen của Bootstrap
            } else {
                btn.classList.remove('btn-dark');
                btn.classList.add('btn-outline-dark');
            }
        });

        checkMatchingVariant();
    }

    // Kiểm tra xem tổ hợp khách chọn có ra được 1 Biến thể cụ thể không
    function checkMatchingVariant() {
        const isAllSelected = Object.values(selectedAttributes).every(val => val !== null);
        if (!isAllSelected) return;

        const matched = variantsData.find(variant => {
            let isMatch = true;
            for (const key in selectedAttributes) {
                if (variant.attributes[key] !== selectedAttributes[key]) {
                    isMatch = false; break;
                }
            }
            return isMatch;
        });

        const btnAdd = document.getElementById('btnAddToCart');
        const qtyText = document.getElementById('productQuantityText');
        
        if (matched) {
            // Đổi giá
            document.getElementById('productPrice').innerText = new Intl.NumberFormat('vi-VN').format(matched.price) + ' đ';
            
            // Đổi ảnh nếu biến thể có ảnh riêng
            if (matched.image && matched.image.trim() !== '') {
                document.getElementById('mainImage').src = '/storage/uploads/variants/' + matched.image;
            } else {
                document.getElementById('mainImage').src = defaultImage;
            }

            // Lưu ID biến thể để chuẩn bị gửi Form
            document.getElementById('selectedVariantId').value = matched.id;

            // Kiểm tra kho
            if (matched.quantity > 0) {
                qtyText.innerText = "Còn hàng (" + matched.quantity + " sản phẩm)";
                qtyText.className = "text-success fw-bold";
                btnAdd.disabled = false;
            } else {
                qtyText.innerText = "Hết hàng";
                qtyText.className = "text-danger fw-bold";
                btnAdd.disabled = true;
            }
        } else {
            qtyText.innerText = "Phân loại không tồn tại";
            qtyText.className = "text-danger fw-bold";
            btnAdd.disabled = true;
        }
    }

    // Logic đổi ảnh main khi click vào ảnh thumbnail
    function changeMainImage(src) {
        document.getElementById('mainImage').src = src;
        // Bỏ active cũ, thêm active mới
        document.querySelectorAll('.gallery-thumb').forEach(el => el.classList.remove('active'));
        event.target.classList.add('active');
    }

    // Logic Tăng/Giảm số lượng
    function increaseQty() {
        let input = document.getElementById('qtyInput');
        input.value = parseInt(input.value) + 1;
    }
    function decreaseQty() {
        let input = document.getElementById('qtyInput');
        if (parseInt(input.value) > 1) {
            input.value = parseInt(input.value) - 1;
        }
    }

    // Chạy khi load trang
    document.addEventListener("DOMContentLoaded", initVariants);
</script>

@endsection