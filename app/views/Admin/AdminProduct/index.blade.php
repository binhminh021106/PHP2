@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold text-primary"><i class="fa-solid fa-box-open me-2"></i>Quản lý Sản phẩm</h4>
        <a href="/product/create" class="btn btn-primary">
            <i class="fa-solid fa-plus me-2"></i>Thêm mới
        </a>
    </div>

    @if(!empty($success_msg))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Thành công!',
                text: "{{ $success_msg }}",
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        });
    </script>
    @endif

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
                                
                                <!-- CÁCH MỚI: Dùng class và data-id, KHÔNG dùng onclick -->
                                <td class="btn-show-detail" data-id="{{ $product['id'] }}" style="cursor: pointer;" title="Xem chi tiết">
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

                                <!-- CÁCH MỚI: Dùng class và data-id -->
                                <td class="btn-show-detail" data-id="{{ $product['id'] }}" style="cursor: pointer;" title="Xem chi tiết">
                                    <h6 class="mb-1 fw-bold text-dark">{{ $product['name'] }}</h6>
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info">
                                        {{ $product['category_name'] ?? 'Chưa phân loại' }}
                                    </span>
                                    <small class="d-block text-muted mt-1" style="font-size: 0.8rem;">Slug: {{ $product['slug'] }}</small>
                                </td>

                                <td>
                                    @if($product['price_sale'] > 0)
                                        <div class="text-danger fw-bold">{{ number_format($product['price_sale']) }} đ</div>
                                        <div class="text-muted text-decoration-line-through small">{{ number_format($product['price_regular']) }} đ</div>
                                    @else
                                        <div class="text-dark fw-bold">{{ number_format($product['price_regular']) }} đ</div>
                                    @endif
                                </td>

                                <td class="text-center">
                                    @if($product['status'] == 'active')
                                        <span class="badge rounded-pill bg-success">Đang bán</span>
                                    @else
                                        <span class="badge rounded-pill bg-secondary">Ngừng bán</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="text-muted small">
                                        <i class="fa-regular fa-clock me-1"></i>
                                        {{ date('d/m/Y', strtotime($product['created_at'])) }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="/product/edit/{{ $product['id'] }}" class="btn btn-sm btn-outline-warning" title="Sửa">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        
                                        <button class="btn btn-sm btn-outline-danger" 
                                                data-id="{{ $product['id'] }}"
                                                data-name="{{ $product['name'] }}"
                                                onclick="confirmDelete(this)" 
                                                title="Xóa">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
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

<form id="deleteProductForm" method="POST" action="/product/delete" style="display: none;">
    <input type="hidden" name="delete_id" id="deleteProductIdInput">
</form>

<div class="modal fade" id="productDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fa-solid fa-box me-2"></i>Chi tiết sản phẩm</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalContent" style="min-height: 200px;">
                <div class="d-flex justify-content-center align-items-center h-100 py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // 1. Lắng nghe sự kiện click cho các phần tử xem chi tiết
    document.addEventListener('DOMContentLoaded', function() {
        // Lấy tất cả các phần tử có class 'btn-show-detail'
        const detailButtons = document.querySelectorAll('.btn-show-detail');
        
        detailButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Lấy ID từ data-attribute
                const id = this.getAttribute('data-id');
                if (id) {
                    showDetail(id);
                }
            });
        });
    });

    function confirmDelete(el) {
        var id = el.getAttribute('data-id');
        var name = el.getAttribute('data-name');

        Swal.fire({
            title: 'Bạn chắc chắn chứ?',
            text: "Xóa sản phẩm '" + name + "' sẽ không thể hoàn tác!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Vẫn xóa!',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteProductIdInput').value = id;
                document.getElementById('deleteProductForm').submit();
            }
        });
    }

    function showDetail(id) {
        const modalEl = document.getElementById('productDetailModal');
        const modal = new bootstrap.Modal(modalEl);
        const container = document.getElementById('modalContent');
        
        modal.show();
        
        container.innerHTML = `
            <div class="d-flex justify-content-center align-items-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
            </div>`;

        fetch(`/product/show/${id}`)
            .then(response => response.json())
            .then(res => {
                if (res.status === 'success') {
                    const p = res.data;
                    
                    let variantsHtml = '';
                    if (p.variants && p.variants.length > 0) {
                        const rows = p.variants.map(v => {
                            let attrStr = '';
                            if (typeof v.attributes === 'object') {
                                if(v.attributes.Color) attrStr += `<span class="badge bg-light text-dark border me-1">Màu: ${v.attributes.Color}</span>`;
                                if(v.attributes.Size) attrStr += `<span class="badge bg-light text-dark border">Size: ${v.attributes.Size}</span>`;
                            } 
                            
                            const imgUrl = v.image ? `/storage/uploads/variants/${v.image}` : 'https://placehold.co/40x40?text=No+Img';
                            
                            return `
                                <tr>
                                    <td class="text-center"><img src="${imgUrl}" width="40" height="40" class="rounded object-fit-cover"></td>
                                    <td><code>${v.sku}</code></td>
                                    <td>${attrStr}</td>
                                    <td class="fw-bold text-end">${new Intl.NumberFormat('vi-VN').format(v.price)} đ</td>
                                    <td class="text-center">${v.quantity}</td>
                                </tr>
                            `;
                        }).join('');

                        variantsHtml = `
                            <div class="mt-4">
                                <h6 class="fw-bold text-dark border-bottom pb-2"><i class="fa-solid fa-layer-group me-2"></i>Danh sách biến thể (${p.variants.length})</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center" width="10%">Ảnh</th>
                                                <th width="20%">SKU</th>
                                                <th width="30%">Thuộc tính</th>
                                                <th class="text-end" width="20%">Giá</th>
                                                <th class="text-center" width="20%">Tồn kho</th>
                                            </tr>
                                        </thead>
                                        <tbody>${rows}</tbody>
                                    </table>
                                </div>
                            </div>`;
                    } else {
                        variantsHtml = `<div class="mt-3 text-muted fst-italic">Sản phẩm này không có biến thể.</div>`;
                    }

                    let galleryHtml = '';
                    if (p.gallery && p.gallery.length > 0) {
                        const imgs = p.gallery.map(img => `
                            <div class="col-3 col-md-3 mb-2">
                                <img src="/storage/uploads/gallery/${img.image_path}" class="img-thumbnail w-100" style="height: 80px; object-fit: cover;">
                            </div>
                        `).join('');
                        
                        galleryHtml = `
                            <div class="mt-4">
                                <h6 class="fw-bold text-dark border-bottom pb-2"><i class="fa-regular fa-images me-2"></i>Thư viện ảnh</h6>
                                <div class="row gx-2">${imgs}</div>
                            </div>`;
                    }

                    container.innerHTML = `
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card border-0">
                                    <img src="/storage/uploads/products/${p.img_thumbnail}" 
                                         class="card-img-top rounded shadow-sm border" 
                                         style="max-height: 300px; object-fit: cover;" 
                                         onerror="this.src='https://placehold.co/300x300?text=No+Image'">
                                </div>
                                ${galleryHtml}
                            </div>

                            <div class="col-md-8">
                                <h4 class="fw-bold text-primary">${p.name}</h4>
                                <div class="mb-2">
                                    <span class="badge bg-info text-dark">${p.category_name || 'Chưa phân loại'}</span>
                                    <span class="text-muted small ms-2"><i class="fa-solid fa-link me-1"></i>${p.slug}</span>
                                </div>

                                <div class="d-flex align-items-center gap-3 mt-3">
                                    ${p.price_sale > 0 
                                        ? `<h5 class="text-danger fw-bold mb-0">${new Intl.NumberFormat('vi-VN').format(p.price_sale)} đ</h5>
                                           <span class="text-muted text-decoration-line-through">${new Intl.NumberFormat('vi-VN').format(p.price_regular)} đ</span>`
                                        : `<h5 class="text-dark fw-bold mb-0">${new Intl.NumberFormat('vi-VN').format(p.price_regular)} đ</h5>`
                                    }
                                    <div class="ms-auto">
                                        ${p.status === 'active' 
                                            ? '<span class="badge bg-success">Đang bán</span>' 
                                            : '<span class="badge bg-secondary">Ngừng bán</span>'}
                                    </div>
                                </div>

                                <div class="bg-light p-3 rounded mt-3">
                                    <h6 class="fw-bold mb-2">Mô tả ngắn:</h6>
                                    <p class="mb-0 text-secondary" style="font-size: 0.95rem;">${p.description || 'Chưa cập nhật mô tả.'}</p>
                                </div>

                                ${variantsHtml}
                            </div>
                        </div>
                    `;
                } else {
                    container.innerHTML = `<div class="alert alert-danger text-center"><i class="fa-solid fa-triangle-exclamation me-2"></i>${res.message}</div>`;
                }
            })
            .catch(err => {
                console.error(err);
                container.innerHTML = `<div class="alert alert-danger text-center">Lỗi kết nối máy chủ! Vui lòng thử lại.</div>`;
            });
    }
</script>
@endsection