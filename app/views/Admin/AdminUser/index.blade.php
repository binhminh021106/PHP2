@extends('layouts.admin')

@section('title', $title ?? 'Quản Lý Thành Viên')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mt-0 mb-2">Quản Lý Thành Viên</h1>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item active">Thành viên</li>
            </ol>
        </div>
        <a href="/user/create" class="btn btn-dark shadow-sm px-4" style="text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">
            <i class="fa-solid fa-user-plus me-2"></i>Thêm Mới
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

    <!-- Bảng dữ liệu thành viên -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <i class="fa-solid fa-users me-1 text-muted"></i> Danh sách người dùng hệ thống
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead class="bg-light" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <tr>
                            <th width="5%" class="py-3 text-muted">ID</th>
                            <th width="8%" class="py-3 text-muted">Avatar</th>
                            <th width="20%" class="py-3 text-muted text-start">Họ Tên</th>
                            <th width="18%" class="py-3 text-muted text-start">Email</th>
                            <th width="12%" class="py-3 text-muted">Số ĐT</th>
                            <th width="12%" class="py-3 text-muted">Vai trò</th>
                            <th width="12%" class="py-3 text-muted">Trạng Thái</th>
                            <th width="13%" class="py-3 text-muted">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!empty($user) && count($user) > 0)
                            @foreach ($user as $u)
                            <tr>
                                <td class="fw-bold text-muted">#{{ $u['id'] }}</td>
                                <td>
                                    @if(!empty($u['avatar_url']))
                                        <img src="/storage/uploads/users/{{ $u['avatar_url'] }}"
                                             class="rounded-circle border shadow-sm"
                                             width="40" height="40"
                                             style="object-fit: cover;" alt="Avt">
                                    @else
                                        <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center mx-auto"
                                             style="width: 40px; height: 40px; font-size: 14px; font-family: var(--font-heading);">
                                            {{ strtoupper(substr($u['name'], 0, 1)) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-start">
                                    <div class="fw-bold text-dark" style="font-family: var(--font-base);">{{ $u['name'] }}</div>
                                    <small class="text-muted d-inline-block text-truncate" style="max-width: 180px;">
                                        <i class="fa-solid fa-location-dot me-1 opacity-50" style="font-size: 0.7rem;"></i>{{ $u['address'] ?? 'Chưa cập nhật' }}
                                    </small>
                                </td>
                                <td class="text-start">
                                    <span class="text-muted small">{{ $u['email'] }}</span>
                                </td>
                                <td>
                                    <span class="text-dark fw-medium small">{{ $u['phone'] ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    @if($u['role'] == 1)
                                        <span class="badge bg-dark-subtle text-dark border border-dark-subtle px-2 py-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                            <i class="fa-solid fa-user-shield me-1"></i>ADMIN
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border px-2 py-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                            <i class="fa-solid fa-user me-1"></i>MEMBER
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($u['status'] == 'active')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                            ACTIVE
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                            BLOCKED
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="/user/edit/{{ $u['id'] }}"
                                           class="btn btn-sm btn-outline-dark" title="Chỉnh sửa" style="width: 32px; height: 32px; padding: 0; line-height: 30px;">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger"
                                                style="width: 32px; height: 32px; padding: 0; line-height: 30px;"
                                                data-id="{{ $u['id'] }}"
                                                data-name="{{ $u['name'] }}"
                                                onclick="confirmDelete(this)" title="Xóa tài khoản">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fa-solid fa-users-slash fs-1 mb-3 opacity-25"></i>
                                        <h5 class="fw-normal">Chưa có thành viên nào</h5>
                                        <p class="small mb-3">Người dùng đăng ký tài khoản sẽ xuất hiện tại đây.</p>
                                        <a href="/user/create" class="btn btn-dark btn-sm px-3">Thêm thành viên</a>
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

<!-- Modal Xóa (Đã đồng bộ giao diện) -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-0">
            <form method="POST" action="/user/delete">
                <div class="modal-body text-center py-5 px-4">
                    <div class="mb-4 text-danger opacity-75">
                        <i class="fa-solid fa-user-slash fa-4x"></i>
                    </div>
                    <h5 class="fw-bold font-heading text-uppercase mb-3" style="letter-spacing: 1px;">Xóa tài khoản?</h5>
                    <p class="text-muted small mb-4">Bạn có chắc muốn xóa tài khoản của <b><span id="deleteUserName"></span></b>? Dữ liệu không thể khôi phục.</p>
                    <input type="hidden" name="delete_id" id="deleteIdInput">
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-light rounded-0 px-4" data-bs-dismiss="modal" style="font-size: 0.8rem; letter-spacing: 1px;">HỦY</button>
                        <button type="submit" class="btn btn-danger rounded-0 px-4" style="font-size: 0.8rem; letter-spacing: 1px;">XÓA NGAY</button>
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