@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Thêm Mới Sản Phẩm</h5>
        </div>
        <div class="card-body">
            <!-- Hiển thị lỗi tổng quan nếu có -->
            @if(isset($errors['system']))
                <div class="alert alert-danger">{{ $errors['system'] }}</div>
            @endif

            <form action="/product/store" method="POST" enctype="multipart/form-data">

                <!-- 1. Thông tin chung -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" name="name" 
                                   class="form-control {{ isset($errors['name']) ? 'is-invalid' : '' }}" 
                                   value="{{ $old['name'] ?? '' }}" 
                                   placeholder="Nhập tên sản phẩm...">
                            @if(isset($errors['name']))
                                <div class="invalid-feedback">{{ $errors['name'] }}</div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select {{ isset($errors['category_id']) ? 'is-invalid' : '' }}">
                                    <option value="">-- Chọn danh mục --</option>
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat['id'] }}" {{ (isset($old['category_id']) && $old['category_id'] == $cat['id']) ? 'selected' : '' }}>
                                        {{ $cat['name'] }}                                            
                                    </option>
                                    @endforeach
                                </select>
                                @if(isset($errors['category_id']))
                                    <div class="invalid-feedback">{{ $errors['category_id'] }}</div>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Trạng thái</label>
                                <select name="status" class="form-select">
                                    <option value="active" {{ (isset($old['status']) && $old['status'] == 'active') ? 'selected' : '' }}>Đang bán</option>
                                    <option value="inactive" {{ (isset($old['status']) && $old['status'] == 'inactive') ? 'selected' : '' }}>Ngừng bán</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Giá gốc <span class="text-danger">*</span></label>
                                <input type="number" name="price_regular" 
                                       class="form-control {{ isset($errors['price_regular']) ? 'is-invalid' : '' }}" 
                                       value="{{ $old['price_regular'] ?? 0 }}">
                                @if(isset($errors['price_regular']))
                                    <div class="invalid-feedback">{{ $errors['price_regular'] }}</div>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Giá khuyến mãi</label>
                                <input type="number" name="price_sale" 
                                       class="form-control {{ isset($errors['price_sale']) ? 'is-invalid' : '' }}" 
                                       value="{{ $old['price_sale'] ?? 0 }}">
                                @if(isset($errors['price_sale']))
                                    <div class="invalid-feedback">{{ $errors['price_sale'] }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mô tả ngắn</label>
                            <textarea name="description" class="form-control" rows="3">{{ $old['description'] ?? '' }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nội dung chi tiết</label>
                            <textarea name="content" class="form-control" rows="5">{{ $old['content'] ?? '' }}</textarea>
                        </div>
                    </div>

                    <!-- 2. Ảnh đại diện & Gallery -->
                    <div class="col-md-4">
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <label class="form-label fw-bold">Ảnh đại diện</label>
                                <input type="file" name="img_thumbnail" class="form-control mb-2" accept="image/*">
                            </div>
                        </div>
                        <div class="card bg-light">
                            <div class="card-body">
                                <label class="form-label fw-bold">Thư viện ảnh (Gallery)</label>
                                <input type="file" name="gallery[]" class="form-control" multiple accept="image/*">
                                <small class="text-muted">Giữ Ctrl để chọn nhiều ảnh</small>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- 3. Biến thể (Variants) -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="fw-bold text-secondary">Danh sách Biến thể</h5>
                        <button type="button" class="btn btn-sm btn-success" id="btnAddVariant">
                            <i class="fa-solid fa-plus"></i> Thêm biến thể
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>SKU</th>
                                    <th>Thuộc tính (Màu, Size)</th>
                                    <th>Giá</th>
                                    <th>Số lượng</th>
                                    <th>Ảnh</th>
                                    <th>Xóa</th>
                                </tr>
                            </thead>
                            <tbody id="variantContainer">
                                <!-- Dòng mẫu mặc định -->
                                <tr>
                                    <td><input type="text" name="variant_sku[]" class="form-control form-control-sm" placeholder="SKU-001"></td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <input type="text" name="variant_color[]" class="form-control form-control-sm" placeholder="Màu (Đỏ)">
                                            <input type="text" name="variant_size[]" class="form-control form-control-sm" placeholder="Size (XL)">
                                        </div>
                                    </td>
                                    <td><input type="number" name="variant_price[]" class="form-control form-control-sm" value="0"></td>
                                    <td><input type="number" name="variant_qty[]" class="form-control form-control-sm" value="10"></td>
                                    <td><input type="file" name="variant_image[]" class="form-control form-control-sm"></td>
                                    <td><button type="button" class="btn btn-danger btn-sm btn-remove"><i class="fa-solid fa-trash"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="/product/index" class="btn btn-secondary">Hủy</a>
                    <button type="submit" class="btn btn-primary px-4">Lưu Sản Phẩm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('btnAddVariant').addEventListener('click', function() {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="text" name="variant_sku[]" class="form-control form-control-sm"></td>
            <td>
                <div class="d-flex gap-1">
                    <input type="text" name="variant_color[]" class="form-control form-control-sm" placeholder="Màu">
                    <input type="text" name="variant_size[]" class="form-control form-control-sm" placeholder="Size">
                </div>
            </td>
            <td><input type="number" name="variant_price[]" class="form-control form-control-sm" value="0"></td>
            <td><input type="number" name="variant_qty[]" class="form-control form-control-sm" value="10"></td>
            <td><input type="file" name="variant_image[]" class="form-control form-control-sm"></td>
            <td><button type="button" class="btn btn-danger btn-sm btn-remove"><i class="fa-solid fa-trash"></i></button></td>
        `;
        document.getElementById('variantContainer').appendChild(tr);
    });

    document.getElementById('variantContainer').addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove')) {
            e.target.closest('tr').remove();
        }
    }); 
</script>
@endsection