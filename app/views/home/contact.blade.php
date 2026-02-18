@extends('layouts.client')

@section('title', 'Liên hệ')

@section('content')
<main class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h1 class="h3 mb-4 text-center">Liên hệ</h1>
                        <p class="text-muted text-center mb-4">Có thắc mắc? Gửi tin nhắn cho chúng tôi, chúng tôi sẽ phản hồi sớm.</p>

                        {{-- Hiển thị lỗi nếu có --}}
                        @if(!empty($errors))
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form action="/home/contactSubmit" method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name" placeholder="Nhập tên của bạn"
                                        value="{{ $old['name'] ?? '' }}"
                                        class="form-control {{ isset($errors['name']) ? 'is-invalid' : '' }}">
                                    @if(isset($errors['name']))
                                        <div class="invalid-feedback">{{ $errors['name'] }}</div>
                                    @endif
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" id="email" name="email" placeholder="Nhập email"
                                        value="{{ $old['email'] ?? '' }}"
                                        class="form-control {{ isset($errors['email']) ? 'is-invalid' : '' }}">
                                    @if(isset($errors['email']))
                                        <div class="invalid-feedback">{{ $errors['email'] }}</div>
                                    @endif
                                </div>

                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Số điện thoại</label>
                                    <input type="tel" id="phone" name="phone" placeholder="Nhập số điện thoại"
                                        value="{{ $old['phone'] ?? '' }}"
                                        class="form-control {{ isset($errors['phone']) ? 'is-invalid' : '' }}">
                                    @if(isset($errors['phone']))
                                        <div class="invalid-feedback">{{ $errors['phone'] }}</div>
                                    @endif
                                </div>

                                <div class="col-md-6">
                                    <label for="subject" class="form-label">Chủ đề <span class="text-danger">*</span></label>
                                    <select id="subject" name="subject" class="form-select {{ isset($errors['subject']) ? 'is-invalid' : '' }}">
                                        <option value="" disabled {{ isset($old['subject']) ? '' : 'selected' }}>Chọn chủ đề</option>
                                        <option value="general" {{ (isset($old['subject']) && $old['subject'] == 'general') ? 'selected' : '' }}>Thắc mắc chung</option>
                                        <option value="support" {{ (isset($old['subject']) && $old['subject'] == 'support') ? 'selected' : '' }}>Hỗ trợ kỹ thuật</option>
                                        <option value="sales" {{ (isset($old['subject']) && $old['subject'] == 'sales') ? 'selected' : '' }}>Bán hàng</option>
                                        <option value="feedback" {{ (isset($old['subject']) && $old['subject'] == 'feedback') ? 'selected' : '' }}>Phản hồi</option>
                                        <option value="other" {{ (isset($old['subject']) && $old['subject'] == 'other') ? 'selected' : '' }}>Khác</option>
                                    </select>
                                    @if(isset($errors['subject']))
                                        <div class="invalid-feedback">{{ $errors['subject'] }}</div>
                                    @endif
                                </div>

                                <div class="col-12">
                                    <label for="message" class="form-label">Tin nhắn <span class="text-danger">*</span></label>
                                    <textarea id="message" name="message" rows="5" placeholder="Viết tin nhắn của bạn..."
                                        class="form-control {{ isset($errors['message']) ? 'is-invalid' : '' }}">{{ $old['message'] ?? '' }}</textarea>
                                    @if(isset($errors['message']))
                                        <div class="invalid-feedback">{{ $errors['message'] }}</div>
                                    @endif
                                </div>

                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter"
                                            {{ isset($old['newsletter']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="newsletter">Đăng ký nhận bản tin</label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100">Gửi tin nhắn</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Phần thông tin liên hệ bên dưới --}}
                <div class="row g-3 mt-4">
                    <div class="col-md-4">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <div class="mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="text-primary" viewBox="0 0 16 16">
                                        <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z"/>
                                    </svg>
                                </div>
                                <h5 class="card-title">Email</h5>
                                <p class="card-text text-muted">contact@simpleshop.com</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <div class="mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="text-primary" viewBox="0 0 16 16">
                                        <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"/>
                                    </svg>
                                </div>
                                <h5 class="card-title">Phone</h5>
                                <p class="card-text text-muted">+84 123 456 789</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <div class="mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="text-primary" viewBox="0 0 16 16">
                                        <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                                    </svg>
                                </div>
                                <h5 class="card-title">Address</h5>
                                <p class="card-text text-muted">123 Street, District 1, Ho Chi Minh City</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@section('scripts')
    {{-- 1. QUAN TRỌNG: Nhúng thư viện SweetAlert2 ở đây --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- 2. Kiểm tra và hiển thị thông báo --}}
    @if(!empty($success_msg))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Kiểm tra xem Swal đã tải chưa
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: "{!! $success_msg !!}",
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end',
                    timerProgressBar: true
                });
            } else {
                // Fallback nếu thư viện lỗi
                console.error('SweetAlert2 chưa được tải!');
                alert("{!! $success_msg !!}");
            }
        });
    </script>
    @endif
@endsection