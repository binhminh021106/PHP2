@extends('layouts.admin')

@section('title', $title ?? 'Quản lý Danh mục')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Breadcrumb & Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mt-0 mb-2">Quản Lý Danh Mục</h1>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item active">Danh mục</li>
            </ol>
        </div>
        <a href="/category/create" class="btn btn-dark shadow-sm px-4" style="text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">
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
            <i class="fa-solid fa-layer-group me-1 text-muted"></i> Danh sách danh mục
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center mb-0">
                    <thead class="bg-light" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <tr>
                            <th width="5%" class="py-3 text-muted">#</th>
                            <th width="25%" class="py-3 text-muted text-start">Tên danh mục</th>
                            <th width="40%" class="py-3 text-muted text-start">Mô tả</th>
                            <th width="15%" class="py-3 text-muted">Trạng thái</th>
                            <th width="15%" class="py-3 text-muted">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($category))
                            @foreach($category as $item)
                            <tr>
                                <td class="fw-bold text-muted">{{ $item['id'] }}</td>
                                
                                <!-- Tên danh mục -->
                                <td class="text-start">
                                    <h6 class="mb-0 fw-bold text-dark" style="font-family: var(--font-base);">{{ $item['name'] }}</h6>
                                    @if(!empty($item['slug']))
                                        <small class="text-muted" style="font-size: 0.8rem;">/{{ $item['slug'] }}</small>
                                    @endif
                                </td>

                                <!-- Mô tả -->
                                <td class="text-start">
                                    <span class="text-muted small text-truncate d-inline-block" style="max-width: 300px; font-size: 0.9rem;">
                                        {{ $item['description'] ?? 'Không có mô tả' }}
                                    </span>
                                </td>

                                <!-- Trạng thái -->
                                <td>
                                    @if($item['status'] == 'active')
                                        <span class="badge bg-success" style="padding: 6px 12px; font-weight: 500; letter-spacing: 0.5px;">HIỂN THỊ</span>
                                    @else
                                        <span class="badge bg-secondary" style="padding: 6px 12px; font-weight: 500; letter-spacing: 0.5px;">ẨN</span>
                                    @endif
                                </td>

                                <!-- Hành động -->
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="/category/edit/{{ $item['id'] }}" class="btn btn-sm btn-outline-dark" title="Sửa" style="width: 32px; height: 32px; padding: 0; line-height: 30px;">
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
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fa-solid fa-layer-group fs-1 mb-3 opacity-25"></i>
                                        <h5 class="fw-normal">Chưa có danh mục nào</h5>
                                        <p class="small mb-3">Hãy thêm danh mục mới để phân loại sản phẩm.</p>
                                        <a href="/category/create" class="btn btn-dark btn-sm px-3">Thêm mới ngay</a>
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

<!-- Form Ẩn dùng để Submit xóa -->
<form id="deleteCategoryForm" method="POST" action="/category/delete" style="display: none;">
    <input type="hidden" name="delete_id" id="deleteCategoryIdInput">
</form>

@endsection

@section('scripts')
<script>
    function confirmDelete(el) {
        var id = el.getAttribute('data-id');
        var name = el.getAttribute('data-name');

        Swal.fire({
            title: 'Xóa danh mục?',
            html: "Bạn có chắc chắn muốn xóa <b>" + name + "</b>?<br>Hành động này không thể hoàn tác (bao gồm cả các sản phẩm thuộc danh mục này nếu có ràng buộc)!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#111',
            confirmButtonText: '<i class="fa-solid fa-trash me-1"></i> Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteCategoryIdInput').value = id;
                document.getElementById('deleteCategoryForm').submit();
            }
        });
    }
</script>
@endsection