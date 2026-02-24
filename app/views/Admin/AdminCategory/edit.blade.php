@extends('layouts.admin')

@section('title', $title ?? 'Cập Nhật Danh Mục')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Breadcrumb & Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mt-0 mb-2">Cập Nhật Danh Mục</h1>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/category/index" class="text-decoration-none text-muted">Danh mục</a></li>
                <li class="breadcrumb-item active">Cập nhật</li>
            </ol>
        </div>
        <a href="/category/index" class="btn btn-outline-dark shadow-sm px-4" style="text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">
            <i class="fa-solid fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <i class="fa-solid fa-layer-group me-1 text-muted"></i> Thông tin danh mục
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="/category/update/{{ $category['id'] }}">

                        <!-- Tên danh mục -->
                        <div class="mb-4">
                            <label for="name" class="form-label fw-bold" style="font-size: 0.95rem;">Tên danh mục <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-0 py-2 {{ isset($errors['name']) ? 'is-invalid' : '' }}"
                                id="name" name="name"
                                value="{{ $category['name'] ?? '' }}"
                                placeholder="Ví dụ: Áo sơ mi, Quần Jean...">
                            @if(isset($errors['name']))
                            <div class="invalid-feedback">{{ $errors['name'] }}</div>
                            @endif
                        </div>

                        <!-- Trạng thái -->
                        <div class="mb-4">
                            <label for="status" class="form-label fw-bold" style="font-size: 0.95rem;">Trạng thái</label>
                            <select class="form-select rounded-0 py-2" id="status" name="status">
                                <option value="active" {{ (isset($category['status']) && $category['status'] == 'active') ? 'selected' : '' }}>Hiển thị</option>
                                <option value="inactive" {{ (isset($category['status']) && $category['status'] == 'inactive') ? 'selected' : '' }}>Ẩn</option>
                            </select>
                        </div>

                        <!-- Icon -->
                        <div class="mb-4">
                            <label for="icon" class="form-label fw-bold" style="font-size: 0.95rem;">Icon hiển thị <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light rounded-0 border-end-0 text-muted"><i class="fa-solid fa-icons"></i></span>
                                <input type="text" class="form-control rounded-0 border-start-0 py-2 {{ isset($errors['icon']) ? 'is-invalid' : '' }}"
                                    id="icon" name="icon"
                                    value="{{ $category['icon'] ?? '' }}"
                                    placeholder="Ví dụ: fa-solid fa-shirt">
                                <a href="https://fontawesome.com/search?o=r&m=free" target="_blank" class="btn btn-outline-dark rounded-0 px-3 d-flex align-items-center" title="Tìm kiếm icon">
                                    <i class="fa-solid fa-magnifying-glass me-2 d-none d-sm-inline"></i> Tra icon
                                </a>
                                @if(isset($errors['icon']))
                                <div class="invalid-feedback">{{ $errors['icon'] }}</div>
                                @endif
                            </div>
                            <div class="form-text text-muted mt-2">
                                <small><i class="fa-solid fa-circle-info me-1"></i>Sử dụng class icon từ thư viện <strong>FontAwesome 6</strong>.</small>
                            </div>
                        </div>

                        <!-- Mô tả -->
                        <div class="mb-5">
                            <label for="description" class="form-label fw-bold" style="font-size: 0.95rem;">Mô Tả</label>
                            <textarea class="form-control rounded-0" id="description" name="description" rows="4" placeholder="Nhập mô tả danh mục...">{{ $category['description'] ?? '' }}</textarea>
                        </div>

                        <!-- Nút Thao tác -->
                        <div class="d-flex justify-content-end gap-3 border-top pt-4">
                            <a href="/category/index" class="btn btn-outline-dark px-4 py-2" style="text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">Hủy bỏ</a>
                            <button type="submit" class="btn btn-dark px-4 py-2" style="text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">
                                <i class="fa-solid fa-save me-2"></i>Cập nhật
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection