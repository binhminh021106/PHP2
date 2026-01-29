<div class="d-flex flex-column flex-shrink-0 p-3 bg-dark text-white" style="width: 260px; min-height: 100vh;">
    <a href="/admin/dashboard" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none px-2">
        <i class="fa-solid fa-shield-halved fa-2x me-3 text-primary"></i>
        <span class="fs-4 fw-bold">Admin Panel</span>
    </a>
    <hr class="border-secondary">

    <ul class="nav nav-pills flex-column mb-auto">
        <!-- Dashboard -->
        <li class="nav-item mb-1">
            <a href="/dashboard" class="nav-link text-white {{ strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false ? 'active' : '' }}">
                <i class="fa-solid fa-gauge me-3" style="width: 20px;"></i>
                Tổng quan
            </a>
        </li>

        <!-- Danh mục (Category) -->
        <li class="nav-item mb-1">
            <a href="/category/index" class="nav-link text-white {{ strpos($_SERVER['REQUEST_URI'], 'category') !== false ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group me-3" style="width: 20px;"></i>
                Danh mục
            </a>
        </li>

        <!-- Sản phẩm (Product) -->
        <li class="nav-item mb-1">
            <a href="/product/index" class="nav-link text-white {{ strpos($_SERVER['REQUEST_URI'], 'product') !== false ? 'active' : '' }}">
                <i class="fa-solid fa-box-open me-3" style="width: 20px;"></i>
                Sản phẩm
            </a>
        </li>

        <!-- Thương hiệu (Brand) -->
        <li class="nav-item mb-1">
            <a href="/brand/index" class="nav-link text-white {{ strpos($_SERVER['REQUEST_URI'], 'brand') !== false ? 'active' : '' }}">
                <i class="fa-solid fa-copyright me-3" style="width: 20px;"></i>
                Thương hiệu
            </a>
        </li>

        <!-- Mã giảm giá (Coupon) - Đã thêm dựa trên Controller của bạn -->
        <li class="nav-item mb-1">
            <a href="/coupon/index" class="nav-link text-white {{ strpos($_SERVER['REQUEST_URI'], 'coupon') !== false ? 'active' : '' }}">
                <i class="fa-solid fa-ticket me-3" style="width: 20px;"></i>
                Mã giảm giá
            </a>
        </li>

        <!-- Khách hàng (User) - Đã cập nhật link -->
        <li class="nav-item mb-1">
            <a href="/user/index" class="nav-link text-white {{ strpos($_SERVER['REQUEST_URI'], 'user') !== false ? 'active' : '' }}">
                <i class="fa-solid fa-users me-3" style="width: 20px;"></i>
                Khách hàng
            </a>
        </li>
    </ul>
</div>