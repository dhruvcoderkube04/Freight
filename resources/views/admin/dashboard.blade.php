@extends('admin.layouts.base')

@section('content')
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <!-- Toolbar -->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center me-3">
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        Dashboard
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">Home</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="{{ route('admin.quotes') }}" class="btn btn-sm fw-bold btn-secondary">View Quotes</a>
                    <a href="{{ route('admin.tql-responses') }}" class="btn btn-sm fw-bold btn-primary">TQL Responses</a>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-fluid">

                <!-- Stats Cards -->
                <div class="row g-5 g-xl-8 mb-8">
                    <div class="col-xl-3">
                        <div class="card card-flush bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <i class="ki-duotone ki-file fs-2x text-white me-4"><span class="path1"></span><span class="path2"></span></i>
                                    <div>
                                        <div class="fs-2hx fw-bold">{{ number_format($totalQuotes) }}</div>
                                        <div class="fw-semibold opacity-75">Total Quotes</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3">
                        <div class="card card-flush bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <i class="ki-duotone ki-check-circle fs-2x text-white me-4"><span class="path1"></span><span class="path2"></span></i>
                                    <div>
                                        <div class="fs-2hx fw-bold">{{ $quoteSuccessRate }}%</div>
                                        <div class="fw-semibold opacity-75">Quote Success Rate</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3">
                        <div class="card card-flush bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <i class="ki-duotone ki-dollar fs-2x text-white me-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                    <div>
                                        <div class="fs-2hx fw-bold">${{ number_format($monthlyData[5]['revenue'] ?? 0, 0) }}</div>
                                        <div class="fw-semibold opacity-75">This Month Revenue</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3">
                        <div class="card card-flush bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <i class="ki-duotone ki-truck fs-2x text-white me-4"><span class="path1"></span><span class="path2"></span></i>
                                    <div>
                                        <div class="fs-2hx fw-bold">{{ $successfulTQLResponses }}</div>
                                        <div class="fw-semibold opacity-75">Successful TQL</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row g-5 g-xl-8 mb-8">
                    <!-- Quote Status Pie Chart -->
                    <div class="col-xl-6">
                        <div class="card card-flush h-md-100">
                            <div class="card-header">
                                <h3 class="card-title">Quote Status Distribution</h3>
                            </div>
                            <div class="card-body pt-4">
                                <canvas id="quoteStatusChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Trends -->
                    <div class="col-xl-6">
                        <div class="card card-flush h-md-100">
                            <div class="card-header">
                                <h3 class="card-title">Monthly Quotes & Revenue</h3>
                            </div>
                            <div class="card-body pt-4">
                                <canvas id="monthlyTrendsChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TQL + Top Carriers -->
                <div class="row g-5 g-xl-8 mb-8">
                    <div class="col-xl-4">
                        <div class="card card-flush h-md-100">
                            <div class="card-header">
                                <h3 class="card-title">TQL Response Status</h3>
                            </div>
                            <div class="card-body d-flex justify-content-center pt-4">
                                <canvas id="tqlStatusChart" width="250" height="250"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-8">
                        <div class="card card-flush h-md-100">
                            <div class="card-header">
                                <h3 class="card-title">Top 10 Carriers (by Quote Volume)</h3>
                            </div>
                            <div class="card-body pt-4">
                                <div id="topCarriersChart" class="h-300px"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Quotes Table -->
                <div class="card card-flush mb-8">
                    <div class="card-header">
                        <h3 class="card-title">Recent Quotes</h3>
                    </div>
                    <div class="card-body py-4">
                        <div class="table-responsive">
                            <table class="table table-striped gy-4 gs-7">
                                <thead>
                                    <tr class="fw-bold fs-6 text-gray-800">
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Origin → Destination</th>
                                        <th>Status</th>
                                        <th>TQL Responses</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentQuotes as $quote)
                                    <tr>
                                        <td>#{{ $quote->id }}</td>
                                        <td>{{ $quote->user->fullname ?? 'N/A' }}</td>
                                        <td>
                                            {{ $quote->pickupDetail->city ?? 'N/A' }}, {{ $quote->pickupDetail->state ?? '' }}
                                            → {{ $quote->deliveryDetail->city ?? 'N/A' }}, {{ $quote->deliveryDetail->state ?? '' }}
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $quote->status == 'completed' ? 'success' : ($quote->status == 'failed' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($quote->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $quote->tqlResponses->count() }}</td>
                                        <td>{{ $quote->created_at->format('M d, Y') }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="6" class="text-center">No quotes found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Carrier Pricing Table -->
                <div class="card card-flush">
                    <div class="card-header">
                        <h3 class="card-title">Latest Carrier Pricing</h3>
                    </div>
                    <div class="card-body py-4">
                        <table id="carrierDataTable" class="table table-striped table-row-bordered gy-5 gs-7">
                            <thead>
                                <tr class="fw-bold fs-6 text-gray-800">
                                    <th>Quote ID</th>
                                    <th>User</th>
                                    <th>Carrier</th>
                                    <th>SCAC</th>
                                    <th>Service</th>
                                    <th>Rate</th>
                                    <th>Transit Days</th>
                                    <th>Preferred</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($carrierData as $data)
                                <tr>
                                    <td>{{ $data['quote_id'] }}</td>
                                    <td>{{ Str::limit($data['user_name'], 20) }}</td>
                                    <td>{{ $data['carrier'] }}</td>
                                    <td><code>{{ $data['carrier_scac'] }}</code></td>
                                    <td>{{ $data['service_level'] }}</td>
                                    <td>${{ number_format($data['customer_rate'], 2) }}</td>
                                    <td>{{ $data['transit_days'] }}</td>
                                    <td>
                                        @if($data['is_preferred'] || $data['is_carrier_of_the_year'])
                                            <i class="ki-duotone ki-check-circle text-success fs-2"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-success">Success</span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($data['created_at'])->format('M d, H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Footer -->
    <div id="kt_app_footer" class="app-footer">
        <div class="app-container container-fluid d-flex flex-column flex-md-row flex-center flex-md-stack py-3">
            <div class="text-gray-900 order-2 order-md-1">
                <span class="text-muted fw-semibold me-1">2025&copy;</span>
                <a href="https://keenthemes.com/" target="_blank" class="text-gray-800 text-hover-primary">{{ config('app.name') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // Quote Status Pie Chart
    new Chart(document.getElementById('quoteStatusChart'), {
        type: 'pie',
        data: {
            labels: ['Completed', 'Failed', 'Processing', 'Pending'],
            datasets: [{
                data: [{{ $quoteStatusDistribution['completed'] }}, {{ $quoteStatusDistribution['failed'] }}, {{ $quoteStatusDistribution['processing'] }}, {{ $quoteStatusDistribution['pending'] }}],
                backgroundColor: ['#28a745', '#dc3545', '#ffc107', '#6c757d']
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    // Monthly Trends
    new Chart(document.getElementById('monthlyTrendsChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($monthlyData, 'month')) !!},
            datasets: [
                {
                    label: 'Total Quotes',
                    data: {!! json_encode(array_column($monthlyData, 'quotes')) !!},
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Revenue ($)',
                    data: {!! json_encode(array_column($monthlyData, 'revenue')) !!},
                    borderColor: '#f0932b',
                    backgroundColor: 'rgba(240, 147, 43, 0.1)',
                    yAxisID: 'y1',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true },
                y1: { position: 'right', beginAtZero: true, ticks: { callback: v => '$' + v.toLocaleString() } }
            }
        }
    });

    // TQL Status Doughnut
    new Chart(document.getElementById('tqlStatusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Success', 'Failed', 'Pending'],
            datasets: [{
                data: [{{ $successfulTQLResponses }}, {{ $failedTQLResponses }}, {{ $pendingTQLResponses }}],
                backgroundColor: ['#28a745', '#dc3545', '#ffc107']
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    // Top Carriers Bar Chart
    new ApexCharts(document.querySelector("#topCarriersChart"), {
        series: [{ name: 'Quotes', data: {!! json_encode(array_values($topCarriers->toArray())) !!} }],
        chart: { type: 'bar', height: 300 },
        plotOptions: { bar: { horizontal: true } },
        xaxis: { categories: {!! json_encode($topCarriers->keys()->toArray()) !!} },
        colors: ['#667eea']
    }).render();

    // DataTable
    $(document).ready(function() {
        $('#carrierDataTable').DataTable({
            order: [[0, 'desc']],
            pageLength: 10,
            responsive: true
        });
    });
</script>
@endsection