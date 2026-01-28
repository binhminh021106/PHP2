@extends('layouts.client')

@section('title', 'Trang Chủ - MyShop')

@section('content')
<div class="row g-4">

    <!-- Sidebar Categories (Bên trái) -->
    <aside class="col-12 col-lg-3">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold text-uppercase text-secondary">Danh mục</div>
            <div class="list-group list-group-flush">
                <a href="#" class="list-group-item list-group-item-action active fw-bold">Tất cả sản phẩm</a>
                <a href="#" class="list-group-item list-group-item-action">Điện thoại</a>
                <a href="#" class="list-group-item list-group-item-action">Laptop</a>
                <a href="#" class="list-group-item list-group-item-action">Phụ kiện</a>
                <a href="#" class="list-group-item list-group-item-action">Đồ gia dụng</a>
            </div>
        </div>

        <div class="card shadow-sm mt-3 border-0">
            <div class="card-body">
                <div class="fw-bold mb-2 text-uppercase text-secondary">Khoảng giá</div>
                <div class="d-flex gap-2 align-items-center">
                    <input type="number" class="form-control form-control-sm" placeholder="Min" min="0">
                    <span>-</span>
                    <input type="number" class="form-control form-control-sm" placeholder="Max" min="0">
                </div>
                <button class="btn btn-primary w-100 mt-3 btn-sm">Áp dụng</button>
            </div>
        </div>
    </aside>

    <!-- Khu vực Sản phẩm (Bên phải) -->
    <section class="col-12 col-lg-9">
        <div class="d-flex align-items-center justify-content-between mb-4 bg-white p-3 rounded shadow-sm">
            <h1 class="h5 mb-0 fw-bold text-primary">Sản phẩm nổi bật</h1>

            <div class="d-flex gap-2">
                <select class="form-select form-select-sm" style="width: 180px;">
                    <option selected>Sắp xếp: Mặc định</option>
                    <option>Giá: Thấp đến Cao</option>
                    <option>Giá: Cao đến Thấp</option>
                    <option>Mới nhất</option>
                </select>
                <button class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-filter me-1"></i> Lọc</button>
            </div>
        </div>

        <!-- Grid Sản phẩm -->
        <div class="row g-3">

            {{-- Dữ liệu giả lập (Sau này sẽ lấy từ Controller) --}}
            @php
            $dummyProducts = [
                ['name' => 'iPhone 15 Pro Max', 'price' => '29.990.000đ', 'cat' => 'Phones', 'color' => 'primary'],
                ['name' => 'MacBook Air M2', 'price' => '24.500.000đ', 'cat' => 'Laptops', 'color' => 'success'],
                ['name' => 'Tai nghe Sony WH-1000XM5', 'price' => '6.490.000đ', 'cat' => 'Accessories', 'color' => 'warning'],
                ['name' => 'Samsung Galaxy S24', 'price' => '18.990.000đ', 'cat' => 'Phones', 'color' => 'primary'],
                ['name' => 'Chuột Logitech MX Master 3S', 'price' => '2.100.000đ', 'cat' => 'Accessories', 'color' => 'warning'],
                ['name' => 'Dell XPS 13', 'price' => '32.000.000đ', 'cat' => 'Laptops', 'color' => 'success'],
            ];
            @endphp

            @foreach($dummyProducts as $index => $product)
            <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                <div class="card h-100 shadow-sm border-0 product-card">
                    <div class="position-relative">
                        <img src="https://picsum.photos/600/400?random={{ $index }}" class="card-img-top object-fit-cover" height="200" alt="Product">
                        <span class="position-absolute top-0 end-0 badge bg-{{ $product['color'] }} m-2">{{ $product['cat'] }}</span>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title fw-bold text-truncate" title="{{ $product['name'] }}">
                            <a href="#" class="text-decoration-none text-dark">{{ $product['name'] }}</a>
                        </h6>
                        <p class="card-text text-muted small mb-2">Mô tả ngắn gọn về sản phẩm...</p>
                        
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-bold text-danger fs-5">{{ $product['price'] }}</span>
                                <small class="text-decoration-line-through text-muted small">35.000.000đ</small>
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-cart-plus me-1"></i> Thêm giỏ hàng</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

        </div>

        <!-- Phân trang -->
        <nav class="mt-5 d-flex justify-content-center">
            <ul class="pagination">
                <li class="page-item disabled"><a class="page-link" href="#"><i class="fa-solid fa-chevron-left"></i></a></li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#"><i class="fa-solid fa-chevron-right"></i></a></li>
            </ul>
        </nav>
    </section>

</div>
@endsection