@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold text-primary"><i class="fa-solid fa-tag me-2"></i>Quản lý Thương hiệu</h4>
        <a href="/brand/create" class="btn btn-primary">
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
                            <th width="15%">Logo</th>
                            <th width="25%">Tên Thương Hiệu</th>
                            <th width="30%">Mô Tả</th>
                            <th width="15%" class="text-center">Trạng thái</th>
                            <th width="10%" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!empty($brand))
                            @foreach ($brand as $item)
                            <tr>
                                <td class="text-center fw-bold text-muted">{{ $item['id'] }}</td>
                                
                                <td>
                                    @if (!empty($item['image']))
                                        <img src="/storage/uploads/brands/{{ $item['image'] }}" 
                                             class="rounded border object-fit-cover" 
                                             width="60" height="60" 
                                             alt="Logo">
                                    @else
                                        <div class="bg-secondary bg-opacity-10 rounded d-flex align-items-center justify-content-center text-muted" style="width: 60px; height: 60px;">
                                            <i class="fa-regular fa-image"></i>
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    <h6 class="mb-0 fw-bold text-dark">{{ $item['name'] }}</h6>
                                </td>

                                <td>
                                    <span class="text-muted small">{{ \Illuminate\Support\Str::limit($item['description'], 50) }}</span>
                                </td>

                                <td class="text-center">
                                    @if($item['status'] == 'active')
                                        <span class="badge rounded-pill bg-success">Hiển thị</span>
                                    @else
                                        <span class="badge rounded-pill bg-secondary">Ẩn</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="/brand/edit/{{ $item['id'] }}" class="btn btn-sm btn-outline-warning" title="Sửa">
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
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fa-solid fa-box-open fs-2 mb-2 d-block"></i>
                                    Chưa có thương hiệu nào.
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
            title: 'Bạn chắc chắn chứ?',
            text: "Xóa thương hiệu '" + name + "' sẽ không thể hoàn tác!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Vẫn xóa!',
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