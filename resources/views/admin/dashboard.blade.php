@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard Overview')

@push('styles')
    <style>
        .stat-card .stat-icon.blue {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .stat-card .stat-icon.green {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }

        .stat-card .stat-icon.orange {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .stat-card .stat-icon.purple {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }

        .percentage-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 10px;
        }

        .percentage-badge.success {
            background: #d4edda;
            color: #155724;
        }

        .percentage-badge.warning {
            background: #fff3cd;
            color: #856404;
        }

        .percentage-badge.danger {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
@endpush

@section('content')
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <h3>{{ $totalQuotes }}</h3>
                <p>Total Quotes</p>
                <span class="percentage-badge success">Success: {{ $quoteSuccessRate }}%</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-users"></i>
                </div>
                <h3>{{ $totalUsers }}</h3>
                <p>Total Users</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-credit-card"></i>
                </div>
                <h3>{{ $totalPayments }}</h3>
                <p>Total Payments</p>
                <span class="percentage-badge success">Completed: {{ $paymentCompletionRate }}%</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fas fa-database"></i>
                </div>
                <h3>{{ $totalTQLResponses }}</h3>
                <p>TQL Responses</p>
                <span class="percentage-badge success">Success: {{ $tqlSuccessRate }}%</span>
            </div>
        </div>
    </div>

    <!-- Status Breakdown -->
    <div class="row">
        <div class="col-md-6">
            <div class="chart-container">
                <h5>Quote Status Distribution</h5>
                <canvas id="quoteStatusChart" height="100"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="chart-container">
                <h5>Monthly Quotes Trend</h5>
                <canvas id="monthlyQuotesChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="chart-container">
                <h5>Monthly Revenue</h5>
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="chart-container">
                <h5>TQL Response Status</h5>
                <canvas id="tqlStatusChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- TQL Response Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="chart-container">
                <h5>Carrier Quotes from TQL Responses</h5>
                <div class="table-responsive">
                    <table id="carrierDataTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Quote ID</th>
                                <th>User Name</th>
                                <th>User Email</th>
                                <th>TQL Quote ID</th>
                                <th>Carrier</th>
                                <th>Carrier SCAC</th>
                                <th>Service Level</th>
                                <th>Customer Rate</th>
                                <th>Transit Days</th>
                                <th>Preferred</th>
                                <th>Status</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($carrierData as $data)
                                <tr>
                                    <td>{{ $data['quote_id'] }}</td>
                                    <td>{{ $data['user_name'] }}</td>
                                    <td>{{ $data['user_email'] }}</td>
                                    <td>{{ $data['tql_quote_id'] }}</td>
                                    <td>{{ $data['carrier'] }}</td>
                                    <td>{{ $data['carrier_scac'] }}</td>
                                    <td>{{ $data['service_level'] }}</td>
                                    <td>${{ number_format($data['customer_rate'], 2) }}</td>
                                    <td>{{ $data['transit_days'] }}</td>
                                    <td>
                                        @if ($data['is_preferred'])
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($data['status'] === 'success')
                                            <span class="badge bg-success">Success</span>
                                        @elseif($data['status'] === 'failed')
                                            <span class="badge bg-danger">Failed</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($data['created_at'] instanceof \Carbon\Carbon)
                                            {{ $data['created_at']->format('Y-m-d H:i') }}
                                        @else
                                            {{ $data['created_at'] ?? 'N/A' }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @if (count($carrierData) === 0)
                                <tr>
                                    <td colspan="12" class="text-center">No carrier data available</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Quotes -->
    <div class="row">
        <div class="col-12">
            <div class="chart-container">
                <h5>Recent Quotes</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Pickup Location</th>
                                <th>Delivery Location</th>
                                <th>Shipment Date</th>
                                <th>Status</th>
                                <th>TQL Responses</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentQuotes->take(10) as $quote)
                                <tr>
                                    <td>{{ $quote->id }}</td>
                                    <td>{{ $quote->user->fullname ?? 'N/A' }}</td>
                                    <td>
                                        {{ $quote->pickupDetail->city ?? 'N/A' }},
                                        {{ $quote->pickupDetail->state ?? 'N/A' }}
                                    </td>
                                    <td>
                                        {{ $quote->deliveryDetail->city ?? 'N/A' }},
                                        {{ $quote->deliveryDetail->state ?? 'N/A' }}
                                    </td>
                                    <td>{{ $quote->shipment_date }}</td>
                                    <td>
                                        @if ($quote->status === 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($quote->status === 'failed')
                                            <span class="badge bg-danger">Failed</span>
                                        @elseif($quote->status === 'processing')
                                            <span class="badge bg-warning">Processing</span>
                                        @else
                                            <span class="badge bg-secondary">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $quote->tqlResponses->count() }}</td>
                                    <td>{{ $quote->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Quote Status Pie Chart
        const quoteStatusCtx = document.getElementById('quoteStatusChart').getContext('2d');
        new Chart(quoteStatusCtx, {
            type: 'pie',
            data: {
                labels: ['Completed', 'Failed', 'Processing', 'Pending'],
                datasets: [{
                    data: [
                        {{ $quoteStatusDistribution['completed'] }},
                        {{ $quoteStatusDistribution['failed'] }},
                        {{ $quoteStatusDistribution['processing'] }},
                        {{ $quoteStatusDistribution['pending'] }}
                    ],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(220, 53, 69, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(108, 117, 125, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Monthly Quotes Line Chart
        const monthlyQuotesCtx = document.getElementById('monthlyQuotesChart').getContext('2d');
        new Chart(monthlyQuotesCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_column($monthlyData, 'month')) !!},
                datasets: [{
                    label: 'Total Quotes',
                    data: {!! json_encode(array_column($monthlyData, 'quotes')) !!},
                    borderColor: 'rgba(102, 126, 234, 1)',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Successful Quotes',
                    data: {!! json_encode(array_column($monthlyData, 'successful_quotes')) !!},
                    borderColor: 'rgba(40, 167, 69, 1)',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_column($monthlyData, 'month')) !!},
                datasets: [{
                    label: 'Revenue ($)',
                    data: {!! json_encode(array_column($monthlyData, 'revenue')) !!},
                    backgroundColor: 'rgba(255, 193, 7, 0.8)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // TQL Status Chart
        const tqlStatusCtx = document.getElementById('tqlStatusChart').getContext('2d');
        new Chart(tqlStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Success', 'Failed', 'Pending'],
                datasets: [{
                    data: [
                        {{ $successfulTQLResponses }},
                        {{ $failedTQLResponses }},
                        {{ $pendingTQLResponses }}
                    ],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(220, 53, 69, 0.8)',
                        'rgba(255, 193, 7, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // DataTable for Carrier Data
        $(document).ready(function() {
            $('#carrierDataTable').DataTable({
                order: [
                    [11, 'desc']
                ],
                pageLength: 25,
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)"
                }
            });
        });
    </script>
@endpush
