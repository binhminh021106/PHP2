@extends('layouts.admin');

@section('title', $title)

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3" width="5%">STT</th>
                        <th width="20%">Tên Thương Hiệu</th>
                        <th width="35%">Hình Ảnh</th>
                        <th>Mô tả</th>
                        <th width="20%" class="text-end pe-3">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($brands))
                    @php $i = 1; @endphp
                    @foreach ($brands as $item)
                    <tr>
                        <td class="ps-3 fw-bold text-muted">#{{ $i++ }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ htmlspecialchars($item['name']) }}</div>
                        </td>
                        <td class="text-secondary small">{{ htmlspecialchars($item['description']) }}</td>
                        <td>
                            @if (!empty($item['icon']))
                            <span class="badge bg-light text-dark border">
                                <i class="{{ htmlspecialchars($item['icon']) }} me-2"></i>{{ htmlspecialchars($item['icon']) }}
                            </span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <a href="/category/edit/{{ $item['id'] }}"
                                class="btn btn-sm btn-outline-primary me-1" title="Sửa">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-danger" title="Xóa">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486777.png" width="64" class="mb-3 opacity-50" alt="Empty">
                            <p class="mb-0">Chưa có danh mục nào.</p>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection