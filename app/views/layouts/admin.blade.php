<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>

    <!-- Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        /* Layout Structure */
        .admin-wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        .admin-sidebar {
            width: 260px;
            flex-shrink: 0;
            background-color: #212529;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s;
        }

        .admin-content {
            flex-grow: 1;
            margin-left: 260px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: all 0.3s;
        }

        /* Sidebar Responsive */
        @media (max-width: 991.98px) {
            .admin-sidebar {
                margin-left: -260px;
            }

            .admin-sidebar.active {
                margin-left: 0;
            }

            .admin-content {
                margin-left: 0;
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* General UI Tweaks */
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border-radius: 0.5rem;
        }

        .nav-pills .nav-link.active {
            background-color: #0d6efd;
            box-shadow: 0 4px 6px -1px rgba(13, 110, 253, 0.4);
        }

        .nav-pills .nav-link:hover:not(.active) {
            background-color: rgba(255, 255, 255, 0.1);
        }
    </style>

    @yield('styles')
</head>

<body>

    <div class="admin-wrapper">
        <!-- 1. Sidebar -->
        <aside class="admin-sidebar" id="sidebarMenu">
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

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                    // Tạo overlay nếu muốn
                });
            }
        });
    </script>

    @yield('scripts')
</body>

</html>