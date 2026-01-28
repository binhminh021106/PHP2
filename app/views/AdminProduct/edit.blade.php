@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow border-0">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">Cập Nhật Sản Phẩm: {{ $product['name'] }}</h5>
        </div>
        <div class="card-body">
            @if(isset($errors['system']))
                <div class="alert alert-danger">{{ $errors['system'] }}</div>
            @endif

            <form action="/product/update/{{ $product['id'] }}" method="POST" enctype="multipart/form-data">
                
                <!-- 1. Thông tin chung -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" name="name" 
                                   class="form-control {{ isset($errors['name']) ? 'is-invalid' : '' }}" 
                                   value="{{ $product['name'] }}">
                            @if(isset($errors['name']))
                                <div class="invalid-feedback">{{ $errors['name'] }}</div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Danh mục</label>
                                <select name="category_id" class="form-select">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat['id'] }}" {{ $product['category_id'] == $cat['id'] ? 'selected' : '' }}>
                                            {{ $cat['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Trạng thái</label>
                                <select name="status" class="form-select">
                                    <option value="active" {{ $product['status'] == 'active' ? 'selected' : '' }}>Đang bán</option>
                                    <option value="inactive" {{ $product['status'] == 'inactive' ? 'selected' : '' }}>Ngừng bán</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Giá gốc</label>
                                <input type="number" name="price_regular" 
                                       class="form-control {{ isset($errors['price_regular']) ? 'is-invalid' : '' }}" 
                                       value="{{ $product['price_regular'] }}">
                                @if(isset($errors['price_regular']))
                                    <div class="invalid-feedback">{{ $errors['price_regular'] }}</div>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Giá khuyến mãi</label>
                                <input type="number" name="price_sale" 
                                       class="form-control {{ isset($errors['price_sale']) ? 'is-invalid' : '' }}" 
                                       value="{{ $product['price_sale'] }}">
                                @if(isset($errors['price_sale']))
                                    <div class="invalid-feedback">{{ $errors['price_sale'] }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mô tả ngắn</label>
                            <textarea name="description" class="form-control" rows="3">{{ $product['description'] }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nội dung chi tiết</label>
                            <textarea name="content" class="form-control" rows="5">{{ $product['content'] }}</textarea>
                        </div>
                    </div>

                    <!-- 2. Ảnh đại diện & Gallery -->
                    <div class="col-md-4">
                        <div class="card bg-light mb-3">
                            <div class="card-body text-center">
                                <label class="form-label fw-bold">Ảnh đại diện</label>
                                @if($product['img_thumbnail'])
                                    <div class="mb-2">
                                        <img src="/storage/uploads/products/{{ $product['img_thumbnail'] }}" class="img-thumbnail" style="height: 150px;">
                                    </div>
                                @endif
                                <input type="file" name="img_thumbnail" class="form-control" accept="image/*">
                            </div>
                        </div>
                        
                        <!-- Gallery Hiện Tại -->
                        <div class="card bg-light">
                            <div class="card-body">
                                <label class="form-label fw-bold">Gallery hiện tại</label>
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    @foreach($product['gallery'] as $img)
                                        <div class="position-relative" style="width: 60px; height: 60px;">
                                            <img src="/storage/uploads/gallery/{{ $img['image_path'] }}" class="w-100 h-100 object-fit-cover rounded border">
                                            <div class="form-check position-absolute top-0 end-0 m-0">
                                                <input class="form-check-input bg-danger border-0" type="checkbox" name="delete_gallery_ids[]" value="{{ $img['id'] }}" title="Tick để xóa ảnh này">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <label class="form-label small text-muted">Thêm ảnh mới vào Gallery:</label>
                                <input type="file" name="gallery[]" class="form-control" multiple accept="image/*">
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
                                    <th>Thuộc tính</th>
                                    <th>Giá</th>
                                    <th>Số lượng</th>
                                    <th>Ảnh</th>
                                    <th>Xóa</th>
                                </tr>
                            </thead>
                            <tbody id="variantContainer">
                                @foreach($product['variants'] as $var)
                                    @php 
                                        $attr = json_decode($var['attributes'], true);
                                    @endphp
                                    <tr>
                                        <td><input type="text" name="variant_sku[]" class="form-control form-control-sm" value="{{ $var['sku'] }}" required></td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <input type="text" name="variant_color[]" class="form-control form-control-sm" value="{{ $attr['Color'] ?? '' }}" placeholder="Màu">
                                                <input type="text" name="variant_size[]" class="form-control form-control-sm" value="{{ $attr['Size'] ?? '' }}" placeholder="Size">
                                            </div>
                                        </td>
                                        <td><input type="number" name="variant_price[]" class="form-control form-control-sm" value="{{ $var['price'] }}"></td>
                                        <td><input type="number" name="variant_qty[]" class="form-control form-control-sm" value="{{ $var['quantity'] }}"></td>
                                        <td>
                                            @if($var['image'])
                                                <img src="/storage/uploads/variants/{{ $var['image'] }}" width="30" height="30" class="me-1">
                                                <input type="hidden" name="existing_variant_image[]" value="{{ $var['image'] }}">
                                            @endif
                                            <input type="file" name="variant_image[]" class="form-control form-control-sm d-inline-block" style="width: 150px;">
                                        </td>
                                        <td><button type="button" class="btn btn-danger btn-sm btn-remove"><i class="fa-solid fa-trash"></i></button></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="/product/index" class="btn btn-secondary">Hủy</a>
                    <button type="submit" class="btn btn-warning px-4">Cập nhật</button>
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
            <td>
                <input type="hidden" name="existing_variant_image[]" value="">
                <input type="file" name="variant_image[]" class="form-control form-control-sm">
            </td>
            <td><button type="button" class="btn btn-danger btn-sm btn-remove"><i class="fa-solid fa-trash"></i></button></td>
        `;
        document.getElementById('variantContainer').appendChild(tr);
    });

    document.getElementById('variantContainer').addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove')) {
            if(confirm('Xóa biến thể này?')) {
                e.target.closest('tr').remove();
            }
        }
    });
</script>
@endsection