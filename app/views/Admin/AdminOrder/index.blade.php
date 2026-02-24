@extends('layouts.admin')

@section('title', $title ?? 'Quản lý đơn hàng')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Breadcrumb & Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mt-0 mb-2">Quản Lý Đơn Hàng</h1>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item active">Đơn hàng</li>
            </ol>
        </div>
        <!-- Có thể thêm nút lọc nhanh ở đây nếu cần -->
    </div>

    <!-- Thông báo Alert -->
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

    <!-- Bảng dữ liệu đơn hàng -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <i class="fa-solid fa-receipt me-1 text-muted"></i> Danh sách đơn hàng toàn hệ thống
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center mb-0" id="datatablesSimple">
                    <thead class="bg-light" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <tr>
                            <th width="8%" class="py-3 text-muted">Mã Đơn</th>
                            <th width="15%" class="py-3 text-muted">Ngày Đặt</th>
                            <th width="20%" class="py-3 text-muted text-start">Khách Hàng</th>
                            <th width="15%" class="py-3 text-muted">Tổng Tiền</th>
                            <th width="12%" class="py-3 text-muted">Thanh Toán</th>
                            <th width="15%" class="py-3 text-muted">Trạng Thái</th>
                            <th width="15%" class="py-3 text-muted">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($orders))
                            @foreach($orders as $order)
                            <tr>
                                <td class="fw-bold text-dark">#{{ $order['id'] }}</td>
                                <td>
                                    <span class="d-block text-dark fw-medium">{{ date('d/m/Y', strtotime($order['created_at'])) }}</span>
                                    <span class="small text-muted">{{ date('H:i', strtotime($order['created_at'])) }}</span>
                                </td>
                                <td class="text-start">
                                    <div class="fw-bold text-dark" style="font-family: var(--font-base);">{{ $order['fullname'] }}</div>
                                    <small class="text-muted"><i class="fa-solid fa-phone me-1" style="font-size: 0.7rem;"></i> {{ $order['phone'] }}</small>
                                </td>
                                <td>
                                    <span class="text-danger fw-bold fs-6">{{ number_format($order['total_amount'], 0, ',', '.') }}đ</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                        {{ $order['payment_method'] == 'cod' ? 'COD' : 'Chuyển khoản' }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = 'bg-secondary';
                                        $statusText = 'Không rõ';
                                        switch($order['status']) {
                                            case 'pending': $badgeClass = 'bg-warning text-dark'; $statusText = 'Chờ xác nhận'; break;
                                            case 'processing': $badgeClass = 'bg-info text-white'; $statusText = 'Đang xử lý'; break;
                                            case 'shipped': $badgeClass = 'bg-primary'; $statusText = 'Đang giao hàng'; break;
                                            case 'delivered': $badgeClass = 'bg-success'; $statusText = 'Đã giao'; break;
                                            case 'cancelled': $badgeClass = 'bg-danger'; $statusText = 'Đã hủy'; break;
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeClass }}" style="padding: 6px 12px; font-weight: 500; letter-spacing: 0.5px; text-transform: uppercase; font-size: 0.7rem;">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td>
                                    <a href="/adminorder/detail/{{ $order['id'] }}" class="btn btn-sm btn-outline-dark px-3 rounded-0" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                                        <i class="fa-solid fa-eye me-1"></i> CHI TIẾT
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fa-solid fa-box-open fs-1 mb-3 opacity-25"></i>
                                        <h5 class="fw-normal">Chưa có đơn hàng nào</h5>
                                        <p class="small mb-0">Các đơn hàng của khách sẽ xuất hiện tại đây.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection