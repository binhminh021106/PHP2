@extends('layouts.client')

@section('title', $title ?? 'Lịch sử đơn hàng')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap');
    body { font-family: 'Jost', sans-serif; background-color: #fcfcfc; color: #111; }
    .page-title { font-family: 'Playfair Display', serif; font-size: 2.5rem; font-weight: 600; text-align: center; margin-bottom: 40px; }
    
    .table th { text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; color: #777; font-weight: 600; border-bottom: 2px solid #eee; padding-bottom: 15px;}
    .table td { vertical-align: middle; padding: 20px 10px; border-bottom: 1px solid #eee; }
    
    .btn-outline-dark { border-radius: 0; padding: 8px 20px; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; font-weight: 500; transition: 0.3s; }
    .btn-outline-dark:hover { background: #111; color: white; }

    /* Custom Badges cho Trạng thái */
    .status-badge { padding: 6px 12px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; }
    .bg-pending { background-color: #ffc107; color: #000; }
    .bg-processing { background-color: #17a2b8; color: #000; }
    .bg-shipped { background-color: #0dcaf0; color: #fff; }
    .bg-delivered { background-color: #198754; color: #fff; }
    .bg-cancelled { background-color: #dc3545; color: #fff; }
</style>

<div class="bg-light py-3 border-bottom mb-5">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-dark">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="/profile" class="text-decoration-none text-dark">Tài khoản</a></li>
                <li class="breadcrumb-item active">Lịch sử đơn hàng</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5 pb-5">
    <h1 class="page-title">Lịch Sử Đơn Hàng</h1>

    <div class="card shadow-sm border-0 bg-white p-4">
        @if(empty($orders))
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-4x text-muted mb-3 opacity-50"></i>
                <h4>Bạn chưa có đơn hàng nào</h4>
                <p class="text-muted">Khi bạn đặt hàng, thông tin sẽ xuất hiện tại đây.</p>
                <a href="/shop" class="btn btn-outline-dark mt-3">Mua sắm ngay</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table align-middle text-center">
                    <thead>
                        <tr>
                            <th>Mã Đơn</th>
                            <th>Ngày Đặt Hàng</th>
                            <th>Người Nhận</th>
                            <th>Tổng Tiền</th>
                            <th>Trạng Thái</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td class="fw-bold">#{{ $order['id'] }}</td>
                            <td>{{ date('d/m/Y H:i', strtotime($order['created_at'])) }}</td>
                            <td>{{ $order['fullname'] }}</td>
                            <td class="text-danger fw-bold">{{ number_format($order['total_amount'], 0, ',', '.') }}đ</td>
                            <td>
                                @php
                                    $statusClass = '';
                                    $statusText = '';
                                    switch($order['status']) {
                                        case 'pending': $statusClass = 'bg-pending'; $statusText = 'Chờ xác nhận'; break;
                                        case 'processing': $statusClass = 'bg-processing'; $statusText = 'Đang xử lý'; break;
                                        case 'shipped': $statusClass = 'bg-shipped'; $statusText = 'Đang giao hàng'; break;
                                        case 'delivered': $statusClass = 'bg-delivered'; $statusText = 'Đã giao thành công'; break;
                                        case 'cancelled': $statusClass = 'bg-cancelled'; $statusText = 'Đã hủy'; break;
                                        default: $statusClass = 'bg-secondary'; $statusText = 'Không rõ'; break;
                                    }
                                @endphp
                                <span class="badge status-badge {{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                            <td>
                                <a href="/order/detail/{{ $order['id'] }}" class="btn btn-outline-dark btn-sm">Xem chi tiết</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

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