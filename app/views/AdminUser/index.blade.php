@extends('layouts.admin')

@section('title', $title)

@section('content')
<!-- Header Page -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0 text-dark">Quản Lý Thành Viên</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Thành viên</li>
            </ol>
        </nav>
    </div>
    <a href="/user/create" class="btn btn-primary shadow-sm">
        <i class="fa-solid fa-user-plus me-2"></i>Thêm Mới
    </a>
</div>

<!-- SweetAlert Success -->
@if(!empty($success_msg))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: 'Thành công!',
            text: "{{ $success_msg }}",
            timer: 3000,
            showConfirmButton: false
        });
    });
</script>
@endif

<!-- User Table -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3" width="5%">ID</th>
                        <th width="8%">Avatar</th>
                        <th width="17%">Họ Tên</th>
                        <th width="15%">Email</th>
                        <th width="12%">Số ĐT</th>
                        <th width="12%">Vai trò</th> <!-- Thêm cột này -->
                        <th width="10%">Trạng Thái</th>
                        <th width="15%" class="text-end pe-4">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($user) && count($user) > 0)
                    @foreach ($user as $u)
                    <tr>
                        <td class="ps-3 fw-bold text-muted">#{{ $u['id'] }}</td>
                        <td>
                            @if(!empty($u['avatar_url']))
                            <img src="/storage/uploads/users/{{ $u['avatar_url'] }}"
                                class="rounded-circle border"
                                width="40" height="40"
                                style="object-fit: cover;" alt="Avt">
                            @else
                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center"
                                style="width: 40px; height: 40px; font-size: 14px;">
                                {{ substr($u['name'], 0, 1) }}
                            </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ htmlspecialchars($u['name']) }}</div>
                            <small class="text-muted text-truncate d-inline-block" style="max-width: 150px;">{{ $u['address'] ?? 'Chưa cập nhật' }}</small>
                        </td>
                        <td>{{ htmlspecialchars($u['email']) }}</td>
                        <td>{{ htmlspecialchars($u['phone']) }}</td>
                        <td>
                            @if($u['role'] == 1)
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">
                                <i class="fa-solid fa-user-shield me-1"></i>Admin
                            </span>
                            @else
                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1">
                                <i class="fa-solid fa-user me-1"></i>Thành viên
                            </span>
                            @endif
                        </td>
                        <td>
                            @if($u['status'] == 'active')
                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
                                Active
                            </span>
                            @else
                            <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">
                                Blocked
                            </span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="/user/edit/{{ $u['id'] }}"
                                class="btn btn-sm btn-outline-primary me-1" title="Sửa">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-danger"
                                data-id="{{ $u['id'] }}"
                                data-name="{{ $u['name'] }}"
                                onclick="confirmDelete(this)" title="Xóa">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-users-slash fa-2x mb-3 opacity-50"></i>
                            <p class="mb-0">Chưa có thành viên nào.</p>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Xóa -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="/user/delete">
                <div class="modal-body text-center py-4">
                    <div class="mb-3 text-danger">
                        <i class="fa-solid fa-user-xmark fa-3x"></i>
                    </div>
                    <h5 class="fw-bold">Xóa thành viên?</h5>
                    <p class="text-muted small mb-4">Bạn có chắc muốn xóa <strong id="deleteUserName"></strong>? Hành động này không thể hoàn tác.</p>
                    <input type="hidden" name="delete_id" id="deleteIdInput">
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-light w-50" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-danger w-50">Xóa</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

    function confirmDelete(btn) {
        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name');

        document.getElementById('deleteIdInput').value = id;
        document.getElementById('deleteUserName').innerText = name;
        deleteModal.show();
    }
</script>
@endsection