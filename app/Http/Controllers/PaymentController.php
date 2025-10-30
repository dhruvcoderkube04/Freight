<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;

class PaymentController extends Controller
{
    public function __construct()
    {        
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function processStripePayment($paymentId)
    {
        $payment = Payment::with(['shipment', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($paymentId);

        if (!$payment->canProcessPayment()) {
            return redirect()->route('payments.status', $payment->id)
                ->with('error', 'Payment cannot be processed at this time.');
        }

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $payment->total_amount * 100,
                'currency' => $payment->currency,
                'metadata' => [
                    'payment_id' => $payment->id,
                    'shipment_id' => $payment->shipment_id,
                    'user_id' => $payment->user_id,
                ],
                'description' => "Shipping payment for Shipment #{$payment->shipment_id}",
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            $payment->update([
                'stripe_payment_intent_id' => $paymentIntent->id,
                'payment_status' => 'processing',
            ]);

            return view('payments.stripe', [
                'payment' => $payment,
                'clientSecret' => $paymentIntent->client_secret,
                'stripeKey' => config('services.stripe.key'),
            ]);

        } catch (ApiErrorException $e) {
            \Log::error('Stripe Payment Error: ' . $e->getMessage());
            
            $payment->update([
                'payment_status' => 'failed',
            ]);

            return redirect()->route('payments.status', $payment->id)
                ->with('error', 'Payment processing failed. Please try again.');
        }
    }

    public function paymentSuccess(Request $request, $paymentId)
    {
        $payment = Payment::with(['shipment', 'shipment.pickupDetail', 'shipment.deliveryDetail'])
            ->where('user_id', Auth::id())
            ->findOrFail($paymentId);

        // Verify the payment with Stripe
        if ($payment->stripe_payment_intent_id) {
            try {
                $paymentIntent = PaymentIntent::retrieve($payment->stripe_payment_intent_id);
                
                if ($paymentIntent->status === 'succeeded') {
                    $payment->update([
                        'payment_status' => 'completed',
                        'stripe_charge_id' => $paymentIntent->latest_charge,
                    ]);

                    // Update shipment status
                    $payment->shipment->update([
                        'status' => 'paid',
                    ]);

                    return view('payments.success', compact('payment'));
                }
            } catch (ApiErrorException $e) {
                \Log::error('Stripe Verification Error: ' . $e->getMessage());
            }
        }

        return redirect()->route('payments.status', $payment->id)
            ->with('error', 'Unable to verify payment. Please contact support.');
    }

    public function paymentCancel($paymentId)
    {
        $payment = Payment::where('user_id', Auth::id())
            ->findOrFail($paymentId);

        // Update payment status to cancelled
        $payment->update([
            'payment_status' => 'cancelled',
        ]);

        return redirect()->route('payments.status', $payment->id)
            ->with('info', 'Payment was cancelled.');
    }

    public function handleStripeWebhook(Request $request)
    {
        // This would handle Stripe webhooks for payment confirmation
        // You'll need to set up webhooks in your Stripe dashboard
        
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $this->handlePaymentSuccess($paymentIntent);
                break;
            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $this->handlePaymentFailure($paymentIntent);
                break;
            default:
                return response()->json(['status' => 'unhandled_event']);
        }

        return response()->json(['status' => 'success']);
    }

    private function handlePaymentSuccess($paymentIntent)
    {
        $payment = Payment::where('stripe_payment_intent_id', $paymentIntent->id)->first();
        
        if ($payment) {
            $payment->update([
                'payment_status' => 'completed',
                'stripe_charge_id' => $paymentIntent->latest_charge,
            ]);

            // Update shipment status
            $payment->shipment->update([
                'status' => 'paid',
            ]);

            // Here you can send email notifications.
        }
    }

    private function handlePaymentFailure($paymentIntent)
    {
        $payment = Payment::where('stripe_payment_intent_id', $paymentIntent->id)->first();
        
        if ($payment) {
            $payment->update([
                'payment_status' => 'failed',
            ]);
        }
    }
}