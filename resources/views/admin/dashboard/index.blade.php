
@extends('layouts.app')

@section('content')
<div class="dashboard">

    <!-- Header -->
    <div class="dashboard-header">
        <div>
            <h1>Dashboard</h1>
            <p class="dashboard-subtitle">
                Welcome back! Showing data for {{ $subtitle }}.
            </p>
        </div>

        <div class="filter-buttons">
            <a href="{{ route('admin.dashboard.index', ['range' => 'day']) }}" class="button {{ $range === 'day' ? 'active' : '' }}">Day</a>
            <a href="{{ route('admin.dashboard.index', ['range' => 'week']) }}" class="button {{ $range === 'week' ? 'active' : '' }}">Week</a>
            <a href="{{ route('admin.dashboard.index', ['range' => 'month']) }}" class="button {{ $range === 'month' ? 'active' : '' }}">Month</a>
        </div>
    </div>

    <!-- Stats -->
    <div class="summary-cards">

    <div class="stat-card">
        <div class="stat-content">
            <div>
                <span class="stat-title">Total Revenue</span>
                <h2>${{ number_format($totalRevenue, 2) }}</h2>

                <div class="stat-change positive">
                    <i class="fa-solid fa-arrow-trend-up"></i>
                    <span>Today</span>
                </div>
            </div>

            <div class="stat-icon revenue">
                <i class="fa-solid fa-dollar-sign"></i>
            </div>
        </div>
    </div>

    <div class="stat-card" data-url="{{ route('admin.orders.index') }}">
        <div class="stat-content">
            <div>
                <span class="stat-title">Orders</span>
                <h2>{{ $ordersCount }}</h2>

                <div class="stat-change positive">
                    <i class="fa-solid fa-arrow-trend-up"></i>
                    <span>Today</span>
                </div>
            </div>

            <div class="stat-icon order">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
        </div>
    </div>

    <div class="stat-card" >
        <div class="stat-content">
            <div>
                <span class="stat-title">Tables served</span>
                <h2>{{ $customersCount }}</h2>

                <div class="stat-change positive">
                    <i class="fa-solid fa-arrow-trend-up"></i>
                    <span>Today</span>
                </div>
            </div>

            <div class="stat-icon customer">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>
    </div>

    <div class="stat-card" data-url="{{ route('items.index') }}">
        <div class="stat-content">
            <div>
                <span class="stat-title">Menu items</span>
                <h2>{{ $productsCount }}</h2>

                <div class="stat-change negative">
                    <i class="fa-solid fa-box"></i>
                    <span>All items</span>
                </div>
            </div>

            <div class="stat-icon product">
                <i class="fa-solid fa-box"></i>
            </div>
        </div>
    </div>

</div>
    <!-- Charts -->
    <div class="chart-grid">

        <div class="chart-card">
            <div class="chart-header">
                <h3>Sales Overview</h3>
            </div>

            <div id="salesChart"></div>
        </div>

        <div class="chart-card">
            <div class="chart-header">
                <h3>Order Status</h3>
            </div>

            <canvas id="pieChart"></canvas>
        </div>

    </div>

    <!-- Recent Orders -->
    <div class="table-card">

        <div class="chart-header">
            <h3>Recent Orders</h3>
        </div>

        <div class="table-responsive">
            <table>

                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($recentOrders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ optional($order->table)->name ?? 'Guest' }}</td>
                        <td>${{ number_format($order->total, 2) }}</td>
                        <td><span class="badge {{ $order->status == 'completed' ? 'completed' : ($order->status == 'pending' ? 'pending' : 'cancelled') }}">
                            {{ ucfirst($order->status) }}
                        </span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center; padding: 24px;">No recent orders available.</td>
                    </tr>
                    @endforelse

                </tbody>

            </table>
        </div>

    </div>

</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Pie Chart
    const pieElement = document.getElementById('pieChart');

    if (pieElement) {
        new Chart(pieElement, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Pending', 'Cancelled'],
                datasets: [{
                    data: @json([$completedCount, $pendingCount, $cancelledCount]),
                    backgroundColor: [
                        '#10B981',
                        '#F59E0B',
                        '#EF4444'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1,
                cutout: '75%'
            }
        });
    }
    // link direction
    const linkDirection = document.querySelector('.stat-card');
    document.querySelectorAll('.stat-card').forEach(card => {

        card.style.cursor = 'pointer';

        card.addEventListener('click', function () {

            const url = this.dataset.url;

            if (url) {
                window.location.href = url;
            }

        });

    });
    document.querySelectorAll('.stat-card').forEach(card => {

        card.style.cursor = 'pointer';

        card.addEventListener('click', function () {

            if (this.querySelector('.stat-icon.order')) {
                window.location.href = "{{ route('admin.orders.index') }}";
            }

            else if (this.querySelector('.stat-icon.product')) {
                window.location.href = "{{ route('items.index') }}";
            }

            else if (this.querySelector('.stat-icon.customer')) {
                window.location.href = "#";
            }

            else if (this.querySelector('.stat-icon.revenue')) {
                window.location.href = "#";
            }

        });

    });

    // Sales Chart
    const salesElement = document.querySelector('#salesChart');

    if (salesElement) {
        const chart = new ApexCharts(salesElement, {
            series: [{
                name: 'Sales',
                data: @json($salesChartData),
            }],
            chart: {
                type: 'area',
                height: 350,
            },
            xaxis: {
                categories: @json($salesChartLabels),
            },
        });

        chart.render();
    }

});
</script>
@endsection
