<style>
    /* Tuỳ chỉnh giao diện Footer Admin */
    .admin-footer {
        font-family: var(--font-base, 'Jost', sans-serif);
        background-color: #fff;
        border-top: 1px solid #eee !important;
        padding: 20px 0;
        margin-top: auto;
    }

    .admin-footer .text-muted {
        color: #777 !important;
        font-size: 0.9rem;
    }

    .admin-footer b {
        color: var(--color-dark, #111);
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .admin-footer a {
        transition: all 0.3s ease;
    }

    .admin-footer a:hover {
        color: var(--color-accent, #c9a47c) !important;
    }
</style>

<footer class="admin-footer text-center text-lg-start">
    <div class="container-fluid px-4">
        <div class="row align-items-center justify-content-between">
            <div class="col-md-6 text-muted small mb-2 mb-md-0 text-md-start">
                Copyright &copy; <?php echo date('Y'); ?> <b>MENSWEAR</b>. All rights reserved.
            </div>
            <div class="col-md-6 text-md-end small">
                <a href="#" class="text-muted text-decoration-none me-4">Chính sách bảo mật</a>
                <a href="#" class="text-muted text-decoration-none">Điều khoản sử dụng</a>
            </div>
        </div>
    </div>
</footer>