@extends('layouts.admin')

@section('title', $title ?? 'Quản lý Thương hiệu')

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
            <h1 class="mt-0 mb-2">Quản Lý Thương Hiệu</h1>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item active">Thương hiệu</li>
            </ol>
        </div>
        
        <div class="d-flex gap-2 align-items-center">
            <!-- Search Form -->
            <form action="" method="GET" class="d-flex gap-2">
                <div class="input-group shadow-sm">
                    <input type="text" name="search" class="form-control rounded-0 border-dark" placeholder="Tìm tên thương hiệu..." value="{{ $search ?? '' }}">
                    <button class="btn btn-dark rounded-0 px-3" type="submit" title="Tìm kiếm">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
                @if(!empty($search))
                    <a href="/brand/index" class="btn btn-outline-dark rounded-0 d-flex align-items-center" title="Xóa bộ lọc">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </form>

            <a href="/brand/create" class="btn btn-dark shadow-sm px-4 rounded-0 d-flex align-items-center" style="text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; height: 38px;">
                <i class="fa-solid fa-plus me-2"></i>Thêm mới
            </a>
        </div>
    </div>

    <!-- Hiển thị thông báo tìm kiếm -->
    <div class="mb-3 text-muted small" style="letter-spacing: 0.5px;">
        <span>Tìm thấy <b class="text-dark">{{ $total_records ?? 0 }}</b> thương hiệu @if(!empty($search)) với từ khóa "<b class="text-dark">{{ $search }}</b>" @endif</span>
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
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#111',
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