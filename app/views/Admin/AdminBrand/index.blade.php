@extends('layouts.admin')

@section('title', $title ?? 'Quản lý Thương hiệu')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Breadcrumb & Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mt-0 mb-2">Quản Lý Thương Hiệu</h1>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item active">Thương hiệu</li>
            </ol>
        </div>
        <a href="/brand/create" class="btn btn-dark shadow-sm px-4" style="text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">
            <i class="fa-solid fa-plus me-2"></i>Thêm mới
        </a>
    </div>

    <!-- Thông báo SweetAlert2 -->
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
            <i class="fa-solid fa-tags me-1 text-muted"></i> Danh sách thương hiệu
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center mb-0">
                    <thead class="bg-light" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <tr>
                            <th width="5%" class="py-3 text-muted">#</th>
                            <th width="15%" class="py-3 text-muted">Logo</th>
                            <th width="25%" class="py-3 text-muted text-start">Tên Thương Hiệu</th>
                            <th width="30%" class="py-3 text-muted text-start">Mô Tả</th>
                            <th width="15%" class="py-3 text-muted">Trạng thái</th>
                            <th width="10%" class="py-3 text-muted">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!empty($brand))
                            @foreach ($brand as $item)
                            <tr>
                                <td class="fw-bold text-muted">{{ $item['id'] }}</td>
                                
                                <td>
                                    @if (!empty($item['image']))
                                        <div class="d-inline-block p-1 border rounded bg-white">
                                            <img src="/storage/uploads/brands/{{ $item['image'] }}" 
                                                 class="object-fit-contain" 
                                                 style="width: 50px; height: 50px;" 
                                                 alt="Logo">
                                        </div>
                                    @else
                                        <div class="bg-light rounded d-inline-flex align-items-center justify-content-center text-muted" style="width: 58px; height: 58px; border: 1px dashed #ccc;">
                                            <i class="fa-regular fa-image fs-5"></i>
                                        </div>
                                    @endif
                                </td>

                                <td class="text-start">
                                    <h6 class="mb-0 fw-bold text-dark" style="font-family: var(--font-base);">{{ $item['name'] }}</h6>
                                </td>

                                <td class="text-start">
                                    <span class="text-muted" style="font-size: 0.9rem;">
                                        {{ \Illuminate\Support\Str::limit($item['description'], 50) }}
                                    </span>
                                </td>

                                <td>
                                    @if($item['status'] == 'active')
                                        <span class="badge bg-success" style="padding: 6px 12px; font-weight: 500; letter-spacing: 0.5px;">HIỂN THỊ</span>
                                    @else
                                        <span class="badge bg-secondary" style="padding: 6px 12px; font-weight: 500; letter-spacing: 0.5px;">ẨN</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="/brand/edit/{{ $item['id'] }}" class="btn btn-sm btn-outline-dark" title="Sửa" style="width: 32px; height: 32px; padding: 0; line-height: 30px;">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        
                                        <button class="btn btn-sm btn-outline-danger" 
                                                style="width: 32px; height: 32px; padding: 0; line-height: 30px;"
                                                data-id="{{ $item['id'] }}"
                                                data-name="{{ $item['name'] }}"
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
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fa-solid fa-tags fs-1 mb-3 opacity-25"></i>
                                        <h5 class="fw-normal">Chưa có thương hiệu nào</h5>
                                        <p class="small mb-3">Hãy thêm thương hiệu mới để hiển thị tại đây.</p>
                                        <a href="/brand/create" class="btn btn-dark btn-sm px-3">Thêm mới ngay</a>
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

<form id="deleteBrandForm" method="POST" action="/brand/delete" style="display: none;">
    <input type="hidden" name="delete_id" id="deleteBrandIdInput">
</form>

@endsection

@section('scripts')
<script>
    function confirmDelete(el) {
        var id = el.getAttribute('data-id');
        var name = el.getAttribute('data-name');

        Swal.fire({
            title: 'Xóa thương hiệu?',
            html: "Bạn có chắc chắn muốn xóa <b>" + name + "</b>?<br>Hành động này không thể hoàn tác!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545', // Màu đỏ nguy hiểm cho nút xoá
            cancelButtonColor: '#111',     // Màu tối cho nút huỷ
            confirmButtonText: '<i class="fa-solid fa-trash me-1"></i> Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteBrandIdInput').value = id;
                document.getElementById('deleteBrandForm').submit();
            }
        });
    }
</script>
@endsection