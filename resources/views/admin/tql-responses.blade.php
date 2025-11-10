@extends('layouts.admin')

@section('title', 'Admin - TQL Responses')
@section('page-title', 'TQL API Responses')

@section('content')
    <div class="chart-container">
        <div class="table-responsive">
            <table id="tqlResponsesTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Quote ID</th>
                        <th>User</th>
                        <th>TQL Quote ID</th>
                        <th>Status</th>
                        <th>Status Code</th>
                        <th>Error Message</th>
                        <th>Carriers Count</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($responses as $response)
                        <tr>
                            <td>{{ $response->id }}</td>
                            <td>{{ $response->quote_id }}</td>
                            <td>{{ $response->quote->user->fullname ?? 'N/A' }}</td>
                            <td>{{ $response->tql_quote_id ?? 'N/A' }}</td>
                            <td>
                                @if ($response->status === 'success')
                                    <span class="badge bg-success">Success</span>
                                @elseif($response->status === 'failed')
                                    <span class="badge bg-danger">Failed</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if ($response->status_code)
                                    <span class="badge bg-{{ $response->status_code == 200 ? 'success' : 'danger' }}">
                                        {{ $response->status_code }}
                                    </span>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if ($response->error_message)
                                    <span class="text-danger" title="{{ $response->error_message }}">
                                        {{ strlen($response->error_message) > 50 ? substr($response->error_message, 0, 50) . '...' : $response->error_message }}
                                    </span>
                                @else
                                    <span class="text-success">-</span>
                                @endif
                            </td>
                            <td>
                                @if ($response->status === 'success' && isset($response->response['content']['carrierPrices']))
                                    {{ count($response->response['content']['carrierPrices']) }}
                                @else
                                    0
                                @endif
                            </td>
                            <td>{{ $response->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                    data-bs-target="#responseModal{{ $response->id }}">
                                    <i class="fas fa-eye"></i> View JSON
                                </button>
                            </td>
                        </tr>

                        <!-- Modal for JSON Response -->
                        <div class="modal fade" id="responseModal{{ $response->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">TQL Response #{{ $response->id }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <pre style="max-height: 500px; overflow-y: auto; background: #f8f9fa; padding: 15px; border-radius: 5px;"><code>{{ json_encode($response->response, JSON_PRETTY_PRINT) }}</code></pre>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $responses->links() }}
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#tqlResponsesTable').DataTable({
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
