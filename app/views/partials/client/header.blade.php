<nav class="navbar navbar-expand-lg bg-white sticky-top border-bottom py-3">
    <div class="container">
        <!-- 1. Logo -->
        <a class="navbar-brand fw-bold text-uppercase ls-1" href="/" style="letter-spacing: 2px;">
            MENSWEAR.
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navClient">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- 2. Menu Center -->
        <div class="collapse navbar-collapse" id="navClient">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 text-uppercase fs-6 fw-medium">
                <li class="nav-item px-2"><a class="nav-link text-dark" href="/">Trang chủ</a></li>
                <li class="nav-item px-2"><a class="nav-link text-dark" href="/product">Sản phẩm</a></li>
                <li class="nav-item px-2"><a class="nav-link text-dark" href="#">Áo</a></li>
                <li class="nav-item px-2"><a class="nav-link text-dark" href="#">Quần</a></li>
                <li class="nav-item px-2"><a class="nav-link text-dark" href="#">Sale</a></li>
            </ul>

            <!-- 3. Right Icons (Search, Cart, User) -->
            <div class="d-flex align-items-center gap-3">
                <!-- Search Icon trigger modal/collapse (Simulated) -->
                <a href="#" class="text-dark text-decoration-none" title="Tìm kiếm">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </a>

                <!-- Cart Icon -->
                <a href="#" class="text-dark text-decoration-none position-relative" title="Giỏ hàng">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-dark border border-light rounded-circle" style="width: 8px; height: 8px;"></span>
                </a>

                <div class="vr mx-1 bg-secondary opacity-25"></div>

                <!-- User Auth -->
                @if(isset($_SESSION['user_id']))
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" data-bs-toggle="dropdown">
                            <span class="fw-medium ms-1 d-none d-md-inline">{{ $_SESSION['user_name'] }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-2 rounded-0">
                            <li><a class="dropdown-item" href="/profile">Tài khoản</a></li>
                            <li><a class="dropdown-item" href="/orders">Đơn mua</a></li>
                            @if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin')
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item fw-bold" href="/dashboard">Trang quản trị</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/auth/logout">Đăng xuất</a></li>
                        </ul>
                    </div>
                @else
                    <a href="/auth/login" class="btn btn-dark btn-sm rounded-0 px-3 fw-bold">ĐĂNG NHẬP</a>
                @endif
            </div>
        </div>
    </div>
</nav>