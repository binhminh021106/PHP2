@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold text-primary"><i class="fa-solid fa-ticket me-2"></i>Quản lý Mã Giảm Giá</h4>
        <a href="/coupon/create" class="btn btn-primary">
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
                            <th width="20%">Mã Code</th>
                            <th width="15%">Loại & Giá trị</th>
                            <th width="10%">Số lượng</th>
                            <th width="20%">Ngày hết hạn</th>
                            <th width="15%" class="text-center">Trạng thái</th>
                            <th width="15%" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($coupons))
                        @foreach($coupons as $coupon)
                        <tr>
                            <td class="text-center fw-bold text-muted">{{ $coupon['id'] }}</td>

                            <td>
                                <span class="badge bg-primary fs-6">{{ $coupon['code'] }}</span>
                            </td>

                            <td>
                                @if($coupon['type'] == 'percent')
                                    <span class="text-danger fw-bold">-{{ $coupon['value'] }}%</span>
                                @else
                                    <span class="text-success fw-bold">-{{ number_format($coupon['value']) }} đ</span>
                                @endif
                            </td>

                            <td>{{ $coupon['quantity'] }}</td>

                            <td>
                                <i class="fa-regular fa-calendar-xmark me-1 text-muted"></i>
                                {{ date('d/m/Y H:i', strtotime($coupon['expired_at'])) }}
                            </td>

                            <td class="text-center">
                                @if($coupon['status'] == 'active')
                                <span class="badge rounded-pill bg-success">Hoạt động</span>
                                @else
                                <span class="badge rounded-pill bg-secondary">Tạm khóa</span>
                                @endif
                            </td>

                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="/coupon/edit/{{ $coupon['id'] }}" class="btn btn-sm btn-outline-warning" title="Sửa">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>

                                    <button class="btn btn-sm btn-outline-danger"
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
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fa-solid fa-ticket fs-2 mb-2 d-block"></i>
                                Chưa có mã giảm giá nào.
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

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
            title: 'Bạn chắc chắn chứ?',
            text: "Xóa mã coupon '" + name + "' sẽ không thể hoàn tác!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Vẫn xóa!',
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