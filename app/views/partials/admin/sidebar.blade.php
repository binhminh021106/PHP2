<div class="d-flex flex-column flex-shrink-0 p-3 bg-dark text-white" style="width: 260px; height: 100vh;">
    <a href="/admin/dashboard" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none px-2">
        <i class="fa-solid fa-shield-halved fa-2x me-3 text-primary"></i>
        <span class="fs-4 fw-bold">Admin Panel</span>
    </a>
    <hr class="border-secondary">
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-1">
            <a href="/dashboard" class="nav-link text-white {{ strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false ? 'active' : '' }}">
                <i class="fa-solid fa-gauge me-3" style="width: 20px;"></i>
                Tổng quan
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="/category/index" class="nav-link text-white {{ strpos($_SERVER['REQUEST_URI'], 'category') !== false ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group me-3" style="width: 20px;"></i>
                Danh mục
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="/product/index" class="nav-link text-white {{ strpos($_SERVER['REQUEST_URI'], 'product') !== false ? 'active' : '' }}">
                <i class="fa-solid fa-box-open me-3" style="width: 20px;"></i>
                Sản phẩm
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="#" class="nav-link text-white">
                <i class="fa-solid fa-users me-3" style="width: 20px;"></i>
                Khách hàng
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="#" class="nav-link text-white">
                <i class="fa-solid fa-cart-shopping me-3" style="width: 20px;"></i>
                Đơn hàng
            </a>
        </li>
    </ul>
    <hr class="border-secondary">
    <div class="small text-white-50 px-2">
        &copy; 2024 MyShop System
    </div>
</div>