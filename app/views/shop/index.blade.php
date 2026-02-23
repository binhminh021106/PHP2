@extends('layouts.client')

@section('title', $title ?? 'Cửa hàng')

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
    
    /* Layout styling */
    .page-header { background: #f8f9fa; padding: 40px 0; margin-bottom: 40px; border-bottom: 1px solid #eee; }
    .page-title { font-family: var(--font-heading); font-size: 2.5rem; font-weight: 600; margin: 0; }

    /* Sidebar Filter Styling */
    .sidebar-widget { background: #fff; border: 1px solid #eee; padding: 25px; margin-bottom: 30px; }
    .widget-title { font-family: var(--font-heading); font-size: 1.2rem; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px; position: relative; }
    .widget-title::after { content: ''; position: absolute; left: 0; bottom: -2px; width: 50px; height: 2px; background: var(--color-dark); }
    
    .category-list { list-style: none; padding: 0; margin: 0; }
    .category-list li { margin-bottom: 12px; }
    .category-list a { color: #555; text-decoration: none; transition: var(--transition); display: flex; justify-content: space-between; }
    .category-list a:hover, .category-list a.active { color: var(--color-accent); padding-left: 5px; }

    /* Form Inputs */
    .form-control, .form-select { border-radius: 0; border: 1px solid #ddd; padding: 10px 15px; font-size: 0.9rem; }
    .form-control:focus, .form-select:focus { border-color: var(--color-dark); box-shadow: none; }
    .btn-dark { background: var(--color-dark); border: none; border-radius: 0; padding: 10px 20px; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; transition: var(--transition); }
    .btn-dark:hover { background: var(--color-accent); }

    /* Product Card */
    .product-card { background: transparent; border: none; margin-bottom: 30px; }
    .product-thumb { position: relative; overflow: hidden; aspect-ratio: 3/4; }
    .product-thumb img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.8s ease; }
    .product-card:hover .product-thumb img { transform: scale(1.05); }
    .product-actions { position: absolute; bottom: 15px; left: 0; right: 0; text-align: center; opacity: 0; transform: translateY(20px); transition: var(--transition); }
    .product-card:hover .product-actions { opacity: 1; transform: translateY(0); }
    .action-btn { background: white; color: var(--color-dark); width: 45px; height: 45px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin: 0 5px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); transition: var(--transition); text-decoration: none; }
    .action-btn:hover { background: var(--color-dark); color: white; }
    
    .product-info { padding-top: 15px; text-align: center; }
    .product-category { font-size: 0.75rem; text-transform: uppercase; color: #888; letter-spacing: 1px; }
    .product-title { font-family: var(--font-base); font-size: 1rem; font-weight: 500; margin: 5px 0; }
    .product-title a { color: var(--color-dark); text-decoration: none; transition: 0.3s; }
    .product-title a:hover { color: var(--color-accent); }
    .product-price { font-weight: 600; font-size: 1rem; }

    /* Pagination */
    .pagination { justify-content: center; margin-top: 20px; }
    .page-link { color: var(--color-dark); border: 1px solid #eee; margin: 0 5px; border-radius: 0 !important; padding: 8px 16px; transition: var(--transition); }
    .page-item.active .page-link { background-color: var(--color-dark); border-color: var(--color-dark); color: white; }
    .page-link:hover { background-color: var(--color-accent); border-color: var(--color-accent); color: white; }

    /* Floating Compare Button */
    .floating-compare { position: fixed; bottom: 30px; right: 30px; background: var(--color-dark); color: white; padding: 15px 25px; border-radius: 50px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); z-index: 999; text-decoration: none; display: flex; align-items: center; gap: 10px; transition: var(--transition); }
    .floating-compare:hover { background: var(--color-accent); color: white; transform: translateY(-5px); }
    .compare-badge { background: white; color: var(--color-dark); width: 25px; height: 25px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: bold; }
</style>

<!-- Banner Header -->
<div class="page-header text-center">
    <div class="container">
        <h1 class="page-title">MENSWEAR SHOP</h1>
        <nav aria-label="breadcrumb" class="mt-2">
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="/" class="text-dark text-decoration-none">Trang chủ</a></li>
                <li class="breadcrumb-item active">Cửa hàng</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-4">
        <!-- SIDEBAR BỘ LỌC -->
        <div class="col-lg-3">
            <form action="/shop" method="GET" id="filterForm">
                
                <!-- Giữ lại tham số sắp xếp nếu có -->
                <input type="hidden" name="sort" value="{{ $filters['sort'] ?? 'newest' }}">

                <!-- Tìm kiếm -->
                <div class="sidebar-widget">
                    <h4 class="widget-title">Tìm kiếm</h4>
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Nhập tên sản phẩm..." value="{{ $filters['search'] ?? '' }}">
                        <button class="btn btn-dark" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </div>

                <!-- Danh mục -->
                <div class="sidebar-widget">
                    <h4 class="widget-title">Danh mục</h4>
                    <ul class="category-list">
                        <li>
                            <a href="/shop" class="{{ empty($filters['category']) ? 'active fw-bold' : '' }}">Tất cả sản phẩm</a>
                        </li>
                        @if(!empty($categories))
                            @foreach($categories as $cat)
                                <li>
                                    <!-- ĐÃ FIX: Dùng cú pháp PHP thuần để tránh lỗi biên dịch của Template Engine -->
                                    <a href="#" onclick="document.getElementById('catInput').value='<?php echo $cat['id']; ?>'; document.getElementById('filterForm').submit(); return false;" 
                                       class="<?php echo (isset($filters['category']) && $filters['category'] == $cat['id']) ? 'active fw-bold' : ''; ?>">
                                        <?php echo $cat['name']; ?>
                                    </a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                    <input type="hidden" name="category" id="catInput" value="{{ $filters['category'] ?? '' }}">
                </div>

                <!-- Lọc Giá -->
                <div class="sidebar-widget">
                    <h4 class="widget-title">Mức giá</h4>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <input type="number" class="form-control" name="min_price" placeholder="Từ..." value="{{ $filters['min_price'] ?? '' }}">
                        </div>
                        <div class="col-6">
                            <input type="number" class="form-control" name="max_price" placeholder="Đến..." value="{{ $filters['max_price'] ?? '' }}">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-dark w-100">ÁP DỤNG LỌC</button>
                    
                    @if(array_filter($filters))
                        <a href="/shop" class="btn btn-outline-dark w-100 mt-2" style="font-size: 0.75rem;">Xóa bộ lọc</a>
                    @endif
                </div>

            </form>
        </div>

        <!-- DANH SÁCH SẢN PHẨM -->
        <div class="col-lg-9">
            
            <!-- Top Bar: Kết quả & Sắp xếp -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom gap-3">
                <p class="mb-0 text-muted">Hiển thị <span class="fw-bold text-dark">{{ count($products) }}</span> trên tổng <span class="fw-bold text-dark">{{ $totalProducts }}</span> sản phẩm</p>
                
                <div class="d-flex align-items-center gap-3">
                    <!-- Nút Chuyển qua trang So sánh -->
                    <a href="/shop/compare" class="btn btn-outline-dark d-flex align-items-center" style="padding: 8px 15px; font-size: 0.85rem;">
                        <i class="fas fa-balance-scale me-2"></i> So sánh 
                        <span class="badge bg-dark text-white ms-2">{{ isset($_SESSION['compare']) ? count($_SESSION['compare']) : 0 }}</span>
                    </a>

                    <div class="d-flex align-items-center">
                        <label class="me-2 text-muted text-nowrap mb-0" style="font-size: 0.9rem;">Sắp xếp:</label>
                        <select class="form-select w-auto" form="filterForm" name="sort" onchange="document.getElementById('filterForm').submit()" style="border-radius: 0;">
                            <option value="newest" {{ (isset($filters['sort']) && $filters['sort'] == 'newest') ? 'selected' : '' }}>Mới nhất</option>
                            <option value="price_asc" {{ (isset($filters['sort']) && $filters['sort'] == 'price_asc') ? 'selected' : '' }}>Giá: Thấp đến Cao</option>
                            <option value="price_desc" {{ (isset($filters['sort']) && $filters['sort'] == 'price_desc') ? 'selected' : '' }}>Giá: Cao xuống Thấp</option>
                            <option value="name_asc" {{ (isset($filters['sort']) && $filters['sort'] == 'name_asc') ? 'selected' : '' }}>Tên: A - Z</option>
                            <option value="name_desc" {{ (isset($filters['sort']) && $filters['sort'] == 'name_desc') ? 'selected' : '' }}>Tên: Z - A</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Grid Sản phẩm -->
            @if(empty($products))
                <div class="text-center py-5 my-5">
                    <i class="fas fa-box-open fa-4x text-muted mb-3 opacity-50"></i>
                    <h4>Không tìm thấy sản phẩm nào!</h4>
                    <p class="text-muted">Vui lòng thử điều chỉnh lại bộ lọc tìm kiếm.</p>
                </div>
            @else
                <div class="row g-4">
                    @foreach($products as $product)
                    <div class="col-sm-6 col-md-4">
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
                                    <a href="/wishlist/add/{{ $product['id'] }}" class="action-btn" title="Yêu thích">
                                        <i class="far fa-heart"></i>
                                    </a>
                                    <a href="/home/detail/{{ $product['id'] }}" class="action-btn" title="Xem chi tiết">
                                        <i class="far fa-eye"></i>
                                    </a>
                                    <!-- Nút thêm vào danh sách so sánh -->
                                    <a href="/shop/addCompare/{{ $product['id'] }}" class="action-btn" title="So sánh">
                                        <i class="fas fa-exchange-alt"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="product-info">
                                <div class="product-category">{{ $product['category_name'] ?? 'Fashion' }}</div>
                                <h3 class="product-title">
                                    <a href="/home/detail/{{ $product['id'] }}">{{ $product['name'] }}</a>
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
                </div>

                <!-- Phân trang -->
                @if($totalPages > 1)
                <nav class="mt-5">
                    <ul class="pagination">
                        <!-- Tạo query string giữ nguyên filter -->
                        @php
                            $queryString = http_build_query(array_filter($filters));
                            $prefix = $queryString ? '&' . $queryString : '';
                        @endphp

                        @if($page > 1)
                        <li class="page-item"><a class="page-link" href="?page={{ $page - 1 }}{{ $prefix }}"><i class="fas fa-angle-left"></i></a></li>
                        @endif

                        @for($i = 1; $i <= $totalPages; $i++)
                        <li class="page-item {{ $i == $page ? 'active' : '' }}">
                            <a class="page-link" href="?page={{ $i }}{{ $prefix }}">{{ $i }}</a>
                        </li>
                        @endfor

                        @if($page < $totalPages)
                        <li class="page-item"><a class="page-link" href="?page={{ $page + 1 }}{{ $prefix }}"><i class="fas fa-angle-right"></i></a></li>
                        @endif
                    </ul>
                </nav>
                @endif
            @endif

        </div>
    </div>
</div>

<!-- Nút Nổi So Sánh (Chỉ hiện khi có SP trong session) -->
@if(!empty($_SESSION['compare']))
<a href="/shop/compare" class="floating-compare">
    <i class="fas fa-balance-scale"></i> So Sánh Sản Phẩm
    <span class="compare-badge">{{ count($_SESSION['compare']) }}</span>
</a>
@endif

<!-- SweetAlert2 Toast -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const Toast = Swal.mixin({
            toast: true, position: 'top-end', showConfirmButton: false,
            timer: 3000, timerProgressBar: true
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