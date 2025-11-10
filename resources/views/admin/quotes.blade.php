@extends('layouts.admin')

@section('title', 'Admin - Quotes')
@section('page-title', 'All Quotes')

@section('content')
    <div class="chart-container">
        <div class="table-responsive">
            <table id="quotesTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Pickup Location</th>
                        <th>Delivery Location</th>
                        <th>Shipment Date</th>
                        <th>Status</th>
                        <th>TQL Responses</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($quotes as $quote)
                        <tr>
                            <td>{{ $quote->id }}</td>
                            <td>{{ $quote->user->fullname ?? 'N/A' }}</td>
                            <td>{{ $quote->user->email ?? 'N/A' }}</td>
                            <td>
                                @if ($quote->pickupDetail)
                                    {{ $quote->pickupDetail->city }}, {{ $quote->pickupDetail->state }}
                                    {{ $quote->pickupDetail->postal_code }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if ($quote->deliveryDetail)
                                    {{ $quote->deliveryDetail->city }}, {{ $quote->deliveryDetail->state }}
                                    {{ $quote->deliveryDetail->postal_code }}
                                @else
                                    N/A
                                @endif
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
                            <td>
                                <a href="{{ route('quotes.show', encrypt($quote->id)) }}" class="btn btn-sm btn-primary"
                                    target="_blank">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $quotes->links() }}
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#quotesTable').DataTable({
                order: [
                    [8, 'desc']
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
