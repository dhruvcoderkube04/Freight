@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-credit-card me-2"></i>Payment Confirmation</h4>
                </div>
                <div class="card-body">
                    <!-- Selected Carrier Summary -->
                    <div class="alert alert-info">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-2">Selected Carrier: {{ $selectedCarrier['carrier'] }}</h6>
                                <p class="mb-1"><strong>SCAC:</strong> {{ $selectedCarrier['scac'] }}</p>
                                <p class="mb-1"><strong>Service:</strong> {{ $selectedCarrier['serviceLevelDescription'] ?? $selectedCarrier['serviceLevel'] ?? 'Standard' }}</p>
                                <p class="mb-1"><strong>Transit Days:</strong> {{ $selectedCarrier['transitDays'] ?? 'N/A' }}</p>
                                @if($selectedCarrier['isPreferred'])
                                    <span class="badge bg-warning">Preferred Carrier</span>
                                @endif
                                @if($selectedCarrier['isCarrierOfTheYear'])
                                    <span class="badge bg-info ms-1">Carrier of the Year</span>
                                @endif
                            </div>
                            <div class="text-end">
                                <strong class="text-success h3">${{ number_format($selectedCarrier['customerRate'], 2) }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- User Information -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-user me-2"></i>Your Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Name:</strong> {{ auth()->user()->name }}</p>
                                    <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                                </div>
                                <div class="col-md-6">
                                    @if(auth()->user()->phone)
                                        <p><strong>Phone:</strong> {{ auth()->user()->phone }}</p>
                                    @endif
                                    @if(auth()->user()->address)
                                        <p><strong>Address:</strong> {{ auth()->user()->address }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quote Information -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-shipping-fast me-2"></i>Quote Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Pickup Location</h6>
                                    @if($quote->pickupDetail)
                                        <p class="mb-1"><strong>Address:</strong> {{ $quote->pickupDetail->address_1 ?: 'Not provided' }}</p>
                                        <p class="mb-1"><strong>City/State:</strong> {{ $quote->pickupDetail->city }}, {{ $quote->pickupDetail->state }}</p>
                                        <p class="mb-0"><strong>Postal Code:</strong> {{ $quote->pickupDetail->postal_code ?: 'Not provided' }}</p>
                                    @else
                                        <p class="text-muted fst-italic">Pickup details not available</p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <h6>Delivery Location</h6>
                                    @if($quote->deliveryDetail)
                                        <p class="mb-1"><strong>Address:</strong> {{ $quote->deliveryDetail->address_1 ?: 'Not provided' }}</p>
                                        <p class="mb-1"><strong>City/State:</strong> {{ $quote->deliveryDetail->city }}, {{ $quote->deliveryDetail->state }}</p>
                                        <p class="mb-0"><strong>Postal Code:</strong> {{ $quote->deliveryDetail->postal_code ?: 'Not provided' }}</p>
                                    @else
                                        <p class="text-muted fst-italic">Delivery details not available</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Summary -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Payment Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Carrier Charge:</span>
                                <span>${{ number_format($baseRate, 2) }}</span>
                            </div>

                            @if($markupPercent > 0)
                                <div class="d-flex justify-content-between mb-2 text-primary">
                                    <span>Platform Fee ({{ $markupPercent }}%):</span>
                                    <span><strong>${{ number_format($markupAmount, 2) }}</strong></span>
                                </div>
                            @endif

                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax (0%):</span>
                                <span>$0.00</span>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between fw-bold fs-5">
                                <span>Total Amount:</span>
                                <span class="text-success">${{ number_format($finalTotal, 2) }}</span>
                            </div>

                            @if($markupPercent > 0)
                                <small class="text-muted d-block mt-2">
                                    Includes {{ $markupPercent }}% platform fee
                                </small>
                            @endif
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <form id="paymentForm" action="{{ route('quotes.payment.process', encrypt($quote->id)) }}" method="POST">
                        @csrf
                        <input type="hidden" name="selected_carrier_index" value="{{ $selectedCarrierIndex }}">

                        <!-- Terms and Conditions -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="agree_terms" name="agree_terms" required 
                                       {{ old('agree_terms') ? 'checked' : '' }}>
                                <label class="form-check-label" for="agree_terms">
                                    I agree to the <a href="#" target="_blank">Terms and Conditions</a>, 
                                    <a href="#" target="_blank">Privacy Policy</a>, and 
                                    <a href="#" target="_blank">Shipping Agreement</a>
                                </label>
                                @error('agree_terms')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('quotes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Quote
                            </a>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-lock me-2"></i>Proceed to Secure Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="mt-4 text-center">
                <p class="text-muted small">
                    <i class="fas fa-shield-alt me-1"></i>
                    Your payment information is secure and encrypted. We do not store your credit card details.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('paymentForm');
    form.addEventListener('submit', function(e) {
        const agreeTerms = document.getElementById('agree_terms');
        if (!agreeTerms.checked) {
            e.preventDefault();
            alert('Please agree to the terms and conditions to proceed.');
            agreeTerms.focus();
        }
    });
});
</script>
@endpush