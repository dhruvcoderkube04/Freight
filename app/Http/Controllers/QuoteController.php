<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\Payment;
use App\Models\TQLResponse;
use App\Models\LocationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuoteController extends Controller
{
    public function index()
    {
        $shipments = Shipment::with(['tqlResponses', 'pickupDetail', 'deliveryDetail', 'commodities'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        $locationTypes = LocationType::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('quotes.index', compact('shipments', 'locationTypes'));
    }

    public function show($id)
    {
        $id = decrypt($id);
        $shipment = Shipment::with(['tqlResponses', 'pickupDetail', 'deliveryDetail', 'commodities'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $latestResponse = $shipment->tqlResponses->last();

        $locationTypes = LocationType::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('quotes.show', compact('shipment', 'latestResponse', 'locationTypes'));
    }

    public function showPaymentForm(Request $request, $id)
    {
        $id = decrypt($id);
        $shipment = Shipment::with(['tqlResponses', 'pickupDetail', 'deliveryDetail', 'commodities'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $latestResponse = $shipment->tqlResponses->last();

        if (!$latestResponse || $latestResponse->status !== 'success') {
            return redirect()->route('quotes.show', $id)
                ->with('error', 'No valid quote available for payment.');
        }

        $selectedCarrierIndex = $request->get('carrier_index', 0);
        $carriers = $latestResponse->response['content']['carrierPrices'] ?? [];

        if (!isset($carriers[$selectedCarrierIndex])) {
            return redirect()->route('quotes.show', $id)
                ->with('error', 'Selected carrier not found.');
        }

        $selectedCarrier = $carriers[$selectedCarrierIndex];

        return view('payments.create', compact('shipment', 'latestResponse', 'selectedCarrier', 'selectedCarrierIndex'));
    }

    public function processPayment(Request $request, $id)
    {
        $shipment = Shipment::with(['tqlResponses'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $latestResponse = $shipment->tqlResponses->last();

        $validated = $request->validate([
            'selected_carrier_index' => 'required|integer',
            'agree_terms' => 'required|accepted',
        ]);

        $carriers = $latestResponse->response['content']['carrierPrices'] ?? [];
        $carrierIndex = $validated['selected_carrier_index'];

        if (!isset($carriers[$carrierIndex])) {
            return redirect()->back()->with('error', 'Invalid carrier selection.');
        }

        $selectedCarrier = $carriers[$carrierIndex];

        // Extract specific carrier details
        $carrierName = $selectedCarrier['carrier'] ?? 'Unknown Carrier';
        $carrierScac = $selectedCarrier['scac'] ?? 'N/A';
        $isPreferred = $selectedCarrier['isPreferred'] ?? false;
        $isCarrierOfTheYear = $selectedCarrier['isCarrierOfTheYear'] ?? false;
        $customerRate = $selectedCarrier['customerRate'] ?? 0;
        $transitDays = $selectedCarrier['transitDays'] ?? null;
        $serviceLevel = $selectedCarrier['serviceLevelDescription'] ?? $selectedCarrier['serviceLevel'] ?? 'Standard';
        $serviceType = $selectedCarrier['serviceType'] ?? null;
        $maxLiabilityNew = $selectedCarrier['maxLiabilityNew'] ?? null;
        $maxLiabilityUsed = $selectedCarrier['maxLiabilityUsed'] ?? null;
        $priceCharges = $selectedCarrier['priceCharges'] ?? null;

        // Check if payment requires admin approval
        $requiresApproval = $customerRate > 1000; // Example: Require approval for amounts over $1000

        // Create payment record with simplified structure
        $payment = Payment::create([
            'shipment_id' => $shipment->id,
            'user_id' => Auth::id(),

            // Carrier specific details
            'carrier_name' => $carrierName,
            'carrier_scac' => $carrierScac,
            'is_preferred' => $isPreferred,
            'is_carrier_of_the_year' => $isCarrierOfTheYear,
            'customer_rate' => $customerRate,
            'transit_days' => $transitDays,
            'service_level' => $serviceLevel,
            'service_type' => $serviceType,
            'max_liability_new' => $maxLiabilityNew,
            'max_liability_used' => $maxLiabilityUsed,
            'price_charges' => $priceCharges ? json_encode($priceCharges) : null,

            // Payment status
            'payment_status' => $requiresApproval ? 'requires_approval' : 'pending',
            'requires_approval' => $requiresApproval,

            // Amount details
            'currency' => 'usd',
            'amount' => $customerRate,
            'tax_amount' => 0,
            'total_amount' => $customerRate,
        ]);

        if ($requiresApproval) {
            return redirect()->route('payments.status', $payment->id)
                ->with('success', 'Your payment requires admin approval. We will notify you once it\'s approved.');
        }

        // Redirect to Stripe payment if no approval needed
        return redirect()->route('payments.process', $payment->id);
    }

    public function paymentStatus($paymentId)
    {
        $payment = Payment::with(['shipment', 'shipment.pickupDetail', 'shipment.deliveryDetail'])
            ->where('user_id', Auth::id())
            ->findOrFail($paymentId);

        return view('payments.status', compact('payment'));
    }
}