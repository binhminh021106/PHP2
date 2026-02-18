@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card border-0 shadow-lg rounded-3">

                <div class="card-header bg-warning text-dark py-3 rounded-top-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 fw-bold">
                            <i class="fa-solid fa-pen-to-square me-2"></i>Chỉnh Sửa Mã Coupon
                        </h5>
                        <a href="/coupon/index" class="btn btn-outline-dark btn-sm border-0 bg-white bg-opacity-25">
                            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
                        </a>
                    </div>
                </div>

                <div class="card-body p-5 bg-white">
                    <form method="POST" action="/coupon/update/{{ $coupon['id'] }}">

                        <div class="mb-4">
                            <label class="form-label fw-bold">Mã Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-light {{ isset($errors['code']) ? 'is-invalid' : '' }}"
                                name="code" value="{{ $coupon['code'] }}" placeholder="VD: SALE2024">
                            @if(isset($errors['code'])) <div class="invalid-feedback">{{ $errors['code'] }}</div> @endif
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Loại giảm giá</label>
                                <select name="type" class="form-select bg-light">
                                    <option value="percent" {{ $coupon['type'] == 'percent' ? 'selected' : '' }}>Phần trăm (%)</option>
                                    <option value="fixed" {{ $coupon['type'] == 'fixed' ? 'selected' : '' }}>Tiền mặt (VNĐ)</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Giá trị giảm <span class="text-danger">*</span></label>
                                <input type="number" class="form-control bg-light {{ isset($errors['value']) ? 'is-invalid' : '' }}"
                                    name="value" value="{{ $coupon['value'] }}">
                                @if(isset($errors['value'])) <div class="invalid-feedback">{{ $errors['value'] }}</div> @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Số lượng <span class="text-danger">*</span></label>
                                <input type="number" class="form-control bg-light {{ isset($errors['quantity']) ? 'is-invalid' : '' }}"
                                    name="quantity" value="{{ $coupon['quantity'] }}">
                                @if(isset($errors['quantity'])) <div class="invalid-feedback">{{ $errors['quantity'] }}</div> @endif
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Ngày hết hạn <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control bg-light {{ isset($errors['expired_at']) ? 'is-invalid' : '' }}"
                                    name="expired_at" value="{{ $coupon['expired_at'] }}">
                                @if(isset($errors['expired_at'])) <div class="invalid-feedback">{{ $errors['expired_at'] }}</div> @endif
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Trạng thái</label>
                            <select name="status" class="form-select bg-light">
                                <option value="active" {{ $coupon['status'] == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="inactive" {{ $coupon['status'] == 'inactive' ? 'selected' : '' }}>Tạm khóa</option>
                            </select>
                        </div>

                        <hr class="my-4 opacity-25">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="/coupon/index" class="btn btn-light btn-lg px-4 fs-6 border">Hủy bỏ</a>
                            <button type="submit" class="btn btn-warning btn-lg px-4 fs-6 fw-bold shadow-sm">
                                <i class="fa-solid fa-check me-2"></i>Cập Nhật
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection