@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container-fluid py-4">
    
    {{-- Header & Search Form --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h4 class="mb-0 fw-bold text-primary">
            <i class="fa-solid fa-address-book me-2"></i>Quản lý liên hệ
        </h4>
        
        <form action="" method="GET" class="d-flex gap-2">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Tìm tên, email, sđt..." value="{{ $search }}">
                <button class="btn btn-primary" type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
            @if(!empty($search))
                <a href="/contact/index" class="btn btn-outline-secondary" title="Xóa tìm kiếm">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            @endif
        </form>
    </div>

    {{-- Hiển thị thông báo tìm kiếm --}}
    <div class="mb-3 text-muted">
        <span>Tìm thấy <b>{{ $total_records }}</b> liên hệ @if(!empty($search)) với từ khóa "{{ $search }}" @endif</span>
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
                            <th width="20%">Tên người gửi</th>
                            <th width="15%">Tiêu đề</th>
                            <th width="20%">Nội dung</th>
                            <th width="15%">Email</th>
                            <th width="10%" class="text-center">SĐT</th>
                            <th width="10%" class="text-center">Ngày gửi</th>
                            <th width="5%" class="text-center">Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($contact) && count($contact) > 0)
                        @foreach($contact as $items)
                        <tr>
                            <td class="text-center fw-bold text-muted">{{ $items['id'] }}</td>

                            <td>
                                <div class="fw-bold">{{ $items['fullname'] }}</div>
                            </td>

                            <td class="text-muted">{{ $items['title'] }}</td>

                            <td>
                                <span class="d-inline-block text-truncate" style="max-width: 200px;" title="{{ $items['content'] }}">
                                    {{ $items['content'] }}
                                </span>
                            </td>

                            <td><a href="mailto:{{ $items['email'] }}" class="text-decoration-none">{{ $items['email'] }}</a></td>

                            <td class="text-center">{{ $items['phone'] }}</td>

                            <td class="text-center small text-muted">
                                {{ date('d/m/Y', strtotime($items['created_at'])) }}<br>
                                {{ date('H:i', strtotime($items['created_at'])) }}
                            </td>

                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-danger border-0"
                                    data-id="{{ $items['id'] }}"
                                    onclick="confirmDelete(this)"
                                    title="Xóa">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-inbox fs-1 mb-3 d-block opacity-25"></i>
                                Không tìm thấy dữ liệu liên hệ nào.
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Pagination (Phân trang) --}}
            @if($total_pages > 1)
            <div class="d-flex justify-content-center mt-4">
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        {{-- Nút Previous --}}
                        <li class="page-item {{ $current_page <= 1 ? 'disabled' : '' }}">
                            <a class="page-link" href="?page={{ $current_page - 1 }}&search={{ $search }}" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>

                        {{-- Các số trang --}}
                        @for($i = 1; $i <= $total_pages; $i++)
                        <li class="page-item {{ $current_page == $i ? 'active' : '' }}">
                            <a class="page-link" href="?page={{ $i }}&search={{ $search }}">{{ $i }}</a>
                        </li>
                        @endfor

                        {{-- Nút Next --}}
                        <li class="page-item {{ $current_page >= $total_pages ? 'disabled' : '' }}">
                            <a class="page-link" href="?page={{ $current_page + 1 }}&search={{ $search }}" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Form ẩn để xóa --}}
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
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Xóa ngay',
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