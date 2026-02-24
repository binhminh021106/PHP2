@extends('layouts.admin')

@section('title', $title ?? 'Quản lý Liên hệ')

@section('content')

<style>
    /* Tuỳ chỉnh giao diện Phân trang (Pagination) */
    .custom-pagination .page-link {
        color: var(--color-dark, #111);
        border: 1px solid #eee;
        margin: 0 4px;
        border-radius: 0 !important; /* Bo góc vuông vức */
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
            <h1 class="mt-0 mb-2">Quản Lý Liên Hệ</h1>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item active">Liên hệ</li>
            </ol>
        </div>
        
        <!-- Search Form -->
        <form action="" method="GET" class="d-flex gap-2">
            <div class="input-group shadow-sm">
                <input type="text" name="search" class="form-control rounded-0 border-dark" placeholder="Tìm tên, email, sđt..." value="{{ $search ?? '' }}">
                <button class="btn btn-dark rounded-0 px-3" type="submit" title="Tìm kiếm">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
            @if(!empty($search))
                <a href="/contact/index" class="btn btn-outline-dark rounded-0 d-flex align-items-center" title="Xóa bộ lọc">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- Hiển thị thông báo tìm kiếm -->
    <div class="mb-3 text-muted small" style="letter-spacing: 0.5px;">
        <span>Tìm thấy <b class="text-dark">{{ $total_records ?? 0 }}</b> liên hệ @if(!empty($search)) với từ khóa "<b class="text-dark">{{ $search }}</b>" @endif</span>
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
            <i class="fa-regular fa-envelope me-1 text-muted"></i> Danh sách thư liên hệ
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center mb-0">
                    <thead class="bg-light" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <tr>
                            <th width="5%" class="py-3 text-muted">#</th>
                            <th width="15%" class="py-3 text-muted text-start">Người gửi</th>
                            <th width="20%" class="py-3 text-muted text-start">Tiêu đề</th>
                            <th width="25%" class="py-3 text-muted text-start">Nội dung</th>
                            <th width="15%" class="py-3 text-muted text-start">Thông tin liên lạc</th>
                            <th width="10%" class="py-3 text-muted">Ngày gửi</th>
                            <th width="10%" class="py-3 text-muted">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($contact) && count($contact) > 0)
                            @foreach($contact as $items)
                            <tr>
                                <td class="fw-bold text-muted">{{ $items['id'] }}</td>

                                <td class="text-start">
                                    <h6 class="mb-0 fw-bold text-dark" style="font-family: var(--font-base);">{{ $items['fullname'] }}</h6>
                                </td>

                                <td class="text-start">
                                    <span class="text-dark fw-medium">{{ $items['title'] }}</span>
                                </td>

                                <td class="text-start">
                                    <span class="text-muted small d-inline-block text-truncate" style="max-width: 250px;" title="{{ $items['content'] }}">
                                        {{ $items['content'] }}
                                    </span>
                                </td>

                                <td class="text-start small">
                                    <div class="mb-1"><i class="fa-regular fa-envelope text-muted me-1"></i> <a href="mailto:{{ $items['email'] }}" class="text-decoration-none text-dark">{{ $items['email'] }}</a></div>
                                    <div><i class="fa-solid fa-phone text-muted me-1"></i> {{ $items['phone'] }}</div>
                                </td>

                                <td>
                                    <span class="d-block text-dark fw-medium">{{ date('d/m/Y', strtotime($items['created_at'])) }}</span>
                                    <span class="small text-muted">{{ date('H:i', strtotime($items['created_at'])) }}</span>
                                </td>

                                <td>
                                    <button class="btn btn-sm btn-outline-danger"
                                        style="width: 32px; height: 32px; padding: 0; line-height: 30px;"
                                        data-id="{{ $items['id'] }}"
                                        onclick="confirmDelete(this)"
                                        title="Xóa liên hệ">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fa-solid fa-inbox fs-1 mb-3 opacity-25"></i>
                                        <h5 class="fw-normal">Không tìm thấy dữ liệu liên hệ nào</h5>
                                        <p class="small mb-0">Hộp thư hiện đang trống.</p>
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

<!-- Form ẩn để xóa -->
<form id="deleteContactForm" method="POST" action="/contact/delete" style="display: none;">
    <input type="hidden" name="delete_id" id="deleteContactIdInput">
</form>

@endsection

@section('scripts')
<script>
    function confirmDelete(el) {
        var id = el.getAttribute('data-id');

        Swal.fire({
            title: 'Xóa liên hệ này?',
            text: "Dữ liệu sẽ không thể khôi phục!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545', // Màu đỏ nguy hiểm
            cancelButtonColor: '#111',     // Màu đen tối giản
            confirmButtonText: '<i class="fa-solid fa-trash me-1"></i> Xóa ngay',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteContactIdInput').value = id;
                document.getElementById('deleteContactForm').submit();
            }
        });
    }
</script>
@endsection