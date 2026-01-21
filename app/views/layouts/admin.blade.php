<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title> {{-- Tiêu đề động --}}

    <!-- Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .main-card { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); background: white; }
    </style>
    
    @yield('styles') {{-- Nơi để các view con chèn thêm CSS riêng nếu cần --}}
</head>
<body>

    {{-- Phần Header/Navbar --}}
    @include('layouts.partials.header')

    {{-- Phần Nội dung chính (sẽ thay đổi theo từng trang) --}}
    <div class="container pb-5">
        @yield('content')
    </div>

    {{-- Phần Footer --}}
    @include('layouts.partials.footer')

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('scripts') {{-- Nơi để các view con chèn thêm JS riêng nếu cần --}}
</body>
</html>