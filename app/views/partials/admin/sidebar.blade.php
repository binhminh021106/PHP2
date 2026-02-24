<style>
    @import url('https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap');

    :root {
        --font-base: 'Jost', sans-serif;
        --font-heading: 'Playfair Display', serif;
        --color-dark: #111;
        --color-accent: #c9a47c;
        --transition: all 0.3s ease;
    }

    /* Tuỳ chỉnh giao diện Sidebar Admin phong cách MENSWEAR */
    .admin-sidebar {
        width: 240px; /* Đã thu nhỏ lại từ 260px xuống 240px */
        min-height: 100vh; 
        position: sticky; 
        top: 0;
        background-color: #fff;
        border-right: 1px solid #eee;
        font-family: var(--font-base);
        z-index: 1000;
    }
    
    .admin-brand {
        font-family: var(--font-heading);
        font-weight: 700;
        letter-spacing: 1.5px;
        color: var(--color-dark);
        text-decoration: none;
        display: flex;
        align-items: center;
        padding: 25px 15px; /* Giảm padding một chút cho vừa vặn */
        font-size: 1.4rem;
        border-bottom: 1px solid #eee;
    }

    .admin-brand span {
        color: var(--color-accent);
        font-size: 0.9rem;
        margin-left: 5px;
        font-family: var(--font-base);
        letter-spacing: 1px;
    }

    .admin-sidebar .nav-list {
        padding: 20px 0;
    }

    .admin-sidebar .nav-link {
        color: #555;
        transition: var(--transition);
        border-radius: 0;
        padding: 12px 20px; /* Giảm padding trái phải một chút */
        font-weight: 500;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        margin-bottom: 5px;
        border-left: 3px solid transparent;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .admin-sidebar .nav-link i {
        width: 25px;
        text-align: left;
        margin-right: 10px;
        font-size: 1.1rem;
        transition: var(--transition);
    }

    /* Hiệu ứng khi di chuột (Hover) */
    .admin-sidebar .nav-link:hover:not(.active) {
        color: var(--color-dark);
        background-color: #fcfcfc;
        border-left-color: #ddd;
        padding-left: 25px; /* Trượt nhẹ nội dung chữ */
    }

    /* Hiệu ứng khi đang ở trang đó (Active) */
    .admin-sidebar .nav-link.active {
        background-color: #f8f9fa;
        color: var(--color-accent);
        border-left-color: var(--color-accent);
        font-weight: 600;
    }
    
    .admin-sidebar .nav-link.active i {
        color: var(--color-accent);
    }

    .admin-sidebar-footer {
        border-top: 1px solid #eee;
        padding: 15px 0;
        margin-top: auto; /* Đẩy xuống dưới cùng */
    }
</style>

<div class="d-flex flex-column flex-shrink-0 admin-sidebar">
    <a href="/admin/dashboard" class="admin-brand">
        MENSWEAR<span>ADMIN</span>
    </a>

    <ul class="nav flex-column nav-list mb-auto">
        <!-- Dashboard -->
        <li class="nav-item">
            <a href="/dashboard" class="nav-link {{ strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false ? 'active' : '' }}">
                <i class="fa-solid fa-border-all"></i>
                Tổng quan
            </a>
        </li>

        <!-- Đơn hàng (Order) -->
        <li class="nav-item">
            <a href="/adminorder/index" class="nav-link {{ strpos($_SERVER['REQUEST_URI'], 'adminorder') !== false ? 'active' : '' }}">
                <i class="fa-solid fa-receipt"></i>
                Đơn hàng
            </a>
        </li>

        <!-- Danh mục (Category) -->
        <li class="nav-item">
            <a href="/category/index" class="nav-link {{ strpos($_SERVER['REQUEST_URI'], 'category') !== false ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group"></i>
                Danh mục
            </a>
        </li>

        <!-- Sản phẩm (Product) -->
        <li class="nav-item">
            <a href="/product/index" class="nav-link {{ strpos($_SERVER['REQUEST_URI'], 'product') !== false ? 'active' : '' }}">
                <i class="fa-solid fa-box-open"></i>
                Sản phẩm
            </a>
        </li>

        <!-- Thương hiệu (Brand) -->
        <li class="nav-item">
            <a href="/brand/index" class="nav-link {{ strpos($_SERVER['REQUEST_URI'], 'brand') !== false ? 'active' : '' }}">
                <i class="fa-solid fa-tag"></i>
                Thương hiệu
            </a>
        </li>

        <!-- Mã giảm giá (Coupon) -->
        <li class="nav-item">
            <a href="/coupon/index" class="nav-link {{ strpos($_SERVER['REQUEST_URI'], 'coupon') !== false ? 'active' : '' }}">
                <i class="fa-solid fa-ticket-simple"></i>
                Mã giảm giá
            </a>
        </li>

        <!-- Khách hàng (User) -->
        <li class="nav-item">
            <a href="/user/index" class="nav-link {{ strpos($_SERVER['REQUEST_URI'], 'user') !== false ? 'active' : '' }}">
                <i class="fa-regular fa-user"></i>
                Khách hàng
            </a>
        </li>

        <!-- Liên hệ (Contact) -->
        <li class="nav-item">
            <a href="/contact/index" class="nav-link {{ strpos($_SERVER['REQUEST_URI'], 'contact') !== false ? 'active' : '' }}">
                <i class="fa-regular fa-envelope"></i>
                Liên hệ
            </a>
        </li>
    </ul>
    
    <div class="admin-sidebar-footer">
        <!-- Tùy chọn xem trang chủ -->
        <a href="/" class="nav-link text-muted" target="_blank" style="padding: 10px 20px; text-transform: none; font-size: 0.9rem; letter-spacing: 0;">
            <i class="fa-solid fa-arrow-up-right-from-square me-2"></i>
            Xem trang Web
        </a>
    </div>
</div>