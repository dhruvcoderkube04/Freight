<?php

namespace App\Http\Controllers;

use App\Models\CountryCode;
use App\Models\FreightClassCode;
use App\Models\LocationType;
use App\Models\Quote;
use App\Models\PickupDetail;
use App\Models\DeliveryDetail;
use App\Models\Commodity;
use App\Models\TQLResponse;
use App\Models\UnitType;
use App\Models\Payment;
use App\Services\TQLApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class QuoteController extends Controller
{
    public function index()
    {
        $locationTypes = LocationType::where('is_active', true)->orderBy('sort_order')->get();
        $countries = CountryCode::where('is_active', true)->orderBy('sort_order')->get();
        $unitTypes = UnitType::where('is_active', true)->orderBy('sort_order')->get();
        $freightClasses = FreightClassCode::where('is_active', true)->orderBy('sort_order')->get();

        $quotes = Quote::with(['tqlResponses', 'pickupDetail', 'deliveryDetail', 'commodities'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('quotes.index', compact(
            'locationTypes',
            'countries',
            'unitTypes',
            'freightClasses',
            'quotes'
        ));
    }

    public function show($id)
    {
        $id = decrypt($id);
        $quote = Quote::with(['tqlResponses', 'pickupDetail', 'deliveryDetail', 'commodities'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $latestResponse = $quote->tqlResponses->last();

        $locationTypes = LocationType::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('quotes.show', compact('quote', 'latestResponse', 'locationTypes'));
    }

    public function storeStep1(Request $request)
    {
        // $validated = $request->validate([
        //     'pickup_location' => 'required|string|in:commercial,residential,limited_access,trade_show',
        //     'drop_location' => 'required|string|in:commercial,residential,limited_access,trade_show',
        //     'shipment_date' => 'required|date|after_or_equal:today',
        // ]);

        $quote = Quote::updateOrCreate(
            ['id' => $request->quote_id],
            [
                'user_id' => Auth::id(),
                'pickup_location' => $request->pickup_location,
                'drop_location' => $request->drop_location,
                'shipment_date' => $request->shipment_date,
            ]
        );

        return response()->json([
            'success' => true,
            'quote_id' => $quote->id,
            'next_step' => 2
        ]);
    }

    public function storeStep2(Request $request)
    {
        // $validated = $request->validate([
        //     'quote_id' => 'required|exists:quotes,id',
        //     'city' => 'required|string|max:255',
        //     'state' => 'required|string|size:2',
        //     'postal_code' => 'required|string|regex:/^\d{5}(-\d{4})?$/',
        //     'country' => 'required|string|in:USA,CAN,MEX',
        //     'address_1' => 'nullable|string|max:50',
        //     'address_2' => 'nullable|string|max:50',
        //     'contact_number' => 'nullable|string|regex:/^\+?1?\d{10}$/',
        // ]);

        $quote = Quote::where('id', $request->quote_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        PickupDetail::updateOrCreate(
            ['quote_id' => $request->quote_id],
            [
                'city' => $request->city ?? 'Not provided',
                'state' => $request->state ?? 'Not provided',
                'postal_code' => $request->postal_code ?? 'Not provided',
                'country' => $request->country ?? 'Not provided',
                'address_1' => $request->address_1 ?? 'Not provided',
                'address_2' => $request->address_2,
                'contact_number' => $request->contact_number ?? 'Not provided',
            ]
        );

        return response()->json([
            'success' => true,
            'next_step' => 3
        ]);
    }

    public function storeStep3(Request $request)
    {
        // $validated = $request->validate([
        //     'quote_id' => 'required|exists:quotes,id',
        //     'city' => 'required|string|max:255',
        //     'state' => 'required|string|size:2',
        //     'postal_code' => 'required|string|regex:/^\d{5}(-\d{4})?$/',
        //     'country' => 'required|string|in:USA,CAN,MEX',
        //     'address_1' => 'nullable|string|max:50',
        //     'address_2' => 'nullable|string|max:50',
        //     'contact_number' => 'nullable|string|regex:/^\+?1?\d{10}$/',
        // ]);

        $quote = Quote::where('id', $request->quote_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        DeliveryDetail::updateOrCreate(
            ['quote_id' => $request->quote_id],
            [
                'city' => $request->city ?? 'Not provided',
                'state' => $request->state ?? 'Not provided',
                'postal_code' => $request->postal_code ?? 'Not provided',
                'country' => $request->country ?? 'Not provided',
                'address_1' => $request->address_1 ?? 'Not provided',
                'address_2' => $request->address_2,
                'contact_number' => $request->contact_number ?? 'Not provided',
            ]
        );

        return response()->json([
            'success' => true,
            'next_step' => 4
        ]);
    }

    public function storeStep4(Request $request)
    {
        // $validated = $request->validate([
        //     'quote_id' => 'required|exists:quotes,id',
        //     'quantity' => 'required|integer|min:1|max:315',
        //     'unit_type' => 'required|string|in:pallet,carton,crate,box,bundle,drum,roll,case,piece',
        //     'freight_class_code' => 'required|string|in:50,55,60,65,70,77.5,85,92.5,100,110,125,150,175,200,250,300,400,500',
        //     'weight' => 'required|numeric|min:0.1',
        //     'length' => 'required|integer|min:1|max:636',
        //     'width' => 'required|integer|min:1|max:102',
        //     'height' => 'required|integer|min:1|max:102',
        //     'additional_services' => 'nullable|array',
        //     'additional_services.*' => 'string|in:devanning,labelling,transshipment',
        // ]);

        $quote = Quote::where('id', $request->quote_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        Commodity::create([
            'quote_id' => $request->quote_id,
            'quantity' => $request->quantity ?? 1,
            'unit_type' => $request->unit_type ?? 'pallet',
            'freight_class_code' => $request->freight_class_code ?? '110',
            'weight' => $request->weight ?? 100,
            'length' => $request->length ?? 48,
            'width' => $request->width ?? 48,
            'height' => $request->height ?? 48,
            'additional_services' => $request->additional_services ? json_encode($request->additional_services) : null,
        ]);

        $quote = Quote::with(['pickupDetail', 'deliveryDetail', 'commodities'])
            ->where('user_id', Auth::id())
            ->find($request->quote_id);

        $tqlService = new TQLApiService();
        $commodity = $quote->commodities->first();

        $apiData = [
            'pickup_location' => $quote->pickup_location,
            'drop_location' => $quote->drop_location,
            'shipment_date' => $quote->shipment_date,
            'pickup_city' => $quote->pickupDetail->city,
            'pickup_state' => $quote->pickupDetail->state,
            'pickup_postal_code' => $quote->pickupDetail->postal_code,
            'pickup_country' => $quote->pickupDetail->country ?? 'USA',
            'delivery_city' => $quote->deliveryDetail->city,
            'delivery_state' => $quote->deliveryDetail->state,
            'delivery_postal_code' => $quote->deliveryDetail->postal_code,
            'delivery_country' => $quote->deliveryDetail->country ?? 'USA',
            'quantity' => $commodity->quantity,
            'unit_type' => $commodity->unit_type,
            'freight_class_code' => $commodity->freight_class_code,
            'weight' => $commodity->weight,
            'length' => $commodity->length,
            'width' => $commodity->width,
            'height' => $commodity->height,
            'additional_services' => $commodity->additional_services ? json_decode($commodity->additional_services, true) : [],
        ];

        $quoteData = $tqlService->formatQuoteData($apiData);

        try {
            $apiResponse = $tqlService->createQuote($quoteData);
        } catch (\Exception $e) {
            // Handle timeout or other exceptions
            $apiResponse = [
                'error' => 'API request timed out or failed',
                'exception' => $e->getMessage(),
                'status_code' => 408 // Request Timeout
            ];
        }

        // Store API response in tql_responses table
        $tqlResponse = TQLResponse::create([
            'quote_id' => $quote->id,
            'response' => $apiResponse,
            'tql_quote_id' => $apiResponse['quoteId'] ?? $apiResponse['id'] ?? null,
            'status_code' => $apiResponse['status_code'] ?? (isset($apiResponse['error']) ? 400 : 200),
            'status' => isset($apiResponse['error']) ? 'failed' : 'success',
            'error_message' => $apiResponse['error'] ?? null,
        ]);

        if (isset($apiResponse['error']) || is_null($apiResponse)) {
            $quote->update(['status' => 'failed']);
            return response()->json([
                'success' => false,
                'message' => 'Quote saved but API call failed',
                'api_response' => $apiResponse,
                'tql_response_id' => $tqlResponse->id,
                'error_details' => $apiResponse['error'] ?? 'API response was null'
            ]);
        }

        $quote->update(['status' => 'completed']);

        return response()->json([
            'success' => true,
            'message' => 'Quote Generated Successfully!',
            'api_response' => $apiResponse,
            'tql_response_id' => $tqlResponse->id,
            'quote_id' => $tqlResponse->tql_quote_id
        ]);
    }

    // Optional: Method to get TQL response for a Quote
    public function getTqlResponse($shipmentId)
    {
        $quote = Quote::where('id', $shipmentId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $tqlResponse = $quote->latestTqlResponse;

        return response()->json([
            'success' => true,
            'tql_response' => $tqlResponse
        ]);
    }

    public function showPaymentForm(Request $request, $id)
    {
        $id = decrypt($id);
        $quote = Quote::with(['tqlResponses', 'pickupDetail', 'deliveryDetail', 'commodities'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $latestResponse = $quote->tqlResponses->last();

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

        return view('payments.create', compact('quote', 'latestResponse', 'selectedCarrier', 'selectedCarrierIndex'));
    }

    public function processPayment(Request $request, $id)
    {
        $quote = Quote::with(['tqlResponses'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $latestResponse = $quote->tqlResponses->last();

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
            'quote_id' => $quote->id,
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
        $payment = Payment::with(['quote', 'quote.pickupDetail', 'quote.deliveryDetail'])
            ->where('user_id', Auth::id())
            ->findOrFail($paymentId);

        return view('payments.status', compact('payment'));
    }
}