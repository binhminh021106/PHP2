@extends('layouts.admin')

@section('title', $title ?? 'Tổng quan hệ thống')

@section('content')

<style>
    /* Custom Styling cho Dashboard */
    .stat-card {
        background: #fff;
        border: 1px solid #eee;
        padding: 25px 20px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.05) !important;
        border-color: #ddd;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        background: #f8f9fa;
        color: var(--color-dark, #111);
        border: 1px solid #eee;
    }

    .stat-card.accent-card .stat-icon {
        background: var(--color-dark, #111);
        color: white;
        border-color: var(--color-dark, #111);
    }

    .stat-title {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #777;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .stat-value {
        font-family: var(--font-heading, 'Playfair Display', serif);
        font-size: 2rem;
        font-weight: 700;
        color: var(--color-dark, #111);
        margin-bottom: 0;
    }

    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }

    .card-header-custom {
        background-color: #fff;
        border-bottom: 1px solid #eee;
        padding: 20px 25px;
        font-family: var(--font-heading, 'Playfair Display', serif);
        font-size: 1.25rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .table-recent th {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #777;
        font-weight: 600;
        padding: 15px;
    }
    
    .table-recent td {
        padding: 15px;
        vertical-align: middle;
    }
</style>

<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-3">
        <div>
            <h1 class="mt-0 mb-2 font-heading" style="font-weight: 600;">Tổng Quan Hệ Thống</h1>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Theo dõi doanh thu, đơn hàng và hoạt động của cửa hàng.</p>
        </div>
        <div class="text-end d-flex gap-2">
            <span class="badge bg-white text-dark border rounded-0 px-3 py-2 shadow-sm" style="font-size: 0.8rem; letter-spacing: 1px;">
                <i class="fa-regular fa-calendar-days me-2"></i>Tháng {{ date('m/Y') }}
            </span>
            <span class="badge bg-dark rounded-0 px-3 py-2 shadow-sm" style="font-size: 0.8rem; letter-spacing: 1px;">
                <i class="fa-regular fa-calendar me-2"></i>Hôm nay: {{ date('d/m/Y') }}
            </span>
        </div>
    </div>

    <!-- 4 Khối thống kê (Stats Cards) -->
    <div class="row g-4 mb-5">
        <!-- Tổng doanh thu -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card shadow-sm accent-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-title">Tổng Doanh Thu</div>
                        <h3 class="stat-value text-danger">{{ number_format($totalRevenue ?? 0, 0, ',', '.') }}đ</h3>
                    </div>
                    <div class="stat-icon">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                </div>
                <div class="mt-3 text-muted" style="font-size: 0.8rem;">
                    <span class="text-success fw-bold"><i class="fa-solid fa-arrow-trend-up me-1"></i>Đã giao thành công</span>
                </div>
            </div>
        </div>

        <!-- Tổng đơn hàng -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-title">Đơn Hàng</div>
                        <h3 class="stat-value">{{ number_format($totalOrders ?? 0) }}</h3>
                    </div>
                    <div class="stat-icon">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </div>
                </div>
                <div class="mt-3 text-muted" style="font-size: 0.8rem;">
                    <span class="text-danger fw-bold"><i class="fa-solid fa-circle-exclamation me-1"></i>{{ $pendingOrders ?? 0 }}</span> đơn đang chờ xử lý
                </div>
            </div>
        </div>

        <!-- Tổng sản phẩm -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-title">Sản Phẩm</div>
                        <h3 class="stat-value">{{ number_format($totalProducts ?? 0) }}</h3>
                    </div>
                    <div class="stat-icon">
                        <i class="fa-solid fa-tags"></i>
                    </div>
                </div>
                <div class="mt-3 text-muted" style="font-size: 0.8rem;">
                    <span class="text-warning text-dark fw-bold"><i class="fa-solid fa-boxes-stacked me-1"></i>{{ $lowStockProducts ?? 0 }}</span> sản phẩm sắp hết hàng
                </div>
            </div>
        </div>

        <!-- Tổng khách hàng -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-title">Khách Hàng</div>
                        <h3 class="stat-value">{{ number_format($totalUsers ?? 0) }}</h3>
                    </div>
                    <div class="stat-icon">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
                <div class="mt-3 text-muted" style="font-size: 0.8rem;">
                    <span class="text-success fw-bold"><i class="fa-solid fa-user-check me-1"></i>Thành viên hệ thống</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Khu vực Biểu đồ -->
    <div class="row g-4 mb-5">
        <!-- Biểu đồ doanh thu -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100 rounded-0">
                <div class="card-header-custom">
                    @php
                        // Xử lý tham số Filter từ URL
                        $range = $_GET['range'] ?? '7';
                        $chartTitle = '7 ngày qua';
                        if ($range == '30') $chartTitle = 'Tháng này';
                        if ($range == '365') $chartTitle = 'Năm nay';
                    @endphp
                    
                    <span><i class="fa-solid fa-chart-line me-2 text-muted"></i>Biểu đồ Doanh Thu ({{ $chartTitle }})</span>
                    
                    <select class="form-select form-select-sm w-auto rounded-0" style="font-family: var(--font-base); font-size: 0.85rem;" onchange="window.location.href='?range=' + this.value">
                        <option value="7" {{ $range == '7' ? 'selected' : '' }}>7 ngày qua</option>
                        <option value="30" {{ $range == '30' ? 'selected' : '' }}>Tháng này</option>
                        <option value="365" {{ $range == '365' ? 'selected' : '' }}>Năm nay</option>
                    </select>
                </div>
                <div class="card-body p-4">
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Biểu đồ Trạng thái đơn hàng -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100 rounded-0">
                <div class="card-header-custom">
                    <span><i class="fa-solid fa-chart-pie me-2 text-muted"></i>Trạng Thái Đơn Hàng</span>
                </div>
                <div class="card-body p-4 d-flex align-items-center justify-content-center">
                    <div class="chart-container" style="height: 250px;">
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Đơn hàng mới nhất -->
    <div class="card shadow-sm border-0 rounded-0 mb-4">
        <div class="card-header-custom d-flex justify-content-between">
            <span><i class="fa-solid fa-clock-rotate-left me-2 text-muted"></i>Đơn Hàng Gần Đây</span>
            <a href="/adminorder/index" class="btn btn-outline-dark btn-sm rounded-0 px-3" style="font-family: var(--font-base); letter-spacing: 1px;">XEM TẤT CẢ</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-recent align-middle text-center mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="10%">Mã Đơn</th>
                            <th width="20%" class="text-start">Khách Hàng</th>
                            <th width="15%">Ngày Đặt</th>
                            <th width="15%">Tổng Tiền</th>
                            <th width="15%">Thanh Toán</th>
                            <th width="15%">Trạng Thái</th>
                            <th width="10%">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($recentOrders))
                            @foreach($recentOrders as $order)
                            <tr>
                                <td class="fw-bold text-dark">#{{ $order['id'] }}</td>
                                <td class="text-start">
                                    <div class="fw-bold text-dark" style="font-family: var(--font-base);">{{ $order['fullname'] }}</div>
                                </td>
                                <td><span class="text-muted">{{ date('d/m/Y', strtotime($order['created_at'])) }}</span></td>
                                <td><span class="text-danger fw-bold">{{ number_format($order['total_amount'], 0, ',', '.') }}đ</span></td>
                                <td>
                                    <span class="badge bg-light text-dark border text-uppercase" style="font-size: 0.7rem;">
                                        {{ $order['payment_method'] == 'cod' ? 'COD' : 'BANK' }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = 'bg-secondary';
                                        $statusText = 'Không rõ';
                                        switch($order['status']) {
                                            case 'pending': $badgeClass = 'bg-warning text-dark'; $statusText = 'Chờ duyệt'; break;
                                            case 'processing': $badgeClass = 'bg-info text-white'; $statusText = 'Đang xử lý'; break;
                                            case 'shipped': $badgeClass = 'bg-primary'; $statusText = 'Đang giao'; break;
                                            case 'delivered': $badgeClass = 'bg-success'; $statusText = 'Đã giao'; break;
                                            case 'cancelled': $badgeClass = 'bg-danger'; $statusText = 'Đã hủy'; break;
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeClass }} px-2 py-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">{{ $statusText }}</span>
                                </td>
                                <td>
                                    <a href="/adminorder/detail/{{ $order['id'] }}" class="btn btn-sm btn-outline-dark rounded-0 px-2">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">Chưa có đơn hàng nào trong hệ thống.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Nhúng thư viện Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // --- NHẬN DỮ LIỆU TỪ PHP ---
        // Sử dụng PHP thuần để in JSON ra, tránh lỗi syntax highlighting (báo đỏ) trong VSCode
        const revData = <?php echo json_encode($revenueChartData ?? ['labels' => [], 'revenues' => []]); ?>;
        const statData = <?php echo json_encode($orderStatusData ?? [0, 0, 0, 0, 0]); ?>;

        // Cấu hình chung cho Chart
        Chart.defaults.font.family = "'Jost', sans-serif";
        Chart.defaults.color = '#777';

        // 1. BIỂU ĐỒ DOANH THU (LINE CHART)
        const ctxRev = document.getElementById('revenueChart').getContext('2d');
        
        let gradientRev = ctxRev.createLinearGradient(0, 0, 0, 300);
        gradientRev.addColorStop(0, 'rgba(17, 17, 17, 0.2)');   // Đen nhạt
        gradientRev.addColorStop(1, 'rgba(17, 17, 17, 0)');

        new Chart(ctxRev, {
            type: 'line',
            data: {
                labels: revData.labels, // Data nhãn ngày tháng lấy từ Model
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: revData.revenues, // Dữ liệu tiền lấy từ Model
                    borderColor: '#111',
                    backgroundColor: gradientRev,
                    borderWidth: 2,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#111',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#111',
                        padding: 10,
                        titleFont: { size: 13 },
                        bodyFont: { size: 14, weight: 'bold' },
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return new Intl.NumberFormat('vi-VN').format(context.raw) + ' đ';
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        border: { display: false },
                        grid: { color: '#eee' },
                        ticks: {
                            callback: function(value) {
                                return (value >= 1000000) ? (value / 1000000) + 'Tr' : new Intl.NumberFormat('vi-VN').format(value);
                            }
                        }
                    }
                }
            }
        });

        // 2. BIỂU ĐỒ TRẠNG THÁI ĐƠN HÀNG (DOUGHNUT CHART)
        const ctxStatus = document.getElementById('orderStatusChart').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Chờ duyệt', 'Đang xử lý', 'Đang giao', 'Đã giao', 'Đã hủy'],
                datasets: [{
                    data: statData, // Mảng số lượng từng trạng thái lấy từ Model
                    backgroundColor: [
                        '#ffc107', // Warning (Chờ duyệt)
                        '#17a2b8', // Info (Đang xử lý)
                        '#0d6efd', // Primary (Đang giao)
                        '#198754', // Success (Đã giao)
                        '#dc3545'  // Danger (Đã hủy)
                    ],
                    borderWidth: 0,
                    hoverOffset: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%', // Độ rỗng ở giữa
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { size: 12 }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection