<footer class="bg-dark text-white pt-5 pb-3 mt-auto">
    <div class="container">
        <div class="row g-4 justify-content-between">
            <!-- Cột 1: Thông tin -->
            <div class="col-md-4">
                <h5 class="text-uppercase fw-bold ls-1 mb-4">Menswear.</h5>
                <p class="text-secondary small">
                    Chúng tôi mang đến những bộ trang phục nam tính, tối giản nhưng đầy phong cách. Định hình bản lĩnh phái mạnh.
                </p>
                <div class="d-flex gap-3 mt-3">
                    <a href="#" class="text-white"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="text-white"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="text-white"><i class="fa-brands fa-tiktok"></i></a>
                </div>
            </div>

            <!-- Cột 2: Liên kết -->
            <div class="col-md-3">
                <h6 class="text-uppercase fw-bold mb-3">Khám phá</h6>
                <ul class="list-unstyled text-secondary small d-flex flex-column gap-2">
                    <li><a href="#" class="text-decoration-none text-secondary hover-white">Áo sơ mi</a></li>
                    <li><a href="#" class="text-decoration-none text-secondary hover-white">Quần tây & Jeans</a></li>
                    <li><a href="#" class="text-decoration-none text-secondary hover-white">Phụ kiện</a></li>
                    <li><a href="#" class="text-decoration-none text-secondary hover-white">Hàng mới về</a></li>
                </ul>
            </div>

            <!-- Cột 3: Hỗ trợ -->
            <div class="col-md-3">
                <h6 class="text-uppercase fw-bold mb-3">Hỗ trợ khách hàng</h6>
                <ul class="list-unstyled text-secondary small d-flex flex-column gap-2">
                    <li><a href="#" class="text-decoration-none text-secondary hover-white">Chính sách đổi trả</a></li>
                    <li><a href="#" class="text-decoration-none text-secondary hover-white">Hướng dẫn chọn size</a></li>
                    <li><a href="#" class="text-decoration-none text-secondary hover-white">Phí vận chuyển</a></li>
                    <li>Hotline: 0909 123 456</li>
                </ul>
            </div>
        </div>

        <hr class="border-secondary opacity-25 my-4">

        <div class="text-center small text-secondary">
            &copy; {{ date('Y') }} Menswear Store. All rights reserved.
        </div>
    </div>

    <style>
        .hover-white:hover {
            color: #fff !important;
            transition: 0.3s;
        }

        .ls-1 {
            letter-spacing: 2px;
        }
    </style>
</footer>