@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container-fluid py-4">
    <!-- Header & Nút Thêm -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold text-primary"><i class="fa-solid fa-layer-group me-2"></i>Quản lý Danh mục</h4>
        <a href="/category/create" class="btn btn-primary">
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
    <div class="card shadow border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th width="5%" class="text-center">#</th>
                            <th width="25%">Tên danh mục</th>
                            <th width="40%">Mô tả</th>
                            <th width="15%" class="text-center">Trạng thái</th>
                            <th width="15%" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Sửa biến loop từ $category thành $item -->
                        @if(!empty($category))
                        @foreach($category as $item)
                        <tr>
                            <td class="text-center fw-bold text-muted">{{ $item['id'] }}</td>
                            
                            <!-- Tên danh mục -->
                            <td>
                                <h6 class="mb-0 fw-bold text-dark">{{ $item['name'] }}</h6>
                                @if(!empty($item['slug']))
                                <small class="text-muted" style="font-size: 0.8rem;">/{{ $item['slug'] }}</small>
                                @endif
                            </td>

                            <!-- Mô tả -->
                            <td>
                                <span class="text-muted small text-truncate d-inline-block" style="max-width: 300px;">
                                    {{ $item['description'] ?? 'Không có mô tả' }}
                                </span>
                            </td>

                            <!-- Trạng thái -->
                            <td class="text-center">
                                @if($item['status'] == 'active')
                                <span class="badge rounded-pill bg-success">Hiển thị</span>
                                @else
                                <span class="badge rounded-pill bg-secondary">Ẩn</span>
                                @endif
                            </td>

                            <!-- Hành động -->
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="/category/edit/{{ $item['id'] }}" class="btn btn-sm btn-outline-warning" title="Sửa">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>

                                    <button class="btn btn-sm btn-outline-danger"
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
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="fa-solid fa-layer-group fs-2 mb-2 d-block"></i>
                                Chưa có danh mục nào.
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
            title: 'Bạn chắc chắn chứ?',
            text: "Xóa danh mục '" + name + "' sẽ không thể hoàn tác (bao gồm cả các sản phẩm thuộc danh mục này nếu có ràng buộc)!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Vẫn xóa!',
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