@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0"><i class="fas fa-clock me-2"></i>Payment Status</h4>
                </div>
                <div class="card-body">
                    @if($payment->requiresAdminApproval())
                        <div class="alert alert-warning">
                            <h5><i class="fas fa-user-shield me-2"></i>Admin Approval Required</h5>
                            <p class="mb-0">Your payment requires administrative approval. We will review your request and notify you once it's approved.</p>
                        </div>
                    @elseif($payment->payment_status === 'completed')
                        <div class="alert alert-success">
                            <h5><i class="fas fa-check-circle me-2"></i>Payment Completed</h5>
                            <p class="mb-0">Your payment has been successfully processed.</p>
                        </div>
                    @endif

                    <!-- Selected Carrier Information -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Selected Carrier</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Carrier:</strong> {{ $payment->carrier_name }}</p>
                                    <p><strong>SCAC:</strong> {{ $payment->carrier_scac }}</p>
                                    <p><strong>Service Level:</strong> {{ $payment->service_level }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Amount:</strong> <span class="h5 text-success">${{ number_format($payment->total_amount, 2) }}</span></p>
                                    <p><strong>Transit Days:</strong> {{ $payment->transit_days ?? 'N/A' }}</p>
                                    @if($payment->is_preferred)
                                        <span class="badge bg-warning">Preferred Carrier</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Details -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Payment Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Payment Status:</strong> 
                                        <span class="badge bg-{{ $payment->payment_status === 'completed' ? 'success' : 'warning' }}">
                                            {{ ucfirst(str_replace('_', ' ', $payment->payment_status)) }}
                                        </span>
                                    </p>
                                    <p><strong>Payment ID:</strong> {{ $payment->id }}</p>
                                    <p><strong>Created:</strong> {{ $payment->created_at->format('M d, Y H:i') }}</p>
                                </div>
                                {{-- <div class="col-md-6">
                                    <p><strong>Billing Name:</strong> {{ $payment->billing_name }}</p>
                                    <p><strong>Billing Email:</strong> {{ $payment->billing_email }}</p>
                                    @if($payment->billing_phone)
                                        <p><strong>Billing Phone:</strong> {{ $payment->billing_phone }}</p>
                                    @endif
                                </div> --}}
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-center">
                        <a href="{{ route('quotes.index') }}" class="btn btn-primary me-2">
                            <i class="fas fa-list me-2"></i>Back to Quotes
                        </a>
                        <a href="{{ route('quotes.index', encrypt($payment->quote_id)) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-eye me-2"></i>View Quote Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection