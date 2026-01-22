<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top px-4 shadow-sm" style="height: 70px;">
    <div class="d-flex align-items-center">
        <button class="btn btn-light d-lg-none me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
            <i class="fa-solid fa-bars"></i>
        </button>
        <form class="d-none d-md-flex input-group" style="width: 300px;">
            <span class="input-group-text bg-light border-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
            <input type="search" class="form-control bg-light border-0" placeholder="Tìm kiếm...">
        </form>
    </div>

    <div class="collapse navbar-collapse justify-content-end">
        <ul class="navbar-nav align-items-center">
            <li class="nav-item me-3">
                <a class="nav-link position-relative" href="#">
                    <i class="fa-regular fa-bell fa-lg"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                        3
                    </span>
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=0D8ABC&color=fff" class="rounded-circle me-2" width="35" height="35" alt="Avatar">
                    <span class="fw-semibold text-dark">Quản trị viên</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-user me-2 text-muted"></i> Hồ sơ</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-gear me-2 text-muted"></i> Cài đặt</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-danger" href="#"><i class="fa-solid fa-right-from-bracket me-2"></i> Đăng xuất</a></li>
                </ul>
            </li>
        </ul>
    </div>
</nav>