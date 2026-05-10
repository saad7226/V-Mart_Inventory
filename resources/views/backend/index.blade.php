@extends('backend.master')
@section('title', 'Dashboard')

@push('style')
<style>
/* ── Dashboard-specific styles ────────────────────────────── */
:root {
    --c1: #FF473D; --c2: #FF7B73;
    --c3: #7C5AC2; --c4: #A29BFE;
    --c5: #FFC400; --c6: #FFD95A;
    --c7: #FF473D; --c8: #7C5AC2;
}

.welcome-banner {
    background: linear-gradient(135deg, #FF473D 0%, #7C5AC2 100%);
    border-radius: 24px;
    padding: 40px;
    margin-bottom: 30px;
    color: #fff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 15px 35px rgba(255, 71, 61, 0.2);
}

.welcome-banner h1 {
    font-weight: 800;
    font-size: 32px;
    margin-bottom: 10px;
    letter-spacing: -1px;
}

.welcome-banner p {
    font-size: 16px;
    opacity: 0.9;
    max-width: 500px;
}

.welcome-banner .banner-img {
    position: absolute;
    right: 50px;
    top: 50%;
    transform: translateY(-50%);
    width: 150px;
    opacity: 0.2;
}

.dash-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; margin-bottom: 30px; }
.dash-grid-2 { display: grid; grid-template-columns: 1.5fr 1fr; gap: 24px; margin-bottom: 24px; }

/* Metric card override */
.metric-card {
    background: #fff;
    border-radius: 24px;
    padding: 30px;
    box-shadow: var(--card-shadow);
    border: 1px solid rgba(255, 143, 163, 0.1);
    position: relative;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    text-decoration: none;
    display: block;
    color: inherit;
}

.metric-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(255, 143, 163, 0.15);
    border-color: rgba(255, 143, 163, 0.3);
}

.metric-icon {
    width: 50px; height: 50px;
    border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
    margin-bottom: 20px;
    color: #fff;
}

.mc-1 .metric-icon { background: linear-gradient(135deg,var(--c1),var(--c2)); }
.mc-2 .metric-icon { background: linear-gradient(135deg,var(--c3),var(--c4)); }
.mc-3 .metric-icon { background: linear-gradient(135deg,var(--c5),var(--c6)); }
.mc-4 .metric-icon { background: linear-gradient(135deg,var(--c7),var(--c8)); }
.mc-5 .metric-icon { background: linear-gradient(135deg,#FF7B73,#FF473D); }
.mc-6 .metric-icon { background: linear-gradient(135deg,#A29BFE,#7C5AC2); }
.mc-7 .metric-icon { background: linear-gradient(135deg,#FFD95A,#FFC400); }
.mc-8 .metric-icon { background: linear-gradient(135deg,#A29BFE,#FF7B73); }

.metric-label {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--text-muted);
    margin-bottom: 8px;
    letter-spacing: 0.5px;
}

.metric-value {
    font-size: 28px;
    font-weight: 800;
    color: var(--text-main);
    line-height: 1;
}

/* Chart cards override */
.chart-card {
    background: #fff;
    border-radius: 24px;
    box-shadow: var(--card-shadow);
    border: 1px solid rgba(255, 143, 163, 0.1);
    overflow: hidden;
}

.chart-card-header {
    padding: 25px 30px;
    border-bottom: 1px solid rgba(255, 143, 163, 0.05);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.chart-title {
    font-size: 18px;
    font-weight: 800;
    color: var(--text-main);
}

.chart-card-body { padding: 30px; }

/* Date picker btn */
.date-range-btn {
    background: #FFF5F7;
    border: 1px solid var(--sidebar-border);
    border-radius: 50px;
    padding: 8px 20px;
    font-size: 13px;
    color: var(--primary);
    cursor: pointer;
    font-weight: 700;
}

@media (max-width: 1200px) {
    .dash-grid { grid-template-columns: repeat(2, 1fr); }
    .dash-grid-2 { grid-template-columns: 1fr; }
}
@media (max-width: 768px) {
    .welcome-banner { padding: 25px; margin-bottom: 20px; }
    .welcome-banner h1 { font-size: 24px; }
    .welcome-banner p { font-size: 14px; }
    .welcome-banner .banner-img { display: none; }
    
    .dash-grid { grid-template-columns: repeat(2, 1fr); gap: 15px; }
    .metric-card { padding: 20px; border-radius: 20px; }
    .metric-value { font-size: 22px; }
    .metric-icon { width: 40px; height: 40px; font-size: 16px; margin-bottom: 15px; }
}

@media (max-width: 480px) {
    .dash-grid { grid-template-columns: 1fr; }
    .welcome-banner h1 { font-size: 20px; }
}
}
</style>
@endpush

@section('content')
<section class="content">
    @can('dashboard_view')
    <div class="container-fluid">

        {{-- ── Welcome Banner ────────────────────────────────── --}}
        <div class="welcome-banner floating">
            <div class="banner-content">
                <h1>Welcome Back, {{ explode(' ', auth()->user()->name)[0] }}! ✨</h1>
                <p>Here's a beautiful overview of your V-Mart inventory and sales for today. Have an amazing productive day!</p>
            </div>
            <i class="fas fa-shopping-basket banner-img"></i>
        </div>

        {{-- ── Row 1: Key Metrics ────────────────────────────── --}}
        <div class="dash-grid">
            {{-- Row 1 --}}
            <div class="metric-card mc-1">
                <div class="metric-icon"><i class="fas fa-file-invoice"></i></div>
                <div class="metric-label">Sale Subtotal</div>
                <div class="metric-value">{{ currency()->symbol ?? '' }}{{ number_format($sub_total, 2) }}</div>
                <div class="mt-2 text-muted small">Before discounts</div>
            </div>

            <div class="metric-card mc-2">
                <div class="metric-icon"><i class="fas fa-shopping-cart"></i></div>
                <div class="metric-label">Total Sales</div>
                <div class="metric-value">{{ currency()->symbol ?? '' }}{{ number_format($total, 2) }}</div>
                <div class="mt-2 text-muted small">Net revenue</div>
            </div>

            <div class="metric-card mc-3">
                <div class="metric-icon"><i class="fas fa-tags"></i></div>
                <div class="metric-label">Total Discount</div>
                <div class="metric-value">{{ currency()->symbol ?? '' }}{{ number_format($discount, 2) }}</div>
                <div class="mt-2 text-muted small">Saved by customers</div>
            </div>

            <div class="metric-card mc-4">
                <div class="metric-icon"><i class="fas fa-clock"></i></div>
                <div class="metric-label">Outstanding Due</div>
                <div class="metric-value">{{ currency()->symbol ?? '' }}{{ number_format($due, 2) }}</div>
                <div class="mt-2 text-muted small">Pending collection</div>
            </div>

            {{-- Row 2 --}}
            <a href="{{ route('backend.admin.customers.index') }}" class="metric-card mc-5">
                <div class="metric-icon"><i class="fas fa-users"></i></div>
                <div class="metric-label">Total Customers</div>
                <div class="metric-value">{{ $total_customer }}</div>
                <div class="mt-2 text-muted small"><i class="fas fa-arrow-right mr-1"></i> View all customers</div>
            </a>

            <a href="{{ route('backend.admin.products.index') }}" class="metric-card mc-6">
                <div class="metric-icon"><i class="fas fa-box"></i></div>
                <div class="metric-label">Total Products</div>
                <div class="metric-value">{{ $total_product }}</div>
                <div class="mt-2 text-muted small"><i class="fas fa-arrow-right mr-1"></i> View all products</div>
            </a>

            <a href="{{ route('backend.admin.orders.index') }}" class="metric-card mc-7">
                <div class="metric-icon"><i class="fas fa-clipboard-list"></i></div>
                <div class="metric-label">Total Orders</div>
                <div class="metric-value">{{ $total_order }}</div>
                <div class="mt-2 text-muted small"><i class="fas fa-arrow-right mr-1"></i> View all orders</div>
            </a>

            <a href="{{ route('backend.admin.sale.report') }}" class="metric-card mc-8">
                <div class="metric-icon"><i class="fas fa-chart-pie"></i></div>
                <div class="metric-label">Items Sold</div>
                <div class="metric-value">{{ $total_sale_item }}</div>
                <div class="mt-2 text-muted small"><i class="fas fa-arrow-right mr-1"></i> View sale items</div>
            </a>
        </div>

        {{-- ── Row 2: Charts ──────────────────────────────────── --}}
        <div class="dash-grid-2">

            <div class="chart-card">
                <div class="chart-card-header">
                    <div>
                        <div class="chart-title">Sales Performance 📈</div>
                    </div>
                    <div class="date-range-btn">
                        <i class="far fa-calendar-alt mr-2"></i>
                        <input type="text" id="reservation" value="{{ $dateRange }}" style="background:transparent; border:none; outline:none; color:inherit; width:180px; font-weight:bold;" readonly>
                    </div>
                </div>
                <div class="chart-card-body">
                    <canvas id="dailySaleLineChart" height="200"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="chart-title">Monthly Overview 📊</div>
                </div>
                <div class="chart-card-body">
                    <canvas id="barChartYear" height="250"></canvas>
                </div>
            </div>

        {{-- ── Row 3: Recent Sales Table ────────────────────────── --}}
        <div class="chart-card mt-4">
            <div class="chart-card-header">
                <div class="chart-title">Recent Sales ✨</div>
                <a href="{{ route('backend.admin.orders.index') }}" class="btn btn-sm btn-primary" style="border-radius: 50px; padding: 5px 20px;">View All</a>
            </div>
            <div class="chart-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background: #f8fafc;">
                            <tr>
                                <th class="pl-4">Order ID</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th class="pr-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_sales as $order)
                            <tr>
                                <td class="pl-4">#{{ $order->id }}</td>
                                <td>{{ $order->customer->name ?? 'Walking Customer' }}</td>
                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                                <td>{{ currency()->symbol ?? '' }}{{ number_format($order->total, 2) }}</td>
                                <td>{{ currency()->symbol ?? '' }}{{ number_format($order->paid, 2) }}</td>
                                <td>
                                    <span class="{{ $order->due > 0 ? 'text-danger' : 'text-success' }} font-weight-bold">
                                        {{ currency()->symbol ?? '' }}{{ number_format($order->due, 2) }}
                                    </span>
                                </td>
                                <td class="pr-4">
                                    <span class="badge {{ $order->due == 0 ? 'badge-success' : 'badge-warning' }}" style="border-radius: 50px; padding: 5px 12px;">
                                        {{ $order->due == 0 ? 'Paid' : 'Partial' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">No recent sales found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endcan
</section>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
/* ── Chart Defaults ─────────────────────────────────────── */
Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
Chart.defaults.font.size = 12;
Chart.defaults.color = '#8E8E8E';

/* ── Daily Sales Line Chart ─────────────────────────────── */
new Chart(document.getElementById('dailySaleLineChart'), {
    type: 'line',
    data: {
        labels: @json($dates),
        datasets: [{
            label: 'Sales',
            data: @json($totalAmounts),
            borderColor: '#FF473D',
            backgroundColor: (ctx) => {
                const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 400);
                g.addColorStop(0, 'rgba(255, 71, 61, 0.4)');
                g.addColorStop(1, 'rgba(255, 71, 61, 0)');
                return g;
            },
            borderWidth: 4,
            pointBackgroundColor: '#fff',
            pointBorderColor: '#FF8FA3',
            pointBorderWidth: 3,
            pointRadius: 6,
            pointHoverRadius: 8,
            fill: true,
            tension: 0.4,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#fff',
                titleColor: '#4A4A4A',
                bodyColor: '#FF8FA3',
                borderColor: '#FFB8C6',
                borderWidth: 1,
                padding: 15,
                cornerRadius: 15,
                displayColors: false,
                callbacks: {
                    label: (c) => ' {{ currency()->symbol ?? "" }}' + Number(c.raw).toLocaleString()
                }
            }
        },
        scales: {
            x: { grid: { display: false }, border: { display: false } },
            y: {
                beginAtZero: true,
                grid: { color: '#FFF5F7', drawBorder: false },
                border: { display: false }
            }
        }
    }
});

/* ── Monthly Bar Chart ──────────────────────────────────── */
new Chart(document.getElementById('barChartYear'), {
    type: 'bar',
    data: {
        labels: @json($months),
        datasets: [{
            label: 'Revenue',
            data: @json($totalAmountMonth),
            backgroundColor: (ctx) => {
                const colors = ['#FF473D','#7C5AC2','#FFC400','#FF7B73','#A29BFE','#FFD95A'];
                return colors[ctx.dataIndex % colors.length];
            },
            borderRadius: 12,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#fff',
                padding: 15,
                cornerRadius: 15,
                displayColors: false,
            }
        },
        scales: {
            x: { grid: { display: false }, border: { display: false } },
            y: {
                beginAtZero: true,
                grid: { color: '#FFF5F7' },
                border: { display: false },
                display: false
            }
        }
    }
});

/* ── Date range picker ──────────────────────────────────── */
$(function () {
    $('#reservation').daterangepicker().on('apply.daterangepicker', function(e, picker) {
        let selectedDateRange = picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD');
        let url = new URL(window.location.href);
        url.searchParams.set('daterange', selectedDateRange);
        window.location.href = url.toString();
    });
});
</script>
@endpush