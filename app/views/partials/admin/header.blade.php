<style>
    /* Tuỳ chỉnh giao diện Header Admin đồng bộ với Sidebar */
    .admin-header {
        font-family: var(--font-base, 'Jost', sans-serif);
        height: 70px;
        z-index: 999;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }

    .admin-header .input-group-text,
    .admin-header .form-control {
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }

    .admin-header .form-control:focus {
        box-shadow: none;
        background-color: #fff;
        border-color: #ddd !important;
    }

    .admin-header .input-group:focus-within .input-group-text {
        background-color: #fff;
    }

    .admin-header .nav-link {
        color: #555;
        transition: all 0.3s ease;
    }

    .admin-header .nav-link:hover {
        color: var(--color-accent, #c9a47c);
    }

    /* Tuỳ chỉnh Dropdown */
    .admin-header .dropdown-menu {
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        padding: 10px 0;
        margin-top: 15px;
    }

    .admin-header .dropdown-item {
        padding: 10px 20px;
        font-weight: 500;
        font-size: 0.9rem;
        color: #555;
        transition: all 0.3s ease;
    }

    .admin-header .dropdown-item:hover {
        background-color: #f8f9fa;
        color: var(--color-accent, #c9a47c);
        padding-left: 25px; /* Trượt nhẹ chữ sang phải khi hover */
    }

    .admin-header .dropdown-item i {
        width: 20px;
        text-align: center;
        transition: color 0.3s ease;
    }

    .admin-header .dropdown-item:hover i {
        color: var(--color-accent, #c9a47c) !important;
    }

    .admin-header .dropdown-item.text-danger:hover {
        color: #dc3545 !important;
    }
    
    .admin-header .dropdown-item.text-danger:hover i {
        color: #dc3545 !important;
    }
</style>

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top px-4 admin-header">
    <div class="d-flex align-items-center w-100">
        <!-- Nút toggle sidebar trên Mobile -->
        <button class="btn btn-light d-lg-none me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
            <i class="fa-solid fa-bars"></i>
        </button>
        
        <!-- Thanh tìm kiếm -->
        <form class="d-none d-md-flex input-group" style="width: 300px;">
            <span class="input-group-text border-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
            <input type="search" class="form-control border-0" placeholder="Tìm kiếm...">
        </form>

        <div class="ms-auto d-flex align-items-center">
            <ul class="navbar-nav align-items-center flex-row">
                
                <!-- Dropdown Tài khoản -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center text-decoration-none" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        @php
                            $adminName = $_SESSION['user']['name'] ?? 'Admin';
                        @endphp
                        <!-- Đổi tông màu nền Avatar sang Đen (111) cho ngầu -->
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($adminName) }}&background=111&color=fff" class="rounded-circle me-2 border" width="35" height="35" alt="Avatar">
                        <span class="fw-semibold text-dark d-none d-sm-inline-block">{{ $adminName }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0">
                        <li>
                            <a class="dropdown-item" href="/profile">
                                <i class="fa-regular fa-id-badge me-2 text-muted"></i> Hồ sơ
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider opacity-50 my-2">
                        </li>
                        <li>
                            <!-- Gắn link logout -->
                            <a class="dropdown-item text-danger" href="/auth/logout">
                                <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Đăng xuất
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>