@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="mb-4">
    <h2 class="fw-bold text-dark">Cập Nhật Thành Viên</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/user">Thành viên</a></li>
            <li class="breadcrumb-item active">Chỉnh sửa</li>
        </ol>
    </nav>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="/user/update/{{ $user['id'] }}" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Họ Tên</label>
                    <input type="text" name="name" class="form-control @isset($errors['name']) is-invalid @endisset" value="{{ $user['name'] }}">
                    @isset($errors['name']) <div class="invalid-feedback">{{ $errors['name'] }}</div> @endisset
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Email</label>
                    <input type="email" name="email" class="form-control @isset($errors['email']) is-invalid @endisset" value="{{ $user['email'] }}">
                    @isset($errors['email']) <div class="invalid-feedback">{{ $errors['email'] }}</div> @endisset
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Số Điện Thoại</label>
                    <input type="text" name="phone" class="form-control @isset($errors['phone']) is-invalid @endisset" value="{{ $user['phone'] }}">
                    @isset($errors['phone']) <div class="invalid-feedback">{{ $errors['phone'] }}</div> @endisset
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Mật Khẩu (Để trống nếu không đổi)</label>
                    <input type="password" name="password" class="form-control @isset($errors['password']) is-invalid @endisset">
                    @isset($errors['password']) <div class="invalid-feedback">{{ $errors['password'] }}</div> @endisset
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Vai Trò</label>
                    <select name="role" class="form-select">
                        <option value="0" {{ $user['role'] == 0 ? 'selected' : '' }}>Thành viên (User)</option>
                        <option value="1" {{ $user['role'] == 1 ? 'selected' : '' }}>Quản trị viên (Admin)</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Trạng Thái</label>
                    <select name="status" class="form-select">
                        <option value="active" {{ $user['status'] == 'active' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="blocked" {{ $user['status'] == 'blocked' ? 'selected' : '' }}>Khóa</option>
                    </select>
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Địa Chỉ</label>
                    <textarea name="address" class="form-control @isset($errors['address']) is-invalid @endisset" rows="2">{{ $user['address'] }}</textarea>
                    @isset($errors['address']) <div class="invalid-feedback">{{ $errors['address'] }}</div> @endisset
                </div>

                <div class="col-12 mb-4 d-flex align-items-center gap-3">
                    <div class="flex-grow-1">
                        <label class="form-label fw-bold">Ảnh Đại Diện</label>
                        <input type="file" name="avatar" class="form-control">
                    </div>
                    @if($user['avatar_url'])
                    <div class="mt-4">
                        <img src="/storage/uploads/users/{{ $user['avatar_url'] }}" width="60" height="60" class="rounded border">
                    </div>
                    @endif
                </div>
            </div>

            <div class="text-end">
                <a href="/user" class="btn btn-light px-4 me-2">Quay lại</a>
                <button type="submit" class="btn btn-primary px-4">Cập Nhật</button>
            </div>
        </form>
    </div>
</div>
@endsection