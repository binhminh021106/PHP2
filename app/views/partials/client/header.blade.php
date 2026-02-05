<!-- Load Fonts & Icons (Nếu layout chưa có) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">

<style>
    :root {
        --font-base: 'Jost', sans-serif;
        --font-heading: 'Playfair Display', serif;
        --color-dark: #111;
        --color-accent: #c9a47c;
    }

    .header-branding {
        font-family: var(--font-heading);
        font-weight: 700;
        letter-spacing: 1px;
        font-size: 1.5rem;
        color: var(--color-dark) !important;
    }

    .nav-link {
        font-family: var(--font-base);
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 1.5px;
        font-weight: 500;
        color: var(--color-dark) !important;
        padding: 0 15px !important;
        transition: 0.3s;
        position: relative;
    }

    /* Hiệu ứng gạch chân khi hover */
    .nav-link::after {
        content: '';
        position: absolute;
        width: 0;
        height: 1px;
        bottom: 5px;
        left: 15px;
        background-color: var(--color-accent);
        transition: width 0.3s;
    }

    .nav-link:hover::after, .nav-link.active::after {
        width: calc(100% - 30px);
    }
    
    .nav-link:hover {
        color: var(--color-accent) !important;
    }

    .header-icons .nav-link {
        font-size: 1.2rem;
        padding: 0 10px !important;
    }
    
    .header-icons .nav-link::after { display: none; } /* Không gạch chân icon */

    .navbar-toggler {
        border: none;
        padding: 0;
    }
    
    .navbar-toggler:focus {
        box-shadow: none;
    }

    /* Dropdown User */
    .dropdown-menu {
        border-radius: 0;
        border: none;
        box-shadow: 0 5px 25px rgba(0,0,0,0.1);
        margin-top: 15px;
        padding: 0;
    }
    
    .dropdown-item {
        font-family: var(--font-base);
        font-size: 0.9rem;
        padding: 12px 20px;
        border-bottom: 1px solid #f1f1f1;
        transition: 0.2s;
    }
    
    .dropdown-item:last-child { border-bottom: none; }
    
    .dropdown-item:hover {
        background-color: #f8f9fa;
        color: var(--color-accent);
        padding-left: 25px;
    }
</style>

<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top py-3 shadow-sm">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand header-branding" href="/">MENSWEAR.</a>

        <!-- Mobile Toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Content -->
        <div class="collapse navbar-collapse" id="navbarContent">
            <!-- Centered Menu -->
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="/">Trang chủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/shop">Cửa hàng</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Bộ sưu tập</a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link" href="#">Liên hệ</a>
                </li>
            </ul>

            <!-- Right Icons -->
            <ul class="navbar-nav header-icons align-items-center">
                <!-- Search (Optional) -->
                <li class="nav-item d-none d-lg-block">
                    <a class="nav-link" href="#"><i class="fas fa-search"></i></a>
                </li>

                <!-- Cart -->
                <li class="nav-item position-relative me-2">
                    <a class="nav-link" href="/cart">
                        <i class="fas fa-shopping-bag"></i>
                        @php
                            $cartCount = 0;
                            if (isset($_SESSION['cart'])) {
                                foreach ($_SESSION['cart'] as $item) {
                                    $cartCount += $item['quantity'];
                                }
                            }
                        @endphp
                        @if($cartCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-dark text-white d-flex align-items-center justify-content-center" style="width: 18px; height: 18px; font-size: 10px;">
                            {{ $cartCount }}
                        </span>
                        @endif
                    </a>
                </li>

                <!-- User Dropdown -->
                @if(isset($_SESSION['user']))
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDrop" role="button" data-bs-toggle="dropdown">
                        <i class="far fa-user"></i> <span class="d-lg-none ms-2">{{ $_SESSION['user']['name'] }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end animate__animated animate__fadeIn">
                        <li class="px-4 py-3 bg-light">
                            <small class="text-muted d-block mb-1">Xin chào,</small>
                            <strong class="text-dark">{{ $_SESSION['user']['name'] }}</strong>
                        </li>
                        @if($_SESSION['user']['role'] == 1)
                        <li><a class="dropdown-item" href="/product"><i class="fas fa-cog me-2 text-secondary"></i>Quản trị website</a></li>
                        @endif
                        <li><a class="dropdown-item" href="/profile"><i class="far fa-id-card me-2 text-secondary"></i>Thông tin cá nhân</a></li>
                        <li><a class="dropdown-item" href="/order/history"><i class="fas fa-history me-2 text-secondary"></i>Lịch sử đơn hàng</a></li>
                        <li><a class="dropdown-item text-danger" href="/auth/logout"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
                    </ul>
                </li>
                @else
                <li class="nav-item ms-2">
                    <a class="btn btn-dark text-white rounded-0 px-4 py-2" href="/auth/login" style="font-size: 0.8rem; letter-spacing: 1px;">ĐĂNG NHẬP</a>
                </li>
                @endif
            </ul>
        </div>
    </div>
</nav>