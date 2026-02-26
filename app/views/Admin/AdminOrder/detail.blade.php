@extends('layouts.admin')

@section('title', $title ?? 'Chi tiết đơn hàng')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Breadcrumb & Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mt-0 mb-2 font-heading" style="font-family: var(--font-heading); font-weight: 600;">Chi Tiết Đơn Hàng #{{ $order['id'] }}</h1>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/adminorder/index" class="text-decoration-none text-muted">Đơn hàng</a></li>
                <li class="breadcrumb-item active">Chi tiết</li>
            </ol>
        </div>
        <a href="/adminorder/index" class="btn btn-outline-dark shadow-sm px-4" style="text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">
            <i class="fa-solid fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>

    <!-- Thông báo -->
    @if(!empty($successMsg))
        <div class="alert alert-success alert-dismissible fade show rounded-0 border-0 shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-check-circle me-2"></i> {{ $successMsg }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(!empty($errorMsg))
        <div class="alert alert-danger alert-dismissible fade show rounded-0 border-0 shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i> {{ $errorMsg }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- CỘT TRÁI: THÔNG TIN KHÁCH HÀNG & TRẠNG THÁI -->
        <div class="col-lg-4">
            
            <!-- Xử lý trạng thái -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-dark text-white py-3 fw-bold text-uppercase" style="font-size: 0.9rem; letter-spacing: 1px;">
                    <i class="fa-solid fa-arrows-rotate me-2"></i> Xử lý Đơn Hàng
                </div>
                <div class="card-body p-4 bg-light">
                    <form action="/adminorder/updateStatus" method="POST">
                        <input type="hidden" name="order_id" value="{{ $order['id'] }}">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Trạng thái hiện tại:</label>
                            <select name="status" class="form-select rounded-0 py-2" style="font-weight: 500;">
                                <option value="pending" {{ $order['status'] == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                                <option value="processing" {{ $order['status'] == 'processing' ? 'selected' : '' }}>Đang đóng gói/Xử lý</option>
                                <option value="shipped" {{ $order['status'] == 'shipped' ? 'selected' : '' }}>Đang giao hàng</option>
                                <option value="delivered" {{ $order['status'] == 'delivered' ? 'selected' : '' }}>Đã giao thành công</option>
                                <option value="cancelled" {{ $order['status'] == 'cancelled' ? 'selected' : '' }}>Hủy đơn hàng</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-warning w-100 fw-bold rounded-0 py-2" style="letter-spacing: 1px; color: #111;">
                            <i class="fa-solid fa-save me-1"></i> CẬP NHẬT TRẠNG THÁI
                        </button>
                    </form>
                </div>
            </div>

            <!-- Thông tin người nhận -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 fw-bold text-uppercase" style="font-size: 0.9rem; letter-spacing: 1px;">
                    <i class="fa-solid fa-address-card me-2 text-muted"></i> Thông tin khách hàng
                </div>
                <div class="card-body p-4">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3 d-flex justify-content-between border-bottom pb-2">
                            <span class="text-muted small text-uppercase">Tài khoản ID</span>
                            <span class="fw-bold">#{{ $order['user_id'] }}</span>
                        </li>
                        <li class="mb-3 d-flex justify-content-between border-bottom pb-2">
                            <span class="text-muted small text-uppercase">Người nhận</span>
                            <span class="fw-bold text-dark">{{ $order['fullname'] }}</span>
                        </li>
                        <li class="mb-3 d-flex justify-content-between border-bottom pb-2">
                            <span class="text-muted small text-uppercase">Điện thoại</span>
                            <span class="fw-bold">{{ $order['phone'] }}</span>
                        </li>
                        <li class="mb-3 d-flex justify-content-between border-bottom pb-2">
                            <span class="text-muted small text-uppercase">Email</span>
                            <span>{{ $order['email'] ?? 'Không có' }}</span>
                        </li>
                        <li class="mb-3">
                            <span class="text-muted small text-uppercase d-block mb-1">Địa chỉ giao hàng</span>
                            <span class="d-block" style="line-height: 1.5;">{{ $order['address'] }}</span>
                        </li>
                        <li class="mb-3 border-top pt-3">
                            <span class="text-muted small text-uppercase d-block mb-1">Phương thức thanh toán</span>
                            <span class="badge bg-light text-dark border p-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                {{ $order['payment_method'] == 'cod' ? 'Thanh toán khi nhận hàng (COD)' : 'Chuyển khoản ngân hàng' }}
                            </span>
                        </li>
                        <li class="mb-0">
                            <span class="text-muted small text-uppercase d-block mb-1">Thời gian đặt</span>
                            <span>{{ date('H:i:s - d/m/Y', strtotime($order['created_at'])) }}</span>
                        </li>
                    </ul>

                    @if(!empty($order['note']))
                        <div class="mt-4 bg-light p-3 border-start border-4 border-warning">
                            <span class="text-muted small text-uppercase fw-bold d-block mb-1">Ghi chú của khách:</span>
                            <p class="mb-0 text-dark fst-italic">"{{ $order['note'] }}"</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI: CHI TIẾT SẢN PHẨM -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 fw-bold text-uppercase d-flex align-items-center justify-content-between" style="font-size: 0.9rem; letter-spacing: 1px;">
                    <span><i class="fa-solid fa-box-open me-2 text-muted"></i> Sản phẩm đã đặt</span>
                    <span class="badge bg-dark rounded-pill">{{ count($orderItems) }} sản phẩm</span>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-center">
                            <thead class="bg-light" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                <tr>
                                    <th width="12%" class="py-3 text-muted">Ảnh</th>
                                    <th width="43%" class="py-3 text-muted text-start">Sản phẩm</th>
                                    <th width="15%" class="py-3 text-muted">Đơn giá</th>
                                    <th width="10%" class="py-3 text-muted">SL</th>
                                    <th width="20%" class="py-3 text-muted text-end pe-4">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orderItems as $item)
                                <tr>
                                    <td class="py-3">
                                        @php 
                                            // Sử dụng Fallback hình ảnh thông minh bằng Javascript
                                            $imgName = $item['image'] ?? '';
                                            $variantImg = !empty($imgName) ? '/storage/uploads/variants/' . $imgName : 'https://placehold.co/70x95?text=No+Image';
                                            $productImg = !empty($imgName) ? '/storage/uploads/products/' . $imgName : 'https://placehold.co/70x95?text=No+Image';
                                        @endphp
                                        <div class="d-inline-block border shadow-sm p-1 bg-white">
                                            <img src="{{ $variantImg }}" 
                                                 onerror="this.onerror=null; this.src='{{ $productImg }}';" 
                                                 alt="{{ $item['product_name'] ?? 'Product Image' }}" 
                                                 style="width: 60px; height: 80px; object-fit: cover;">
                                        </div>
                                    </td>
                                    
                                    <td class="text-start py-3">
                                        <a href="/home/detail/{{ $item['product_id'] }}" target="_blank" class="text-decoration-none fw-bold text-dark d-block mb-1" style="font-family: var(--font-base); font-size: 1rem;">
                                            {{ $item['product_name'] }}
                                        </a>
                                        
                                        <!-- HIỂN THỊ CHI TIẾT BIẾN THỂ RÕ RÀNG (Check thông qua attributes) -->
                                        @if(!empty($item['attributes']) || !empty($item['parsed_attr']))
                                            <div class="bg-light border border-secondary-subtle rounded p-2 mt-2" style="display: inline-block; min-width: 70%;">
                                                <div class="text-muted small fw-bold mb-1" style="font-size: 0.7rem; text-transform: uppercase;">
                                                    <i class="fa-solid fa-tags me-1"></i> Phân loại khách chọn:
                                                </div>
                                                
                                                @if(!empty($item['parsed_attr']) && is_array($item['parsed_attr']))
                                                    <div class="d-flex gap-2 flex-wrap">
                                                        @foreach($item['parsed_attr'] as $key => $value)
                                                            @if(!empty($value))
                                                                <span class="badge bg-white text-dark border border-secondary-subtle fw-medium shadow-sm" style="font-size: 0.75rem;">
                                                                    {{ $key }}: <strong class="text-danger">{{ $value }}</strong>
                                                                </span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="badge bg-white text-dark border border-secondary-subtle fw-medium shadow-sm" style="font-size: 0.75rem;">
                                                        <strong class="text-danger">{{ $item['attributes'] }}</strong>
                                                    </span>
                                                @endif
                                                
                                                @php $varId = $item['product_variant_id'] ?? $item['variant_id'] ?? null; @endphp
                                                @if($varId)
                                                    <div class="text-muted mt-1" style="font-size: 0.65rem;">Mã phân loại: #{{ $varId }}</div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border mt-1" style="font-size: 0.7rem;">Sản phẩm đơn (Không có phân loại)</span>
                                        @endif
                                    </td>
                                    
                                    <td class="py-3 text-muted fw-medium">{{ number_format($item['price'], 0, ',', '.') }}đ</td>
                                    
                                    <td class="py-3">
                                        <span class="badge bg-dark px-2 py-1 fs-6 shadow-sm">x{{ $item['quantity'] }}</span>
                                    </td>
                                    
                                    <td class="text-end fw-bold py-3 pe-4 text-danger fs-6">
                                        {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}đ
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TỔNG KẾT ĐƠN HÀNG -->
                <div class="card-footer bg-white border-top-0 p-4">
                    <div class="row justify-content-end">
                        <div class="col-md-6 col-lg-5">
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                <span class="text-muted small text-uppercase">Tạm tính:</span>
                                <span class="fw-medium text-dark">{{ number_format($order['total_amount'], 0, ',', '.') }}đ</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 pb-2 border-bottom">
                                <span class="text-muted small text-uppercase">Phí giao hàng:</span>
                                <span class="text-success fw-medium">Miễn phí</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="fw-bold text-dark text-uppercase">Tổng thanh toán:</span>
                                <span class="fw-bold text-danger fs-4">{{ number_format($order['total_amount'], 0, ',', '.') }}đ</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection