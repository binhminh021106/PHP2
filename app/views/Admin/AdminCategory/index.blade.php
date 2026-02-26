@extends('layouts.admin')

@section('title', $title ?? 'Quản lý Danh mục')

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
            <h1 class="mt-0 mb-2">Quản Lý Danh Mục</h1>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item active">Danh mục</li>
            </ol>
        </div>
        
        <div class="d-flex gap-2 align-items-center">
            <!-- Search Form -->
            <form action="" method="GET" class="d-flex gap-2">
                <div class="input-group shadow-sm">
                    <input type="text" name="search" class="form-control rounded-0 border-dark" placeholder="Tìm tên danh mục..." value="{{ $search ?? '' }}">
                    <button class="btn btn-dark rounded-0 px-3" type="submit" title="Tìm kiếm">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
                @if(!empty($search))
                    <a href="/category/index" class="btn btn-outline-dark rounded-0 d-flex align-items-center" title="Xóa bộ lọc">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </form>

            <a href="/category/create" class="btn btn-dark shadow-sm px-4 rounded-0 d-flex align-items-center" style="text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; height: 38px;">
                <i class="fa-solid fa-plus me-2"></i>Thêm mới
            </a>
        </div>
    </div>

    <!-- Hiển thị thông báo tìm kiếm -->
    <div class="mb-3 text-muted small" style="letter-spacing: 0.5px;">
        <span>Tìm thấy <b class="text-dark">{{ $total_records ?? 0 }}</b> danh mục @if(!empty($search)) với từ khóa "<b class="text-dark">{{ $search }}</b>" @endif</span>
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
                                    <h6 class="mb-0 fw-bold text-dark" style="font-family: var(--font-base);">
                                        <i class="{{ $item['icon'] }} me-2 text-muted"></i>{{ $item['name'] }}
                                    </h6>
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