@extends('layouts.admin')

@section('title', $title ?? 'Chỉnh Sửa Mã Coupon')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Breadcrumb & Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mt-0 mb-2">Chỉnh Sửa Mã Coupon</h1>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/coupon/index" class="text-decoration-none text-muted">Mã giảm giá</a></li>
                <li class="breadcrumb-item active">Cập nhật</li>
            </ol>
        </div>
        <a href="/coupon/index" class="btn btn-outline-dark shadow-sm px-4" style="text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">
            <i class="fa-solid fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <i class="fa-solid fa-pen-to-square me-1 text-muted"></i> Thông tin mã giảm giá #{{ $coupon['id'] }}
                </div>
                
                <div class="card-body p-4">
                    <form method="POST" action="/coupon/update/{{ $coupon['id'] }}">
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold" style="font-size: 0.95rem;">Mã Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-0 py-2 {{ isset($errors['code']) ? 'is-invalid' : '' }}" 
                                    name="code" value="{{ $coupon['code'] }}" placeholder="VD: SALE2024">
                            @if(isset($errors['code'])) 
                                <div class="invalid-feedback">{{ $errors['code'] }}</div> 
                            @endif
                            <div class="form-text text-muted mt-2">
                                <small><i class="fa-solid fa-circle-info me-1"></i>Mã code nên viết liền, không dấu.</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold" style="font-size: 0.95rem;">Loại giảm giá</label>
                                <select name="type" class="form-select rounded-0 py-2">
                                    <option value="percent" {{ $coupon['type'] == 'percent' ? 'selected' : '' }}>Phần trăm (%)</option>
                                    <option value="fixed" {{ $coupon['type'] == 'fixed' ? 'selected' : '' }}>Tiền mặt (VNĐ)</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold" style="font-size: 0.95rem;">Giá trị giảm <span class="text-danger">*</span></label>
                                <input type="number" class="form-control rounded-0 py-2 {{ isset($errors['value']) ? 'is-invalid' : '' }}" 
                                        name="value" value="{{ $coupon['value'] }}">
                                @if(isset($errors['value'])) 
                                    <div class="invalid-feedback">{{ $errors['value'] }}</div> 
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold" style="font-size: 0.95rem;">Số lượng <span class="text-danger">*</span></label>
                                <input type="number" class="form-control rounded-0 py-2 {{ isset($errors['quantity']) ? 'is-invalid' : '' }}" 
                                        name="quantity" value="{{ $coupon['quantity'] }}">
                                @if(isset($errors['quantity'])) 
                                    <div class="invalid-feedback">{{ $errors['quantity'] }}</div> 
                                @endif
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold" style="font-size: 0.95rem;">Ngày hết hạn <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control rounded-0 py-2 {{ isset($errors['expired_at']) ? 'is-invalid' : '' }}" 
                                        name="expired_at" value="{{ date('Y-m-d\TH:i', strtotime($coupon['expired_at'])) }}">
                                @if(isset($errors['expired_at'])) 
                                    <div class="invalid-feedback">{{ $errors['expired_at'] }}</div> 
                                @endif
                            </div>
                        </div>

                        <div class="mb-5">
                            <label class="form-label fw-bold" style="font-size: 0.95rem;">Trạng thái</label>
                            <select name="status" class="form-select rounded-0 py-2">
                                <option value="active" {{ $coupon['status'] == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="inactive" {{ $coupon['status'] == 'inactive' ? 'selected' : '' }}>Tạm khóa</option>
                            </select>
                        </div>

                        <!-- Nút Thao tác -->
                        <div class="d-flex justify-content-end gap-3 border-top pt-4">
                            <a href="/coupon/index" class="btn btn-outline-dark px-4 py-2" style="text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">Hủy bỏ</a>
                            <button type="submit" class="btn btn-dark px-4 py-2" style="text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">
                                <i class="fa-solid fa-check me-2"></i>Cập nhật mã
                            </button>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection