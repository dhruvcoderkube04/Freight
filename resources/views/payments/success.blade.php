@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-check-circle me-2"></i>Payment Successful!</h4>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success fa-5x mb-3"></i>
                        <h3 class="text-success">Thank You for Your Payment!</h3>
                        <p class="lead">Your shipping payment has been processed successfully.</p>
                    </div>

                    <!-- Payment Details -->
                    <div class="row justify-content-center mb-4">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Payment Details</h5>
                                    <div class="row text-start">
                                        <div class="col-6">
                                            <p><strong>Payment ID:</strong></p>
                                            <p><strong>Amount Paid:</strong></p>
                                            <p><strong>Carrier:</strong></p>
                                            <p><strong>Service Level:</strong></p>
                                        </div>
                                        <div class="col-6">
                                            <p>{{ $payment->id }}</p>
                                            <p class="text-success fw-bold">${{ number_format($payment->total_amount, 2) }}</p>
                                            <p>{{ $payment->carrier_name }}</p>
                                            <p>{{ $payment->service_level }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Next Steps -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>What Happens Next?</h6>
                        <p class="mb-0">
                            Your shipment has been confirmed with {{ $payment->carrier_name }}. 
                            You will receive email updates about your shipment status. 
                            The carrier will contact you to schedule pickup.
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <a href="{{ route('quotes.index') }}" class="btn btn-primary">
                            <i class="fas fa-list me-2"></i>View All Quotes
                        </a>
                        <a href="{{ route('quotes.show', $payment->quote_id) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-eye me-2"></i>View Shipment Details
                        </a>
                        <button onclick="window.print()" class="btn btn-outline-primary">
                            <i class="fas fa-print me-2"></i>Print Receipt
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection