@extends('layouts.admin')

@section('title', $title ?? 'Quản lý Sản phẩm')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Breadcrumb & Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mt-0 mb-2">Quản Lý Sản Phẩm</h1>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item active">Sản phẩm</li>
            </ol>
        </div>
        <a href="/product/create" class="btn btn-dark shadow-sm px-4" style="text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">
            <i class="fa-solid fa-plus me-2"></i>Thêm mới
        </a>
    </div>

    <!-- Thông báo SweetAlert Success -->
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

    <!-- Bảng dữ liệu sản phẩm -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <i class="fa-solid fa-box me-1 text-muted"></i> Danh mục sản phẩm hiện có
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center mb-0">
                    <thead class="bg-light" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <tr>
                            <th width="5%" class="py-3 text-muted">#</th>
                            <th width="8%" class="py-3 text-muted">Ảnh</th>
                            <th width="32%" class="py-3 text-muted text-start">Thông tin sản phẩm</th>
                            <th width="15%" class="py-3 text-muted text-end pe-4">Giá bán</th>
                            <th width="12%" class="py-3 text-muted">Trạng thái</th>
                            <th width="15%" class="py-3 text-muted">Ngày tạo</th>
                            <th width="13%" class="py-3 text-muted">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($products))
                            @foreach($products as $product)
                            <tr>
                                <td class="fw-bold text-muted">{{ $product['id'] }}</td>
                                
                                <td class="btn-show-detail" data-id="{{ $product['id'] }}" style="cursor: pointer;">
                                    @if($product['img_thumbnail'])
                                        <img src="/storage/uploads/products/{{ $product['img_thumbnail'] }}" 
                                             class="rounded border shadow-sm object-fit-cover" 
                                             width="50" height="65" 
                                             alt="Thumb">
                                    @else
                                        <div class="bg-light border rounded d-flex align-items-center justify-content-center text-muted" style="width: 50px; height: 65px;">
                                            <i class="fa-regular fa-image"></i>
                                        </div>
                                    @endif
                                </td>

                                <td class="text-start btn-show-detail" data-id="{{ $product['id'] }}" style="cursor: pointer;">
                                    <h6 class="mb-1 fw-bold text-dark" style="font-family: var(--font-base);">{{ $product['name'] }}</h6>
                                    <div class="d-flex gap-1 flex-wrap">
                                        <span class="badge bg-light text-dark border fw-normal" style="font-size: 0.7rem;">
                                            <i class="fa-solid fa-layer-group me-1 opacity-50"></i>{{ $product['category_name'] ?? 'Chưa phân loại' }}
                                        </span>
                                        <span class="badge bg-light text-dark border fw-normal" style="font-size: 0.7rem;">
                                            <i class="fa-solid fa-tag me-1 opacity-50"></i>{{ $product['brand_name'] ?? 'MENSWEAR' }}
                                        </span>
                                    </div>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Slug: {{ $product['slug'] }}</small>
                                </td>

                                <td class="text-end pe-4">
                                    @if($product['price_sale'] > 0)
                                        <div class="text-danger fw-bold">{{ number_format($product['price_sale'], 0, ',', '.') }} đ</div>
                                        <del class="text-muted small" style="font-size: 0.75rem;">{{ number_format($product['price_regular'], 0, ',', '.') }} đ</del>
                                    @else
                                        <div class="text-dark fw-bold">{{ number_format($product['price_regular'], 0, ',', '.') }} đ</div>
                                    @endif
                                </td>

                                <td>
                                    @if($product['status'] == 'active')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">ĐANG BÁN</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">NGỪNG BÁN</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="text-muted small">
                                        <i class="fa-regular fa-calendar-check me-1"></i>{{ date('d/m/Y', strtotime($product['created_at'])) }}
                                    </span>
                                </td>

                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="/product/edit/{{ $product['id'] }}" class="btn btn-sm btn-outline-dark" title="Chỉnh sửa" style="width: 32px; height: 32px; padding: 0; line-height: 30px;">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        
                                        <button class="btn btn-sm btn-outline-danger" 
                                                data-id="{{ $product['id'] }}"
                                                data-name="{{ $product['name'] }}"
                                                onclick="confirmDelete(this)" 
                                                title="Xóa" style="width: 32px; height: 32px; padding: 0; line-height: 30px;">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fa-solid fa-box-open fs-1 mb-3 opacity-25"></i>
                                        <h5 class="fw-normal">Kho hàng đang trống</h5>
                                        <p class="small mb-3">Bắt đầu thêm sản phẩm đầu tiên của bạn.</p>
                                        <a href="/product/create" class="btn btn-dark btn-sm px-3">Thêm sản phẩm</a>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Form ẩn xử lý xoá -->
<form id="deleteProductForm" method="POST" action="/product/delete" style="display: none;">
    <input type="hidden" name="delete_id" id="deleteProductIdInput">
</form>

<!-- Modal Chi tiết sản phẩm (Nâng cấp) -->
<div class="modal fade" id="productDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-0">
            <div class="modal-header bg-dark text-white rounded-0 py-3">
                <h5 class="modal-title font-heading text-uppercase" style="letter-spacing: 1px;"><i class="fa-solid fa-magnifying-glass me-2"></i>Chi tiết sản phẩm</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="modalContent">
                <!-- Nội dung được nạp qua AJAX -->
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Lắng nghe sự kiện click xem chi tiết
        const detailButtons = document.querySelectorAll('.btn-show-detail');
        detailButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                if (id) showDetail(id);
            });
        });
    });

    function confirmDelete(el) {
        const id = el.getAttribute('data-id');
        const name = el.getAttribute('data-name');

        Swal.fire({
            title: 'Xóa sản phẩm?',
            html: "Bạn có chắc chắn muốn xóa <b>" + name + "</b>?<br>Dữ liệu đã xóa sẽ không thể khôi phục!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#111',
            confirmButtonText: 'XÓA NGAY',
            cancelButtonText: 'HỦY BỎ'
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
        container.innerHTML = `<div class="d-flex justify-content-center align-items-center py-5"><div class="spinner-border text-dark" role="status"></div></div>`;

        fetch(`/product/show/${id}`)
            .then(response => response.json())
            .then(res => {
                if (res.status === 'success') {
                    const p = res.data;
                    
                    // Xử lý HTML biến thể
                    let variantsHtml = '';
                    if (p.variants && p.variants.length > 0) {
                        const rows = p.variants.map(v => {
                            let attrStr = '';
                            if (v.attributes) {
                                try {
                                    const attrObj = typeof v.attributes === 'string' ? JSON.parse(v.attributes) : v.attributes;
                                    if(attrObj.Color) attrStr += `<span class="badge bg-light text-dark border me-1">Màu: ${attrObj.Color}</span>`;
                                    if(attrObj.Size) attrStr += `<span class="badge bg-light text-dark border">Size: ${attrObj.Size}</span>`;
                                } catch(e) {}
                            } 
                            const imgV = v.image ? `/storage/uploads/variants/${v.image}` : 'https://placehold.co/40x53?text=No+Img';
                            return `<tr>
                                <td class="text-center"><img src="${imgV}" width="40" height="53" class="rounded border object-fit-cover shadow-sm"></td>
                                <td><code>${v.sku}</code></td>
                                <td>${attrStr || 'N/A'}</td>
                                <td class="fw-bold text-dark text-end">${new Intl.NumberFormat('vi-VN').format(v.price)} đ</td>
                                <td class="text-center"><span class="badge ${v.quantity > 0 ? 'bg-success' : 'bg-danger'}">${v.quantity}</span></td>
                            </tr>`;
                        }).join('');

                        variantsHtml = `<div class="mt-4">
                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3 text-uppercase" style="font-size: 0.85rem; letter-spacing: 1px;">Phiên bản & Tồn kho</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.9rem;">
                                    <thead class="bg-light"><tr><th>Ảnh</th><th>SKU</th><th>Thuộc tính</th><th class="text-end">Giá bán</th><th class="text-center">Kho</th></tr></thead>
                                    <tbody>${rows}</tbody>
                                </table>
                            </div>
                        </div>`;
                    }

                    // Xử lý Gallery
                    let galleryHtml = '';
                    if (p.gallery && p.gallery.length > 0) {
                        const imgs = p.gallery.map(img => `<div class="col-3 mb-2"><img src="/storage/uploads/gallery/${img.image_path}" class="img-thumbnail w-100 shadow-sm" style="height: 70px; object-fit: cover;"></div>`).join('');
                        galleryHtml = `<div class="mt-4"><h6 class="fw-bold text-dark border-bottom pb-2 mb-3 text-uppercase" style="font-size: 0.85rem; letter-spacing: 1px;">Bộ sưu tập ảnh</h6><div class="row gx-2">${imgs}</div></div>`;
                    }

                    container.innerHTML = `
                        <div class="row">
                            <div class="col-md-4 border-end">
                                <img src="/storage/uploads/products/${p.img_thumbnail}" class="img-fluid rounded border shadow-sm w-100" onerror="this.src='https://placehold.co/300x400?text=No+Image'">
                                ${galleryHtml}
                            </div>
                            <div class="col-md-8 ps-md-4">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h3 class="fw-bold text-dark mb-1 font-heading">${p.name}</h3>
                                        <span class="badge bg-dark px-3 py-2" style="font-size: 0.75rem; letter-spacing: 1px;">ID: #${p.id}</span>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge ${p.status === 'active' ? 'bg-success' : 'bg-secondary'} mb-2 d-block">${p.status === 'active' ? 'ĐANG BÁN' : 'NGỪNG BÁN'}</span>
                                        <small class="text-muted">Ngày tạo: ${dateFormatted(p.created_at)}</small>
                                    </div>
                                </div>
                                <hr class="opacity-50">
                                <div class="row mb-3">
                                    <div class="col-6"><small class="text-muted d-block text-uppercase">Danh mục:</small><span class="fw-medium">${p.category_name || 'N/A'}</span></div>
                                    <div class="col-6"><small class="text-muted d-block text-uppercase">Thương hiệu:</small><span class="fw-medium">${p.brand_name || 'N/A'}</span></div>
                                </div>
                                <div class="bg-light p-3 border mb-4">
                                    <small class="text-muted text-uppercase d-block mb-1">Giá hệ thống:</small>
                                    ${p.price_sale > 0 
                                        ? `<span class="h4 fw-bold text-danger me-2">${new Intl.NumberFormat('vi-VN').format(p.price_sale)} đ</span><del class="text-muted">${new Intl.NumberFormat('vi-VN').format(p.price_regular)} đ</del>` 
                                        : `<span class="h4 fw-bold text-dark">${new Intl.NumberFormat('vi-VN').format(p.price_regular)} đ</span>`}
                                </div>
                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark text-uppercase" style="font-size: 0.85rem; letter-spacing: 1px;">Mô tả sản phẩm:</h6>
                                    <p class="text-muted small mb-0" style="line-height: 1.6;">${p.description || 'Không có mô tả cho sản phẩm này.'}</p>
                                </div>
                                ${variantsHtml}
                            </div>
                        </div>`;
                } else {
                    container.innerHTML = `<div class="alert alert-danger text-center"><i class="fa-solid fa-triangle-exclamation me-2"></i>Không thể tải dữ liệu!</div>`;
                }
            })
            .catch(err => {
                console.error(err);
                container.innerHTML = `<div class="alert alert-danger text-center">Lỗi kết nối API!</div>`;
            });
    }

    function dateFormatted(dateStr) {
        const d = new Date(dateStr);
        return d.toLocaleDateString('vi-VN');
    }
</script>
@endsection