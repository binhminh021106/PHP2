@extends('layouts.client')

@section('title', $product['name'] ?? 'Chi tiết sản phẩm')

@section('content')
<div class="container py-5">

    <div class="row g-4">
        <!-- Cột Ảnh Sản Phẩm -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                {{-- Kiểm tra xem ảnh có tồn tại không, nếu không dùng ảnh placeholder --}}
                @if(isset($product['image']) && !empty($product['image']))
                    <img src="{{ BASE_URL . '/public/uploads/' . $product['image'] }}" 
                         class="card-img-top rounded" 
                         alt="{{ $product['name'] }}" 
                         style="max-height: 500px; object-fit: contain; background: #f8f9fa;"
                         onerror="this.src='https://placehold.co/600x600?text=No+Image'">
                @else
                    <img src="https://placehold.co/600x600?text=No+Image" class="card-img-top rounded" alt="No Image">
                @endif
            </div>
            
            <!-- Thumbnails (Ảnh nhỏ bên dưới) -->
            <div class="row mt-3 g-2">
                <div class="col-3">
                     <img src="https://placehold.co/150x150?text=Front" class="img-fluid rounded border cursor-pointer opacity-75 hover-opacity-100" alt="Thumb">
                </div>
                <div class="col-3">
                     <img src="https://placehold.co/150x150?text=Back" class="img-fluid rounded border cursor-pointer opacity-75 hover-opacity-100" alt="Thumb">
                </div>
                <div class="col-3">
                     <img src="https://placehold.co/150x150?text=Detail" class="img-fluid rounded border cursor-pointer opacity-75 hover-opacity-100" alt="Thumb">
                </div>
            </div>
        </div>

        <!-- Cột Thông Tin Sản Phẩm -->
        <div class="col-md-6">
            <h1 class="display-6 fw-bold text-dark">{{ $productDetail['name'] }}</h1>
            
            <div class="d-flex align-items-center mb-3">
                <div class="text-warning me-2">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <span class="text-muted small">(Xem 12 đánh giá)</span>
            </div>

            <h2 class="text-danger fw-bold mb-3">{{ number_format($productDetail['price'], 0, ',', '.') }} đ</h2>
            
            <p class="text-muted mb-4">
                {{ $productDetail['short_description'] ?? 'Sản phẩm chính hãng, chất lượng cao, bảo hành dài hạn.' }}
            </p>

            <div class="card bg-light border-0 mb-4">
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Tình trạng: <span class="fw-bold">Còn hàng</span></li>
                        <li class="mb-2"><i class="fas fa-tag text-secondary me-2"></i> Danh mục: <a href="#" class="text-decoration-none">{{ $category_name ?? 'Chưa cập nhật' }}</a></li>
                        <li class="mb-0"><i class="fas fa-industry text-secondary me-2"></i> Thương hiệu: <strong>{{ $brand_name ?? 'N/A' }}</strong></li>
                    </ul>
                </div>
            </div>

            <!-- Form Thêm vào giỏ hàng (Bao gồm chọn thuộc tính) -->
            <form action="{{ BASE_URL . '/cart/add' }}" method="POST">
                <input type="hidden" name="product_id" value="{{ $product['id'] }}">

                <!-- Chọn Màu Sắc -->
                <div class="mb-4">
                    <label class="fw-bold mb-2 d-block">Màu sắc:</label>
                    <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                        <!-- Màu Đen -->
                        <input type="radio" class="btn-check" name="color" id="color_black" value="black" autocomplete="off" checked>
                        <label class="btn btn-outline-dark px-3" for="color_black">Đen</label>

                        <!-- Màu Trắng -->
                        <input type="radio" class="btn-check" name="color" id="color_white" value="white" autocomplete="off">
                        <label class="btn btn-outline-dark px-3" for="color_white">Trắng</label>

                        <!-- Màu Xanh -->
                        <input type="radio" class="btn-check" name="color" id="color_blue" value="blue" autocomplete="off">
                        <label class="btn btn-outline-dark px-3" for="color_blue">Xanh Navy</label>
                    </div>
                </div>

                <!-- Chọn Kích Thước (Size) -->
                <div class="mb-4">
                    <label class="fw-bold mb-2 d-block">Kích thước:</label>
                    <div class="btn-group" role="group" aria-label="Size radio toggle button group">
                        <input type="radio" class="btn-check" name="size" id="size_s" value="S" autocomplete="off">
                        <label class="btn btn-outline-secondary px-3 py-2" for="size_s">S</label>

                        <input type="radio" class="btn-check" name="size" id="size_m" value="M" autocomplete="off" checked>
                        <label class="btn btn-outline-secondary px-3 py-2" for="size_m">M</label>

                        <input type="radio" class="btn-check" name="size" id="size_l" value="L" autocomplete="off">
                        <label class="btn btn-outline-secondary px-3 py-2" for="size_l">L</label>

                        <input type="radio" class="btn-check" name="size" id="size_xl" value="XL" autocomplete="off">
                        <label class="btn btn-outline-secondary px-3 py-2" for="size_xl">XL</label>
                    </div>
                    <div class="mt-2 text-muted small"><a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#sizeGuideModal"><i class="fas fa-ruler-horizontal me-1"></i>Hướng dẫn chọn size</a></div>
                </div>

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
                        @if(isset($product['description']) && !empty($product['description']))
                            {!! $product['description'] !!}
                        @else
                            <p>Đang cập nhật nội dung chi tiết cho sản phẩm này.</p>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
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
    <div class="row mt-5">
        <h3 class="fw-bold mb-4 border-bottom pb-2">Sản phẩm liên quan</h3>
        {{-- Ví dụ vòng lặp sản phẩm liên quan --}}
        @for($i = 1; $i <= 4; $i++)
        <div class="col-6 col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <img src="https://placehold.co/300x300?text=Related+{{$i}}" class="card-img-top" alt="Related">
                <div class="card-body">
                    <h5 class="card-title fs-6"><a href="#" class="text-decoration-none text-dark">Sản phẩm gợi ý {{ $i }}</a></h5>
                    <p class="card-text text-danger fw-bold">150.000 đ</p>
                </div>
            </div>
        </div>
        @endfor
    </div>
</div>

<!-- Modal Hướng dẫn chọn size (Optional) -->
<div class="modal fade" id="sizeGuideModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Bảng size tham khảo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>Size</th>
                    <th>Chiều cao (cm)</th>
                    <th>Cân nặng (kg)</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>S</td><td>150 - 160</td><td>45 - 55</td></tr>
                <tr><td>M</td><td>160 - 170</td><td>55 - 65</td></tr>
                <tr><td>L</td><td>170 - 180</td><td>65 - 75</td></tr>
                <tr><td>XL</td><td>180 - 190</td><td>75 - 85</td></tr>
            </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection