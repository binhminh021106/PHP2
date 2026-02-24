@extends('layouts.admin')

@section('title', $title ?? 'Quản lý Mã Giảm Giá')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Breadcrumb & Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mt-0 mb-2">Quản Lý Mã Giảm Giá</h1>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item active">Mã giảm giá</li>
            </ol>
        </div>
        <a href="/coupon/create" class="btn btn-dark shadow-sm px-4" style="text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">
            <i class="fa-solid fa-plus me-2"></i>Thêm mới
        </a>
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