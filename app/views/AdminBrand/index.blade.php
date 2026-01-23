@extends('layouts.admin')

@section('title', $title)

@section('content')
<!-- Header Page -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0 text-dark">Thương Hiệu</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Thương Hiệu</li>
            </ol>
        </nav>
    </div>
    <a href="/brand/create" class="btn btn-primary shadow-sm">
        <i class="fa-solid fa-plus me-2"></i>Thêm Mới
    </a>
</div>

<!-- Brand Table -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3" width="5%">STT</th>
                        <th width="25%">Tên Thương Hiệu</th>
                        <th width="30%">Mô Tả</th>
                        <th width="20%">Logo</th>
                        <th width="20%" class="text-end pe-3">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($brand))
                    @php $i = 1; @endphp
                    @foreach ($brand as $item)
                    <tr>
                        <td class="ps-3 fw-bold text-muted">#{{ $i++ }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ htmlspecialchars($item['name']) }}</div>
                        </td>
                        <td class="text-secondary small">{{ htmlspecialchars($item['description']) }}</td>
                        <td>
                            <!-- Kiểm tra nếu có tên file ảnh -->
                            <?php if (!empty($item['image'])): ?>
                                <img src="/storage/uploads/brands/<?= htmlspecialchars($item['image']) ?>"
                                    alt="Logo"
                                    class="img-thumbnail"
                                    style="height: 50px; width: auto;">
                            <?php else: ?>
                                <span class="text-muted small">No Image</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-3">
                            <a href="/brand/edit/{{ $item['id'] }}"
                                class="btn btn-sm btn-outline-primary me-1" title="Sửa">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-danger" data-id="{{ $item['id'] }}" title="Xóa">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486777.png" width="64" class="mb-3 opacity-50" alt="Empty">
                            <p class="mb-0">Chưa có thương hiệu nào.</p>
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
            <form method="POST" action="/brand/delete">
                <div class="modal-body text-center py-4">
                    <div class="mb-3 text-danger">
                        <i class="fa-solid fa-circle-exclamation fa-3x"></i>
                    </div>
                    <h5 class="fw-bold">Xóa thương hiệu?</h5>
                    <p class="text-muted small mb-4">Hành động này không thể hoàn tác.</p>
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

    document.querySelectorAll('.btn-outline-danger').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            document.getElementById('deleteIdInput').value = id;
            deleteModal.show();
        });
    });
</script>
@endsection