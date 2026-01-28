<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary fs-4" href="/">
            <i class="fa-solid fa-bag-shopping me-2"></i>MyShop
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div id="nav" class="collapse navbar-collapse">
            <!-- Menu Chính -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link fw-semibold" href="/">Trang chủ</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold" href="/product">Sản phẩm</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle fw-semibold" href="#" role="button" data-bs-toggle="dropdown">
                        Danh mục
                    </a>
                    <ul class="dropdown-menu border-0 shadow">
                        <li><a class="dropdown-item" href="#">Điện thoại</a></li>
                        <li><a class="dropdown-item" href="#">Laptop</a></li>
                        <li><a class="dropdown-item" href="#">Phụ kiện</a></li>
                    </ul>
                </li>
            </ul>

            <!-- Search & Auth -->
            <div class="d-flex align-items-center gap-3">
                <form class="d-flex" role="search">
                    <div class="input-group">
                        <input class="form-control border-end-0" type="search" placeholder="Tìm kiếm..." aria-label="Search">
                        <button class="btn btn-outline-secondary border-start-0" type="submit">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </form>

                <div class="vr mx-2 d-none d-lg-block"></div>

                <!-- Kiểm tra Session Login -->
                @if(isset($_SESSION['user_id']))
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" data-bs-toggle="dropdown">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                            {{ substr($_SESSION['user_name'], 0, 1) }}
                        </div>
                        <span class="fw-semibold d-none d-md-block">{{ $_SESSION['user_name'] }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-2">
                        <li><a class="dropdown-item" href="/profile"><i class="fa-solid fa-user me-2 text-muted"></i> Tài khoản của tôi</a></li>
                        <li><a class="dropdown-item" href="/orders"><i class="fa-solid fa-box me-2 text-muted"></i> Đơn mua</a></li>
                        @if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin')
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-primary" href="/dashboard"><i class="fa-solid fa-gauge me-2"></i> Trang quản trị</a></li>
                        @endif
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="/auth/logout"><i class="fa-solid fa-right-from-bracket me-2"></i> Đăng xuất</a></li>
                    </ul>
                </div>
                @else
                <div class="d-flex gap-2">
                    <a href="/auth/login" class="btn btn-outline-primary fw-semibold">Đăng nhập</a>
                    <a href="/auth/register" class="btn btn-primary fw-semibold">Đăng ký</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</nav>