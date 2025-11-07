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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class QuoteController extends Controller
{
    public function index()
    {
        $locationTypes   = LocationType::where('is_active', true)->orderBy('sort_order')->get();
        $countries       = CountryCode::where('is_active', true)->orderBy('sort_order')->get();
        $unitTypes       = UnitType::where('is_active', true)->orderBy('sort_order')->get();
        $freightClasses  = FreightClassCode::where('is_active', true)->orderBy('sort_order')->get();

        $quotes = Quote::with(['tqlResponses', 'pickupDetail', 'deliveryDetail'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        $carrierData = [];
            foreach ($quotes as $quote) {
                $resp = $quote->tqlResponses->last();
                if ($resp?->status === 'success') {
                    foreach ($resp->response['content']['carrierPrices'] as $price) {
                        $carrierData[] = [
                            'quote_id'      => $quote->id,
                            'booking_id'    => $price['carrierQuoteId'] ?? '—',
                            'warehouse'     => $price['carrier'] ?? '—',
                            'storage'       => $price['serviceLevel'] ?? 'Standard',
                            'total_amount'  => $price['customerRate'],
                            'total_space'   => $price['transitDays'] . ' day' . ($price['transitDays'] > 1 ? 's' : ''),
                            'booking_status' => ucfirst($quote->status),
                            'payment_status' => $quote->status,
                        ];
                    }
                }
            }

        return view('quotes.index', compact(
            'locationTypes', 'countries', 'unitTypes', 'freightClasses', 'quotes', 'carrierData'
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

    public function storeQuote(Request $request)
    {
        $validated = $request->validate([
            // Step 1
            'pickup_location' => 'required|string|in:commercial,residential,limited_access,trade_show',
            'drop_location' => 'required|string|in:commercial,residential,limited_access,trade_show',
            'shipment_date' => 'required|date|after_or_equal:today',

            // Step 2 - Pickup
            'pickup_city' => 'required|string|max:255',
            'pickup_state' => 'required|string|size:2',
            'pickup_postal_code' => 'required|string|regex:/^\d{5}(-\d{4})?$/',
            'pickup_country' => 'required|string|in:USA,CAN,MEX',
            'pickup_address_1' => 'required|string|max:50',
            'pickup_address_2' => 'nullable|string|max:50',
            'pickup_contact_number' => 'required|string|regex:/^\+?1?\d{10}$/',

            // Step 3 - Delivery
            'delivery_city' => 'required|string|max:255',
            'delivery_state' => 'required|string|size:2',
            'delivery_postal_code' => 'required|string|regex:/^\d{5}(-\d{4})?$/',
            'delivery_country' => 'required|string|in:USA,CAN,MEX',
            'delivery_address_1' => 'required|string|max:50',
            'delivery_address_2' => 'nullable|string|max:50',
            // 'delivery_contact_number' => 'nullable|string|regex:/^\+?1?\d{10}$/',

            // Step 4 - Commodities
            'quantity' => 'required|integer|min:1|max:315',
            'unit_type' => 'required|string',
            'freight_class_code' => 'required|string',
            'weight' => 'required|numeric|min:0.1',
            'length' => 'required|integer|min:1|max:636',
            'width' => 'required|integer|min:1|max:102',
            'height' => 'required|integer|min:1|max:102',
            'additional_services' => 'nullable|array',
            'additional_services.*' => 'in:devanning,labeling,transshipment',
        ]);

        DB::beginTransaction();
        try {
            // 1. Create Quote
            $quote = Quote::create([
                'user_id' => Auth::id(),
                'pickup_location' => $request->pickup_location,
                'drop_location' => $request->drop_location,
                'shipment_date' => $request->shipment_date,
                'status' => 'processing',
            ]);

            // 2. Pickup Detail
            PickupDetail::create([
                'quote_id' => $quote->id,
                'city' => $request->pickup_city,
                'state' => $request->pickup_state,
                'postal_code' => $request->pickup_postal_code,
                'country' => $request->pickup_country,
                'address_1' => $request->pickup_address_1,
                'address_2' => $request->pickup_address_2,
                'contact_number' => $request->pickup_contact_number,
            ]);

            // 3. Delivery Detail
            DeliveryDetail::create([
                'quote_id' => $quote->id,
                'city' => $request->delivery_city,
                'state' => $request->delivery_state,
                'postal_code' => $request->delivery_postal_code,
                'country' => $request->delivery_country,
                'address_1' => $request->delivery_address_1,
                'address_2' => $request->delivery_address_2,
                // 'contact_number' => $request->delivery_contact_number,
            ]);

            // 4. Commodity
            Commodity::create([
                'quote_id' => $quote->id,
                'quantity' => $request->quantity,
                'unit_type' => $request->unit_type,
                'freight_class_code' => $request->freight_class_code,
                'weight' => $request->weight,
                'length' => $request->length,
                'width' => $request->width,
                'height' => $request->height,
                'additional_services' => $request->additional_services,
            ]);

            // 5. Call TQL API
            $tqlService = new TQLApiService();
            $apiData = [
                'pickup_location' => $quote->pickup_location,
                'drop_location' => $quote->drop_location,
                'shipment_date' => $quote->shipment_date,
                'pickup_city' => $request->pickup_city,
                'pickup_state' => $request->pickup_state,
                'pickup_postal_code' => $request->pickup_postal_code,
                'pickup_country' => $request->pickup_country,
                'delivery_city' => $request->delivery_city,
                'delivery_state' => $request->delivery_state,
                'delivery_postal_code' => $request->delivery_postal_code,
                'delivery_country' => $request->delivery_country,
                'quantity' => $request->quantity,
                'unit_type' => $request->unit_type,
                'freight_class_code' => $request->freight_class_code,
                'weight' => $request->weight,
                'length' => $request->length,
                'width' => $request->width,
                'height' => $request->height,
                'additional_services' => $request->additional_services ?? [],
            ];

            $quoteData = $tqlService->formatQuoteData($apiData);

            try {
                $apiResponse = $tqlService->createQuote($quoteData);
            } catch (\Exception $e) {
                $apiResponse = [
                    'error' => 'API request failed',
                    'exception' => $e->getMessage(),
                    'status_code' => 408
                ];
            }

            // 6. Save TQL Response
            TQLResponse::create([
                'quote_id' => $quote->id,
                'response' => $apiResponse,
                'tql_quote_id' => $apiResponse['quoteId'] ?? null,
                'status_code' => $apiResponse['status_code'] ?? (isset($apiResponse['error']) ? 400 : 200),
                'status' => isset($apiResponse['error']) ? 'failed' : 'success',
                'error_message' => $apiResponse['error'] ?? null,
            ]);

            $quote->update(['status' => isset($apiResponse['error']) ? 'failed' : 'completed']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Quote created successfully!',
                'quote_id' => encrypt($quote->id),
                'redirect' => route('quotes.show', encrypt($quote->id))
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Quote creation failed', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create quote. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
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
        // $id = decrypt($id);
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