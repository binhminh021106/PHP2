@extends('layouts.client')

@section('title', $title ?? 'Chi tiết đơn hàng')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap');
    body { font-family: 'Jost', sans-serif; background-color: #fcfcfc; color: #111; }
    
    .detail-card { background: #fff; border: 1px solid #eee; padding: 30px; margin-bottom: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.02); }
    .detail-title { font-family: 'Playfair Display', serif; font-size: 1.25rem; font-weight: 600; margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 10px; }
    
    .info-list { list-style: none; padding: 0; margin: 0; }
    .info-list li { margin-bottom: 10px; display: flex; justify-content: space-between; font-size: 0.95rem; }
    .info-list li span:first-child { color: #777; }
    .info-list li span:last-child { font-weight: 500; text-align: right; }

    .item-img { width: 70px; height: 95px; object-fit: cover; border: 1px solid #eee; }
    
    .btn-custom { border-radius: 0; padding: 10px 25px; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; font-weight: 500; transition: 0.3s; }
    .btn-dark { background: #111; color: white; border: none; }
    .btn-dark:hover { background: #c9a47c; }
    .btn-danger-custom { border: 1px solid #dc3545; color: #dc3545; background: transparent; }
    .btn-danger-custom:hover { background: #dc3545; color: white; }
</style>

<div class="bg-light py-3 border-bottom mb-5">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-dark">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="/order/history" class="text-decoration-none text-dark">Lịch sử đơn hàng</a></li>
                <li class="breadcrumb-item active">Chi tiết đơn #{{ $order['id'] }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5 pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="font-family: 'Playfair Display', serif; font-weight: 600;">Chi Tiết Đơn Hàng #{{ $order['id'] }}</h2>
        <a href="/order/history" class="btn btn-outline-dark btn-custom"><i class="fas fa-arrow-left me-2"></i>Quay Lại</a>
    </div>

    <div class="row g-4">
        <!-- Thông tin giao hàng & Đơn hàng -->
        <div class="col-lg-4">
            <div class="detail-card">
                <h4 class="detail-title">Thông tin nhận hàng</h4>
                <ul class="info-list">
                    <li><span>Họ tên:</span> <span>{{ $order['fullname'] }}</span></li>
                    <li><span>Số điện thoại:</span> <span>{{ $order['phone'] }}</span></li>
                    <li><span>Email:</span> <span>{{ $order['email'] ?? 'Không có' }}</span></li>
                    <li><span>Địa chỉ:</span> <span style="max-width: 60%;">{{ $order['address'] }}</span></li>
                    @if(!empty($order['note']))
                    <li class="mt-3 flex-column">
                        <span>Ghi chú:</span> 
                        <span class="mt-1 p-2 bg-light text-start">{{ $order['note'] }}</span>
                    </li>
                    @endif
                </ul>
            </div>

            <div class="detail-card">
                <h4 class="detail-title">Trạng thái & Thanh toán</h4>
                <ul class="info-list">
                    <li><span>Ngày đặt hàng:</span> <span>{{ date('d/m/Y H:i', strtotime($order['created_at'])) }}</span></li>
                    <li><span>Phương thức TT:</span> <span class="text-uppercase">{{ $order['payment_method'] == 'cod' ? 'Thanh toán khi nhận hàng' : 'Chuyển khoản' }}</span></li>
                    <li>
                        <span>Trạng thái:</span> 
                        <span>
                            @if($order['status'] == 'pending') <span class="badge bg-warning text-dark">Chờ xác nhận</span>
                            @elseif($order['status'] == 'processing') <span class="badge bg-info text-dark">Đang xử lý</span>
                            @elseif($order['status'] == 'shipped') <span class="badge bg-primary">Đang giao</span>
                            @elseif($order['status'] == 'delivered') <span class="badge bg-success">Đã giao</span>
                            @elseif($order['status'] == 'cancelled') <span class="badge bg-danger">Đã hủy</span>
                            @endif
                        </span>
                    </li>
                </ul>

                <!-- Nút hủy đơn (Chỉ hiện khi pending) -->
                @if($order['status'] == 'pending')
                    <div class="mt-4 pt-3 border-top text-center">
                        <p class="small text-muted mb-2">Bạn có thể hủy đơn hàng trước khi chúng tôi xác nhận.</p>
                        <a href="/order/cancel/{{ $order['id'] }}" class="btn btn-danger-custom btn-custom w-100" onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?');">Hủy Đơn Hàng</a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Danh sách Sản phẩm đã mua -->
        <div class="col-lg-8">
            <div class="detail-card">
                <h4 class="detail-title">Sản phẩm trong đơn</h4>
                
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th colspan="2" class="py-3 text-uppercase text-muted" style="font-size: 0.85rem;">Sản phẩm</th>
                                <th class="text-center py-3 text-uppercase text-muted" style="font-size: 0.85rem;">Đơn giá</th>
                                <th class="text-center py-3 text-uppercase text-muted" style="font-size: 0.85rem;">Số lượng</th>
                                <th class="text-end py-3 text-uppercase text-muted" style="font-size: 0.85rem;">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orderItems as $item)
                            <tr>
                                <td style="width: 80px;" class="py-3">
                                    @php $img = !empty($item['image']) ? '/storage/uploads/variants/' . $item['image'] : (!empty($item['img_thumbnail']) ? '/storage/uploads/products/' . $item['img_thumbnail'] : 'https://placehold.co/70x95'); @endphp
                                    <img src="{{ $img }}" alt="{{ $item['product_name'] }}" class="item-img">
                                </td>
                                <td class="py-3">
                                    <a href="/home/detail/{{ $item['product_id'] }}" class="text-dark text-decoration-none fw-medium d-block mb-1">{{ $item['product_name'] }}</a>
                                    @if(!empty($item['parsed_attr']))
                                        <small class="text-muted">{{ $item['parsed_attr']['Color'] ?? '' }} / {{ $item['parsed_attr']['Size'] ?? '' }}</small>
                                    @endif
                                </td>
                                <td class="text-center py-3">{{ number_format($item['price'], 0, ',', '.') }}đ</td>
                                <td class="text-center py-3">x{{ $item['quantity'] }}</td>
                                <td class="text-end fw-bold py-3">{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}đ</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                    <div style="width: 300px;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tạm tính:</span>
                            <span>{{ number_format($order['total_amount'], 0, ',', '.') }}đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Phí giao hàng:</span>
                            <span class="text-success">Miễn phí</span>
                        </div>
                        <div class="d-flex justify-content-between mt-3 pt-3 border-top">
                            <span class="fw-bold text-dark fs-5">Tổng tiền:</span>
                            <span class="fw-bold text-danger fs-4">{{ number_format($order['total_amount'], 0, ',', '.') }}đ</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection