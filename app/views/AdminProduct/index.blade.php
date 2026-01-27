@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container-fluid py-4">
    <!-- Header & Nút Thêm -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold text-primary"><i class="fa-solid fa-box-open me-2"></i>Quản lý Sản phẩm</h4>
        <a href="/product/create" class="btn btn-primary">
            <i class="fa-solid fa-plus me-2"></i>Thêm mới
        </a>
    </div>

    <!-- Thông báo thành công/lỗi -->
    @if(isset($_SESSION['success']))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-check-circle me-2"></i>{{ $_SESSION['success'] }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            @php unset($_SESSION['success']); @endphp
        </div>
    @endif

    @if(isset($_SESSION['error']))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ $_SESSION['error'] }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            @php unset($_SESSION['error']); @endphp
        </div>
    @endif

    <!-- Bảng dữ liệu -->
    <div class="card shadow border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th width="5%" class="text-center">#</th>
                            <th width="10%">Ảnh</th>
                            <th width="30%">Tên sản phẩm / Danh mục</th>
                            <th width="15%">Giá bán</th>
                            <th width="15%" class="text-center">Trạng thái</th>
                            <th width="15%">Ngày tạo</th>
                            <th width="10%" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($products))
                            @foreach($products as $product)
                            <tr>
                                <td class="text-center fw-bold text-muted">{{ $product['id'] }}</td>
                                
                                <!-- Ảnh Thumbnail -->
                                <td>
                                    @if($product['img_thumbnail'])
                                        <img src="/storage/uploads/products/{{ $product['img_thumbnail'] }}" 
                                             class="rounded border object-fit-cover" 
                                             width="60" height="60" 
                                             alt="Thumbnail">
                                    @else
                                        <div class="bg-secondary bg-opacity-10 rounded d-flex align-items-center justify-content-center text-muted" style="width: 60px; height: 60px;">
                                            <i class="fa-regular fa-image"></i>
                                        </div>
                                    @endif
                                </td>

                                <!-- Tên & Danh mục -->
                                <td>
                                    <h6 class="mb-1 fw-bold text-dark">{{ $product['name'] }}</h6>
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info">
                                        {{ $product['category_name'] ?? 'Chưa phân loại' }}
                                    </span>
                                    <small class="d-block text-muted mt-1" style="font-size: 0.8rem;">Slug: {{ $product['slug'] }}</small>
                                </td>

                                <!-- Giá -->
                                <td>
                                    @if($product['price_sale'] > 0)
                                        <div class="text-danger fw-bold">{{ number_format($product['price_sale']) }} đ</div>
                                        <div class="text-muted text-decoration-line-through small">{{ number_format($product['price_regular']) }} đ</div>
                                    @else
                                        <div class="text-dark fw-bold">{{ number_format($product['price_regular']) }} đ</div>
                                    @endif
                                </td>

                                <!-- Trạng thái -->
                                <td class="text-center">
                                    @if($product['status'] == 'active')
                                        <span class="badge rounded-pill bg-success">Đang bán</span>
                                    @else
                                        <span class="badge rounded-pill bg-secondary">Ngừng bán</span>
                                    @endif
                                </td>

                                <!-- Ngày tạo -->
                                <td>
                                    <span class="text-muted small">
                                        <i class="fa-regular fa-clock me-1"></i>
                                        {{ date('d/m/Y', strtotime($product['created_at'])) }}
                                    </span>
                                </td>

                                <!-- Hành động -->
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="/product/edit/{{ $product['id'] }}" class="btn btn-sm btn-outline-warning" title="Sửa">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        
                                        <!-- Form xóa -->
                                        <form action="/product/delete" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">
                                            <input type="hidden" name="delete_id" value="{{ $product['id'] }}">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fa-solid fa-box-open fs-2 mb-2 d-block"></i>
                                    Chưa có sản phẩm nào.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection