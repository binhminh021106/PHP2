@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card border-0 shadow-lg rounded-3">

                {{-- Header Card --}}
                <div class="card-header bg-warning text-dark py-3 rounded-top-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 fw-bold">
                            <i class="fa-solid fa-pen-to-square me-2"></i>Chỉnh Sửa Danh Mục
                        </h5>
                        <a href="/category/index" class="btn btn-outline-dark btn-sm border-0 bg-white bg-opacity-25">
                            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
                        </a>
                    </div>
                </div>

                {{-- Body Card --}}
                <div class="card-body p-5 bg-white">
                    <form method="POST" action="/category/update/{{ $category['id'] }}">

                        {{-- Tên danh mục --}}
                        <div class="mb-4">
                            <label for="name" class="form-label fw-bold text-dark">
                                Tên danh mục <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light text-warning"><i class="fa-solid fa-tag"></i></span>
                                <input type="text" class="form-control bg-light" id="name" name="name"
                                    value="{{ $category['name'] ?? '' }}" required
                                    placeholder="Ví dụ: Laptop, Điện thoại..." style="font-size: 0.95rem;">
                            </div>
                        </div>

                        {{-- Mô tả --}}
                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold text-dark">Mô tả</label>
                            <textarea class="form-control bg-light" id="description" name="description" rows="5"
                                placeholder="Nhập mô tả chi tiết cho danh mục...">{{ $category['description'] ?? '' }}</textarea>
                        </div>

                        {{-- Icon --}}
                        <div class="mb-4">
                            <label for="icon" class="form-label fw-bold text-dark">Icon hiển thị</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-warning"><i class="fa-solid fa-icons"></i></span>
                                <input type="text" class="form-control bg-light" id="icon" name="icon"
                                    value="{{ $category['icon'] ?? '' }}"
                                    placeholder="Ví dụ: fa-solid fa-laptop">
                                <a href="https://fontawesome.com/search?o=r&m=free" target="_blank" class="btn btn-outline-secondary" title="Tìm kiếm icon">
                                    <i class="fa-solid fa-magnifying-glass"></i> Tra icon
                                </a>
                            </div>
                            <div class="form-text text-muted ps-1">
                                <small><i class="fa-solid fa-circle-info me-1"></i>Sử dụng class icon từ thư viện <strong>FontAwesome 6</strong>.</small>
                            </div>
                        </div>

                        <hr class="my-4 opacity-25">

                        {{-- Buttons --}}
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/category/index" class="btn btn-light btn-lg px-4 fs-6 border">
                                <i class="fa-solid fa-xmark me-2"></i>Hủy bỏ
                            </a>
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