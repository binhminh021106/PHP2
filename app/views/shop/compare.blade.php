@extends('layouts.client')

@section('title', $title ?? 'So sánh sản phẩm')

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
    
    .page-header { background: #f8f9fa; padding: 40px 0; margin-bottom: 40px; border-bottom: 1px solid #eee; text-align: center; }
    .page-title { font-family: var(--font-heading); font-size: 2.5rem; font-weight: 600; margin: 0; }

    .btn-dark { background: var(--color-dark); border: none; border-radius: 0; padding: 10px 20px; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; transition: var(--transition); }
    .btn-dark:hover { background: var(--color-accent); color: white; }

    /* Bảng So sánh */
    .compare-table { background: #fff; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #eee; }
    .compare-table th { background: #fcfcfc; font-family: var(--font-heading); font-size: 1.1rem; vertical-align: middle; width: 15%; padding: 20px; color: #555;}
    .compare-table td { padding: 20px; vertical-align: top; width: 21.25%; border-left: 1px solid #eee; }
    
    .compare-img { width: 100%; max-width: 250px; aspect-ratio: 3/4; object-fit: cover; margin-bottom: 15px; border: 1px solid #f0f0f0; }
    .compare-title { font-size: 1.1rem; font-weight: 600; color: var(--color-dark); text-decoration: none; transition: var(--transition); display: block; margin-bottom: 10px;}
    .compare-title:hover { color: var(--color-accent); }
    
    .compare-price { font-size: 1.25rem; font-weight: 600; color: var(--color-dark); }
    
    .btn-remove { position: absolute; top: 10px; right: 10px; background: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #dc3545; box-shadow: 0 2px 5px rgba(0,0,0,0.2); text-decoration: none; transition: var(--transition); z-index: 10; }
    .btn-remove:hover { background: #dc3545; color: white; transform: scale(1.1); }
</style>

<div class="page-header">
    <div class="container">
        <h1 class="page-title">SO SÁNH SẢN PHẨM</h1>
        <nav aria-label="breadcrumb" class="mt-2">
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="/" class="text-dark text-decoration-none">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="/shop" class="text-dark text-decoration-none">Cửa hàng</a></li>
                <li class="breadcrumb-item active">So sánh</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5 pb-5">
    @if(empty($products))
        <div class="text-center py-5">
            <i class="fas fa-balance-scale fa-4x text-muted mb-3 opacity-50"></i>
            <h3>Chưa có sản phẩm nào để so sánh</h3>
            <p class="text-muted mb-4">Vui lòng quay lại trang Cửa hàng để thêm ít nhất 2 sản phẩm vào danh sách so sánh.</p>
            <a href="/shop" class="btn btn-dark">Đến Cửa Hàng</a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered compare-table">
                <tbody>
                    <!-- Hình ảnh & Tiêu đề -->
                    <tr>
                        <th>Sản phẩm</th>
                        @foreach($products as $product)
                        <td class="text-center position-relative">
                            <a href="/shop/removeCompare/{{ $product['id'] }}" class="btn-remove" title="Xóa" onclick="return confirm('Xóa khỏi danh sách so sánh?');">
                                <i class="fas fa-times"></i>
                            </a>
                            @php
                                $img = !empty($product['img_thumbnail']) ? '/storage/uploads/products/' . $product['img_thumbnail'] : 'https://placehold.co/400x533';
                            @endphp
                            <a href="/home/detail/{{ $product['id'] }}">
                                <img src="{{ $img }}" alt="{{ $product['name'] }}" class="compare-img">
                            </a>
                            <a href="/home/detail/{{ $product['id'] }}" class="compare-title">{{ $product['name'] }}</a>
                            
                            <a href="/home/detail/{{ $product['id'] }}" class="btn btn-dark w-100 mt-2">MUA NGAY</a>
                        </td>
                        @endforeach
                    </tr>

                    <!-- Giá tiền -->
                    <tr>
                        <th>Mức giá</th>
                        @foreach($products as $product)
                        <td class="text-center">
                            @if($product['price_sale'] > 0 && $product['price_sale'] < $product['price_regular'])
                                <div class="compare-price text-danger">{{ number_format($product['price_sale'], 0, ',', '.') }}đ</div>
                                <del class="text-muted small">{{ number_format($product['price_regular'], 0, ',', '.') }}đ</del>
                            @else
                                <div class="compare-price">{{ number_format($product['price_regular'], 0, ',', '.') }}đ</div>
                            @endif
                        </td>
                        @endforeach
                    </tr>

                    <!-- Thương hiệu -->
                    <tr>
                        <th>Thương hiệu</th>
                        @foreach($products as $product)
                        <td class="text-center fw-medium">{{ $product['brand_name'] ?? 'Đang cập nhật' }}</td>
                        @endforeach
                    </tr>

                    <!-- Danh mục -->
                    <tr>
                        <th>Danh mục</th>
                        @foreach($products as $product)
                        <td class="text-center text-muted">{{ $product['category_name'] ?? 'Thời trang' }}</td>
                        @endforeach
                    </tr>

                    <!-- Tình trạng -->
                    <tr>
                        <th>Tình trạng</th>
                        @foreach($products as $product)
                        <td class="text-center">
                            @if($product['status'] == 'active')
                                <span class="badge bg-success">Còn hàng</span>
                            @else
                                <span class="badge bg-danger">Hết hàng</span>
                            @endif
                        </td>
                        @endforeach
                    </tr>

                    <!-- Mô tả ngắn -->
                    <tr>
                        <th>Mô tả</th>
                        @foreach($products as $product)
                        <td class="text-muted" style="font-size: 0.9rem;">
                            <!-- Loại bỏ các thẻ HTML dư thừa khi preview -->
                            {{ mb_strimwidth(strip_tags($product['description']), 0, 150, '...') }}
                        </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- SweetAlert2 Toast -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const Toast = Swal.mixin({
            toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true
        });

        <?php if (!empty($successMsg)): ?>
            Toast.fire({ icon: 'success', title: '<?php echo addslashes($successMsg); ?>' });
        <?php endif; ?>
        <?php if (!empty($errorMsg)): ?>
            Toast.fire({ icon: 'error', title: '<?php echo addslashes($errorMsg); ?>' });
        <?php endif; ?>
    });
</script>

@endsection