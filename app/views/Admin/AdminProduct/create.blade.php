@extends('layouts.admin')

@section('title', $title ?? 'Thêm Mới Sản Phẩm')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Breadcrumb & Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mt-0 mb-2">Thêm Sản Phẩm Mới</h1>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/product/index" class="text-decoration-none text-muted">Sản phẩm</a></li>
                <li class="breadcrumb-item active">Thêm mới</li>
            </ol>
        </div>
        <a href="/product/index" class="btn btn-outline-dark shadow-sm px-4" style="text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">
            <i class="fa-solid fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>

    <!-- Hiển thị lỗi hệ thống nếu có -->
    @if(isset($errors['system']))
        <div class="alert alert-danger rounded-0 border-0 shadow-sm mb-4">
            <i class="fa-solid fa-circle-exclamation me-2"></i> {{ $errors['system'] }}
        </div>
    @endif

    <form action="/product/store" method="POST" enctype="multipart/form-data">
        <div class="row g-4">
            <!-- CỘT TRÁI: THÔNG TIN CHÍNH -->
            <div class="col-lg-8">
                <!-- Khối thông tin cơ bản -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 fw-bold">
                        <i class="fa-solid fa-pen-to-square me-2 text-muted"></i>Thông tin cơ bản
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase">Tên sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" name="name" 
                                   class="form-control rounded-0 py-2 {{ isset($errors['name']) ? 'is-invalid' : '' }}" 
                                   value="{{ $old['name'] ?? '' }}" 
                                   placeholder="Ví dụ: Áo Sơ Mi Nam Cotton Oxford...">
                            @if(isset($errors['name']))
                                <div class="invalid-feedback">{{ $errors['name'] }}</div>
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold small text-uppercase">Giá niêm yết (đ) <span class="text-danger">*</span></label>
                                <input type="number" name="price_regular" 
                                       class="form-control rounded-0 py-2 {{ isset($errors['price_regular']) ? 'is-invalid' : '' }}" 
                                       value="{{ $old['price_regular'] ?? 0 }}">
                                @if(isset($errors['price_regular']))
                                    <div class="invalid-feedback">{{ $errors['price_regular'] }}</div>
                                @endif
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold small text-uppercase">Giá khuyến mãi (đ)</label>
                                <input type="number" name="price_sale" 
                                       class="form-control rounded-0 py-2 {{ isset($errors['price_sale']) ? 'is-invalid' : '' }}" 
                                       value="{{ $old['price_sale'] ?? 0 }}">
                                @if(isset($errors['price_sale']))
                                    <div class="invalid-feedback">{{ $errors['price_sale'] }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase">Mô tả ngắn</label>
                            <textarea name="description" class="form-control rounded-0" rows="3" placeholder="Tóm tắt đặc điểm nổi bật của sản phẩm...">{{ $old['description'] ?? '' }}</textarea>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold small text-uppercase">Nội dung chi tiết</label>
                            <textarea name="content" id="editor_content" class="form-control rounded-0">{{ $old['content'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Khối biến thể sản phẩm -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fa-solid fa-layer-group me-2 text-muted"></i>Danh sách biến thể</span>
                        <button type="button" class="btn btn-sm btn-dark rounded-0 px-3" id="btnAddVariant">
                            <i class="fa-solid fa-plus me-1"></i> Thêm biến thể
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 text-center" style="font-size: 0.9rem;">
                                <thead class="table-light text-uppercase small" style="letter-spacing: 0.5px;">
                                    <tr>
                                        <th width="15%">SKU</th>
                                        <th width="30%">Màu sắc / Kích thước</th>
                                        <th width="15%">Giá bán</th>
                                        <th width="12%">Số lượng</th>
                                        <th width="20%">Ảnh</th>
                                        <th width="8%"></th>
                                    </tr>
                                </thead>
                                <tbody id="variantContainer">
                                    <tr>
                                        <td><input type="text" name="variant_sku[]" class="form-control form-control-sm rounded-0" placeholder="SKU-01"></td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <input type="text" name="variant_color[]" class="form-control form-control-sm rounded-0" placeholder="Màu">
                                                <input type="text" name="variant_size[]" class="form-control form-control-sm rounded-0" placeholder="Size">
                                            </div>
                                        </td>
                                        <td><input type="number" name="variant_price[]" class="form-control form-control-sm rounded-0 text-end" value="0"></td>
                                        <td><input type="number" name="variant_qty[]" class="form-control form-control-sm rounded-0 text-center" value="10"></td>
                                        <td><input type="file" name="variant_image[]" class="form-control form-control-sm rounded-0"></td>
                                        <td><button type="button" class="btn btn-link text-danger btn-remove p-0"><i class="fa-solid fa-trash-can"></i></button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CỘT PHẢI: THIẾT LẬP & MEDIA -->
            <div class="col-lg-4">
                <!-- Trạng thái & Phân loại -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 fw-bold">
                        <i class="fa-solid fa-gear me-2 text-muted"></i>Thiết lập
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase">Trạng thái</label>
                            <select name="status" class="form-select rounded-0 py-2">
                                <option value="active" {{ (isset($old['status']) && $old['status'] == 'active') ? 'selected' : '' }}>Đang kinh doanh</option>
                                <option value="inactive" {{ (isset($old['status']) && $old['status'] == 'inactive') ? 'selected' : '' }}>Ngừng bán</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase">Danh mục <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select rounded-0 py-2 {{ isset($errors['category_id']) ? 'is-invalid' : '' }}">
                                <option value="">-- Chọn danh mục --</option>
                                @if(!empty($categories))
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat['id'] }}" {{ (isset($old['category_id']) && $old['category_id'] == $cat['id']) ? 'selected' : '' }}>{{ $cat['name'] }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @if(isset($errors['category_id']))
                                <div class="invalid-feedback">{{ $errors['category_id'] }}</div>
                            @endif
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold small text-uppercase">Thương hiệu <span class="text-danger">*</span></label>
                            <select name="brand_id" class="form-select rounded-0 py-2 {{ isset($errors['brand_id']) ? 'is-invalid' : '' }}">
                                <option value="">-- Chọn thương hiệu --</option>
                                @if(!empty($brands))
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand['id'] }}" {{ (isset($old['brand_id']) && $old['brand_id'] == $brand['id']) ? 'selected' : '' }}>{{ $brand['name'] }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @if(isset($errors['brand_id']))
                                <div class="invalid-feedback">{{ $errors['brand_id'] }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Hình ảnh đại diện -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 fw-bold">
                        <i class="fa-solid fa-image me-2 text-muted"></i>Ảnh đại diện
                    </div>
                    <div class="card-body p-4 text-center">
                        <div class="border border-dashed p-3 mb-3 bg-light position-relative" style="min-height: 200px;">
                            <i class="fa-solid fa-cloud-arrow-up fs-2 text-muted mt-4 d-block mb-2"></i>
                            <span class="text-muted small">Tải lên ảnh thumbnail chính</span>
                            <input type="file" name="img_thumbnail" id="thumbInput" class="form-control rounded-0 mt-3" accept="image/*">
                            <!-- Preview Image -->
                            <div id="thumbPreview" class="mt-2 d-none">
                                <img src="" class="img-fluid border shadow-sm p-1" style="max-height: 150px;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thư viện ảnh -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 fw-bold">
                        <i class="fa-regular fa-images me-2 text-muted"></i>Thư viện ảnh
                    </div>
                    <div class="card-body p-4">
                        <input type="file" name="gallery[]" class="form-control rounded-0" multiple accept="image/*">
                        <div class="form-text mt-2 small">Chọn nhiều ảnh mô tả chi tiết sản phẩm.</div>
                    </div>
                </div>

                <!-- Nút lưu đơn hàng -->
                <div class="sticky-top" style="top: 90px; z-index: 10;">
                    <div class="card shadow border-0 bg-dark text-white">
                        <div class="card-body p-3">
                            <button type="submit" class="btn btn-light w-100 py-3 fw-bold rounded-0" style="letter-spacing: 1.5px; text-transform: uppercase;">
                                <i class="fa-solid fa-floppy-disk me-2"></i>Lưu Sản Phẩm
                            </button>
                            <a href="/product/index" class="btn btn-outline-light w-100 mt-2 py-2 border-0 small text-uppercase" style="font-size: 0.75rem;">Hủy thao tác</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .border-dashed { border: 2px dashed #ddd !important; }
    #variantContainer .form-control { border-color: #eee; }
    #variantContainer .form-control:focus { border-color: #111; }
</style>

<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>

<script>
    // Khởi tạo CKEditor
    CKEDITOR.replace('editor_content', {
        height: 300,
        removeButtons: 'PasteFromWord'
    });

    // Preview ảnh đại diện
    document.getElementById('thumbInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('thumbPreview');
        const previewImg = preview.querySelector('img');
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                previewImg.src = event.target.result;
                preview.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        } else {
            preview.classList.add('d-none');
        }
    });

    // Thêm biến thể
    document.getElementById('btnAddVariant').addEventListener('click', function() {
        const tbody = document.getElementById('variantContainer');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="text" name="variant_sku[]" class="form-control form-control-sm rounded-0" placeholder="SKU"></td>
            <td>
                <div class="d-flex gap-1">
                    <input type="text" name="variant_color[]" class="form-control form-control-sm rounded-0" placeholder="Màu">
                    <input type="text" name="variant_size[]" class="form-control form-control-sm rounded-0" placeholder="Size">
                </div>
            </td>
            <td><input type="number" name="variant_price[]" class="form-control form-control-sm rounded-0 text-end" value="0"></td>
            <td><input type="number" name="variant_qty[]" class="form-control form-control-sm rounded-0 text-center" value="10"></td>
            <td><input type="file" name="variant_image[]" class="form-control form-control-sm rounded-0"></td>
            <td><button type="button" class="btn btn-link text-danger btn-remove p-0"><i class="fa-solid fa-trash-can"></i></button></td>
        `;
        tbody.appendChild(tr);
    });

    // Xóa biến thể
    document.getElementById('variantContainer').addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove')) {
            const rows = document.querySelectorAll('#variantContainer tr');
            if (rows.length > 1) {
                e.target.closest('tr').remove();
            } else {
                Swal.fire({ icon: 'warning', title: 'Thông báo', text: 'Sản phẩm phải có ít nhất một biến thể!' });
            }
        }
    }); 
</script>
@endsection