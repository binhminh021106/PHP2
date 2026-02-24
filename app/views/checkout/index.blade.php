@extends('layouts.client')

@section('title', 'Thanh toán đơn hàng')

@section('content')
<div class="container mt-5 mb-5">
    <h2 class="mb-4 text-center" style="font-family: 'Playfair Display', serif; font-weight: 600;">Thanh toán đơn hàng</h2>
    
    @if(!empty($errorMsg))
        <div class="alert alert-danger">{{ $errorMsg }}</div>
    @endif

    <form action="/checkout/process" method="POST">
        <div class="row">
            <!-- Cột thông tin khách hàng -->
            <div class="col-md-7">
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h4 class="mb-0">Thông tin giao hàng</h4>
                    </div>
                    <div class="card-body">
                        
                        <!-- CHỌN ĐỊA CHỈ TỪ PROFILE -->
                        @if(!empty($addresses))
                        <div class="mb-4 p-3 bg-light border rounded">
                            <label class="form-label fw-bold text-dark"><i class="far fa-address-book me-2"></i>Chọn từ Sổ địa chỉ của bạn:</label>
                            <select class="form-select rounded-0 border-dark" id="addressSelect" onchange="fillAddress()">
                                <option value="">-- Nhập địa chỉ giao hàng mới --</option>
                                @foreach($addresses as $addr)
                                    <option value="{{ $addr['id'] }}" 
                                        data-name="{{ $addr['fullname'] }}" 
                                        data-phone="{{ $addr['phone'] }}" 
                                        data-address="{{ $addr['address'] }}"
                                        {{ ($defaultAddress && $defaultAddress['id'] == $addr['id']) ? 'selected' : '' }}>
                                        {{ $addr['fullname'] }} - {{ $addr['phone'] }} 
                                        {{ $addr['is_default'] ? '(Mặc định)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="mb-3">
                            <label for="fullname" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-0" id="fullname" name="fullname" 
                                   value="{{ $defaultAddress['fullname'] ?? ($user['name'] ?? '') }}" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control rounded-0" id="email" name="email" 
                                       value="{{ $user['email'] ?? '' }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded-0" id="phone" name="phone" 
                                       value="{{ $defaultAddress['phone'] ?? ($user['phone'] ?? '') }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ nhận hàng cụ thể <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-0" id="address" name="address" 
                                   value="{{ $defaultAddress['address'] ?? '' }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="note" class="form-label">Ghi chú đơn hàng (Tùy chọn)</label>
                            <textarea class="form-control rounded-0" id="note" name="note" rows="3" placeholder="Ví dụ: Giao hàng vào giờ hành chính..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cột thông tin đơn hàng -->
            <div class="col-md-5">
                <div class="card shadow-sm border-0 bg-light">
                    <div class="card-header bg-light border-bottom-0 pt-4 pb-0">
                        <h4 class="mb-0">Đơn hàng của bạn</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush mb-3 bg-transparent">
                            @foreach($cartItems as $item)
                            <li class="list-group-item d-flex justify-content-between lh-sm px-0 bg-transparent">
                                <div>
                                    <h6 class="my-0">{{ $item['name'] ?? $item['product_name'] }}</h6>
                                    <small class="text-muted">Số lượng: {{ $item['quantity'] }}</small>
                                    @if(!empty($item['parsed_attr']))
                                        <br><small class="text-muted">{{ $item['parsed_attr']['Color'] ?? '' }} / {{ $item['parsed_attr']['Size'] ?? '' }}</small>
                                    @endif
                                </div>
                                <span class="text-muted">{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }} đ</span>
                            </li>
                            @endforeach
                            
                            <li class="list-group-item d-flex justify-content-between px-0 bg-transparent mt-3 border-top border-dark border-2 pt-3">
                                <div class="text-dark">
                                    <h5 class="my-0 fw-bold">Tổng cộng</h5>
                                </div>
                                <span class="text-danger fw-bold fs-5">{{ number_format($totalPrice, 0, ',', '.') }} đ</span>
                            </li>
                        </ul>

                        <hr class="my-4 text-muted">
                        <h5 class="mb-3">Phương thức thanh toán</h5>
                        <div class="form-check mb-2">
                            <input id="cod" name="payment_method" type="radio" class="form-check-input" value="cod" checked required>
                            <label class="form-check-label" for="cod">
                                Thanh toán khi nhận hàng (COD)
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input id="bank" name="payment_method" type="radio" class="form-check-input" value="bank_transfer" required>
                            <label class="form-check-label" for="bank">
                                Chuyển khoản ngân hàng
                            </label>
                        </div>

                        <button class="btn w-100 py-3 mt-3 fw-bold rounded-0" type="submit" style="background-color: #111; color: white; letter-spacing: 1px;">XÁC NHẬN ĐẶT HÀNG</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Script tự động điền Form khi thay đổi select Box -->
<script>
    function fillAddress() {
        var select = document.getElementById('addressSelect');
        var selectedOption = select.options[select.selectedIndex];
        
        var nameInput = document.getElementById('fullname');
        var phoneInput = document.getElementById('phone');
        var addressInput = document.getElementById('address');
        
        if (select.value !== "") {
            // Nếu chọn địa chỉ có sẵn, lấy data từ option gán vào ô input
            nameInput.value = selectedOption.getAttribute('data-name');
            phoneInput.value = selectedOption.getAttribute('data-phone');
            addressInput.value = selectedOption.getAttribute('data-address');
        } else {
            // Nếu chọn "Nhập địa chỉ mới", xóa trắng các ô (hoặc điền lại tên mặc định từ Profile)
            nameInput.value = '{{ $user["name"] ?? "" }}';
            phoneInput.value = '{{ $user["phone"] ?? "" }}';
            addressInput.value = '';
        }
    }
</script>
@endsection