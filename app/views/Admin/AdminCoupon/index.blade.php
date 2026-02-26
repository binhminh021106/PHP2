@extends('layouts.admin')

@section('title', $title ?? 'Quản lý Mã Giảm Giá')

@section('content')

<style>
    /* Tuỳ chỉnh giao diện Phân trang (Pagination) */
    .custom-pagination .page-link {
        color: var(--color-dark, #111);
        border: 1px solid #eee;
        margin: 0 4px;
        border-radius: 0 !important;
        padding: 8px 16px;
        transition: all 0.3s ease;
        font-weight: 500;
        font-size: 0.9rem;
    }
    .custom-pagination .page-item.active .page-link {
        background-color: var(--color-dark, #111);
        border-color: var(--color-dark, #111);
        color: white;
    }
    .custom-pagination .page-link:hover {
        background-color: var(--color-accent, #c9a47c);
        border-color: var(--color-accent, #c9a47c);
        color: white;
    }
    .custom-pagination .page-item.disabled .page-link {
        color: #bbb;
        background-color: #fafafa;
        border-color: #eee;
    }
</style>

<div class="container-fluid px-4 py-4">
    <!-- Breadcrumb & Header -->
    <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-3">
        <div>
            <h1 class="mt-0 mb-2">Quản Lý Mã Giảm Giá</h1>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item active">Mã giảm giá</li>
            </ol>
        </div>
        
        <div class="d-flex gap-2 align-items-center">
            <!-- Search Form -->
            <form action="" method="GET" class="d-flex gap-2">
                <div class="input-group shadow-sm">
                    <input type="text" name="search" class="form-control rounded-0 border-dark" placeholder="Tìm theo mã code..." value="{{ $search ?? '' }}">
                    <button class="btn btn-dark rounded-0 px-3" type="submit" title="Tìm kiếm">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
                @if(!empty($search))
                    <a href="/coupon/index" class="btn btn-outline-dark rounded-0 d-flex align-items-center" title="Xóa bộ lọc">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </form>

            <a href="/coupon/create" class="btn btn-dark shadow-sm px-4 rounded-0 d-flex align-items-center" style="text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; height: 38px;">
                <i class="fa-solid fa-plus me-2"></i>Thêm mới
            </a>
        </div>
    </div>

    <!-- Hiển thị thông báo tìm kiếm -->
    <div class="mb-3 text-muted small" style="letter-spacing: 0.5px;">
        <span>Tìm thấy <b class="text-dark">{{ $total_records ?? 0 }}</b> mã giảm giá @if(!empty($search)) với từ khóa "<b class="text-dark">{{ $search }}</b>" @endif</span>
    </div>

    <!-- SweetAlert Success Notification -->
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

    <!-- Bảng dữ liệu -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <i class="fa-solid fa-ticket-simple me-1 text-muted"></i> Danh sách mã giảm giá
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center mb-0">
                    <thead class="bg-light" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <tr>
                            <th width="5%" class="py-3 text-muted">#</th>
                            <th width="20%" class="py-3 text-muted text-start">Mã Code</th>
                            <th width="15%" class="py-3 text-muted">Loại & Giá trị</th>
                            <th width="10%" class="py-3 text-muted">Số lượng</th>
                            <th width="20%" class="py-3 text-muted">Ngày hết hạn</th>
                            <th width="15%" class="py-3 text-muted">Trạng thái</th>
                            <th width="15%" class="py-3 text-muted">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($coupons))
                            @foreach($coupons as $coupon)
                            <tr>
                                <td class="fw-bold text-muted">{{ $coupon['id'] }}</td>

                                <td class="text-start">
                                    <span class="badge bg-dark" style="padding: 8px 15px; font-size: 0.95rem; letter-spacing: 1px; font-family: var(--font-base);">
                                        {{ $coupon['code'] }}
                                    </span>
                                </td>

                                <td>
                                    @if($coupon['type'] == 'percent')
                                        <span class="text-danger fw-bold fs-6">-{{ $coupon['value'] }}%</span>
                                    @else
                                        <span class="text-success fw-bold fs-6">-{{ number_format($coupon['value']) }} đ</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="fw-medium text-dark">{{ $coupon['quantity'] }}</span>
                                </td>

                                <td>
                                    <span class="text-dark fw-medium d-block">{{ date('d/m/Y', strtotime($coupon['expired_at'])) }}</span>
                                    <span class="small text-muted"><i class="fa-regular fa-clock me-1"></i>{{ date('H:i', strtotime($coupon['expired_at'])) }}</span>
                                </td>

                                <td>
                                    @if($coupon['status'] == 'active')
                                        <span class="badge bg-success" style="padding: 6px 12px; font-weight: 500; letter-spacing: 0.5px;">HOẠT ĐỘNG</span>
                                    @else
                                        <span class="badge bg-secondary" style="padding: 6px 12px; font-weight: 500; letter-spacing: 0.5px;">TẠM KHÓA</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="/coupon/edit/{{ $coupon['id'] }}" class="btn btn-sm btn-outline-dark" title="Sửa" style="width: 32px; height: 32px; padding: 0; line-height: 30px;">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>

                                        <button class="btn btn-sm btn-outline-danger"
                                            style="width: 32px; height: 32px; padding: 0; line-height: 30px;"
                                            data-id="{{ $coupon['id'] }}"
                                            data-name="{{ $coupon['code'] }}"
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
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fa-solid fa-ticket-simple fs-1 mb-3 opacity-25"></i>
                                        <h5 class="fw-normal">Chưa có mã giảm giá nào</h5>
                                        <p class="small mb-3">Tạo các mã khuyến mãi để kích thích mua sắm.</p>
                                        <a href="/coupon/create" class="btn btn-dark btn-sm px-3">Thêm mới ngay</a>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Pagination (Phân trang) -->
            @if(isset($total_pages) && $total_pages > 1)
            <div class="d-flex justify-content-center border-top py-4 bg-white">
                <nav aria-label="Page navigation">
                    <ul class="pagination custom-pagination mb-0">
                        <!-- Nút Previous -->
                        <li class="page-item {{ $current_page <= 1 ? 'disabled' : '' }}">
                            <a class="page-link" href="?page={{ $current_page - 1 }}&search={{ $search }}" aria-label="Previous">
                                <i class="fa-solid fa-angle-left"></i>
                            </a>
                        </li>

                        <!-- Các số trang -->
                        @for($i = 1; $i <= $total_pages; $i++)
                        <li class="page-item {{ $current_page == $i ? 'active' : '' }}">
                            <a class="page-link" href="?page={{ $i }}&search={{ $search }}">{{ $i }}</a>
                        </li>
                        @endfor

                        <!-- Nút Next -->
                        <li class="page-item {{ $current_page >= $total_pages ? 'disabled' : '' }}">
                            <a class="page-link" href="?page={{ $current_page + 1 }}&search={{ $search }}" aria-label="Next">
                                <i class="fa-solid fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Form ẩn để xoá -->
<form id="deleteCouponForm" method="POST" action="/coupon/delete" style="display: none;">
    <input type="hidden" name="delete_id" id="deleteCouponIdInput">
</form>

@endsection

@section('scripts')
<script>
    function confirmDelete(el) {
        var id = el.getAttribute('data-id');
        var name = el.getAttribute('data-name');

        Swal.fire({
            title: 'Xóa mã giảm giá?',
            html: "Bạn có chắc chắn muốn xóa mã <b>" + name + "</b>?<br>Hành động này không thể hoàn tác!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545', // Màu đỏ nguy hiểm
            cancelButtonColor: '#111',     // Màu đen chuẩn concept
            confirmButtonText: '<i class="fa-solid fa-trash me-1"></i> Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteCouponIdInput').value = id;
                document.getElementById('deleteCouponForm').submit();
            }
        });
    }
</script>
@endsection