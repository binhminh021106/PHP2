<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="/">SHOP ONLINE</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/">Trang chủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/product">Sản phẩm</a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/cart">
                        Giỏ hàng <span class="badge bg-danger">0</span>
                    </a>
                </li>

                @if(isset($_SESSION['user']))
                {{-- Nếu đã đăng nhập --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        Chào, {{ $_SESSION['user']['name'] }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @if($_SESSION['user']['role'] == 1)
                        <li><a class="dropdown-item fw-bold text-primary" href="/product">
                                <i class="bi bi-gear-fill"></i> Trang quản trị
                            </a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        @endif

                        <li><a class="dropdown-item" href="/profile">Thông tin cá nhân</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="/auth/logout">Đăng xuất</a></li>
                    </ul>
                </li>
                @else
                {{-- Nếu chưa đăng nhập --}}
                <li class="nav-item">
                    <a class="nav-link" href="/auth/login">Đăng nhập</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/auth/register">Đăng ký</a>
                </li>
                @endif
            </ul>
        </div>
    </div>
</nav>