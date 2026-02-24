<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard - MENSWEAR')</title>

    <!-- Fonts: Jost & Playfair Display đồng bộ với giao diện -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --font-base: 'Jost', sans-serif;
            --font-heading: 'Playfair Display', serif;
            --color-dark: #111;
            --color-accent: #c9a47c;
            --transition: all 0.3s ease;
        }

        body {
            font-family: var(--font-base);
            background-color: #f8f9fa; /* Nền xám nhạt làm nổi bật các thẻ Card */
            color: var(--color-dark);
            overflow-x: hidden;
        }

        /* Layout Structure */
        .admin-wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        .admin-sidebar-wrapper {
            width: 240px; /* Khớp với độ rộng sidebar mới */
            flex-shrink: 0;
            background-color: #fff;
            border-right: 1px solid #eee;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1050;
            transition: var(--transition);
            box-shadow: 4px 0 15px rgba(0,0,0,0.03);
        }

        .admin-content {
            flex-grow: 1;
            margin-left: 240px; /* Khớp với độ rộng sidebar */
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: var(--transition);
        }

        /* Lớp phủ khi mở menu trên Mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            backdrop-filter: blur(2px);
        }

        /* Sidebar Responsive */
        @media (max-width: 991.98px) {
            .admin-sidebar-wrapper {
                margin-left: -240px;
            }

            .admin-sidebar-wrapper.active {
                margin-left: 0;
            }

            .admin-content {
                margin-left: 0;
            }

            .sidebar-overlay.active {
                display: block;
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--color-accent);
        }

        /* General UI Tweaks cho Admin */
        .card {
            border: 1px solid #eee;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.02);
            border-radius: 8px;
            background-color: #fff;
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #eee;
            font-family: var(--font-heading);
            font-size: 1.15rem;
            font-weight: 600;
            padding: 15px 20px;
            color: var(--color-dark);
        }

        /* Tuỳ chỉnh lại nút bấm hệ thống */
        .btn-primary {
            background-color: var(--color-dark);
            border-color: var(--color-dark);
            border-radius: 4px;
        }
        .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background-color: var(--color-accent) !important;
            border-color: var(--color-accent) !important;
            box-shadow: none !important;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-heading);
        }
    </style>

    @yield('styles')
</head>

<body>

    <div class="admin-wrapper">
        <!-- Sidebar Overlay (Dành cho Mobile) -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- 1. Sidebar -->
        <aside class="admin-sidebar-wrapper" id="sidebarMenu">
            @include('partials.admin.sidebar')
        </aside>

        <!-- Main Content Wrapper -->
        <main class="admin-content">
            <!-- 2. Header -->
            @include('partials.admin.header')

            <!-- Content Body -->
            <div class="container-fluid p-4 flex-grow-1">
                @yield('content')
            </div>

            <!-- 3. Footer -->
            @include('partials.admin.footer')
        </main>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script Toggle Sidebar Mobile -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var sidebarToggle = document.querySelector('[data-bs-target="#sidebarMenu"]');
            var sidebar = document.getElementById('sidebarMenu');
            var overlay = document.getElementById('sidebarOverlay');

            function toggleSidebar() {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            }

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', toggleSidebar);
            }

            // Đóng sidebar khi click ra ngoài overlay (trên mobile)
            if (overlay) {
                overlay.addEventListener('click', toggleSidebar);
            }
        });
    </script>

    @yield('scripts')
</body>

</html>