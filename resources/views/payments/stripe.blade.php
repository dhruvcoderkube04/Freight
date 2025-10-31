@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-lock me-2"></i>Secure Payment</h4>
                </div>
                <div class="card-body">
                    <!-- Payment Summary -->
                    <div class="alert alert-info">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $payment->carrier_name }}</h6>
                                <p class="mb-0 small">{{ $payment->service_level }}</p>
                            </div>
                            <div class="text-end">
                                <strong class="h5 text-success">${{ number_format($payment->total_amount, 2) }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Stripe Payment Form -->
                    <form id="payment-form">
                        <div id="payment-element">
                            <!-- Stripe.js will inject the Payment Element here -->
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button id="submit-button" class="btn btn-success btn-lg">
                                <i class="fas fa-lock me-2"></i>
                                <span id="button-text">Pay ${{ number_format($payment->total_amount, 2) }}</span>
                                <span id="button-spinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </div>
                        
                        <div id="payment-message" class="mt-3 text-center"></div>
                    </form>

                    <!-- Cancel Button -->
                    <div class="text-center mt-3">
                        <a href="{{ route('payments.cancel', $payment->id) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times me-1"></i>Cancel Payment
                        </a>
                    </div>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="mt-4 text-center">
                <p class="text-muted small">
                    <i class="fas fa-shield-alt me-1"></i>
                    Powered by Stripe. Your payment information is secure and encrypted.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
const stripe = Stripe('{{ $stripeKey }}');

const options = {
    clientSecret: '{{ $clientSecret }}',
    appearance: {
        theme: 'stripe',
    },
};

const elements = stripe.elements(options);
const paymentElement = elements.create('payment');
paymentElement.mount('#payment-element');

const form = document.getElementById('payment-form');
const submitButton = document.getElementById('submit-button');
const buttonText = document.getElementById('button-text');
const buttonSpinner = document.getElementById('button-spinner');
const paymentMessage = document.getElementById('payment-message');

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // Show loading state
    submitButton.disabled = true;
    buttonText.classList.add('d-none');
    buttonSpinner.classList.remove('d-none');
    paymentMessage.textContent = '';

    const { error } = await stripe.confirmPayment({
        elements,
        confirmParams: {
            return_url: '{{ route("payments.success", $payment->id) }}',
        },
    });

    if (error) {
        paymentMessage.textContent = error.message;
        paymentMessage.className = 'alert alert-danger';
        
        // Reset button state
        submitButton.disabled = false;
        buttonText.classList.remove('d-none');
        buttonSpinner.classList.add('d-none');
    }
});

// Check for payment status on page load
const checkStatus = async () => {
    const clientSecret = new URLSearchParams(window.location.search).get('payment_intent_client_secret');

    if (!clientSecret) {
        return;
    }

    const { paymentIntent } = await stripe.retrievePaymentIntent(clientSecret);

    switch (paymentIntent.status) {
        case 'succeeded':
            paymentMessage.textContent = 'Payment succeeded!';
            paymentMessage.className = 'alert alert-success';
            break;
        case 'processing':
            paymentMessage.textContent = 'Your payment is processing.';
            paymentMessage.className = 'alert alert-info';
            break;
        case 'requires_payment_method':
            paymentMessage.textContent = 'Your payment was not successful, please try again.';
            paymentMessage.className = 'alert alert-danger';
            break;
        default:
            paymentMessage.textContent = 'Something went wrong.';
            paymentMessage.className = 'alert alert-danger';
            break;
    }
};

checkStatus();
</script>
@endpush