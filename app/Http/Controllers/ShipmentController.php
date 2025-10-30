<?php

namespace App\Http\Controllers;

use App\Models\CountryCode;
use App\Models\FreightClassCode;
use App\Models\LocationType;
use App\Models\Shipment;
use App\Models\PickupDetail;
use App\Models\DeliveryDetail;
use App\Models\Commodity;
use App\Models\TQLResponse;
use App\Models\UnitType;
use App\Services\TQLApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ShipmentController extends Controller
{
    public function create()
    {
        // Get all dynamic options from database
        $locationTypes = LocationType::where('is_active', true)->orderBy('sort_order')->get();
        $countries = CountryCode::where('is_active', true)->orderBy('sort_order')->get();
        $unitTypes = UnitType::where('is_active', true)->orderBy('sort_order')->get();
        $freightClasses = FreightClassCode::where('is_active', true)->orderBy('sort_order')->get();

        return view('shipments.create', compact(
            'locationTypes',
            'countries',
            'unitTypes',
            'freightClasses'
        ));
    }


    public function storeStep1(Request $request)
    {
        // $validated = $request->validate([
        //     'pickup_location' => 'required|string|in:commercial,residential,limited_access,trade_show',
        //     'drop_location' => 'required|string|in:commercial,residential,limited_access,trade_show',
        //     'shipment_date' => 'required|date|after_or_equal:today',
        // ]);

        $shipment = Shipment::updateOrCreate(
            ['id' => $request->shipment_id],
            [
                'user_id' => Auth::id(),
                'pickup_location' => $request->pickup_location,
                'drop_location' => $request->drop_location,
                'shipment_date' => $request->shipment_date,
            ]
        );

        return response()->json([
            'success' => true,
            'shipment_id' => $shipment->id,
            'next_step' => 2
        ]);
    }

    public function storeStep2(Request $request)
    {
        // $validated = $request->validate([
        //     'shipment_id' => 'required|exists:shipments,id',
        //     'city' => 'required|string|max:255',
        //     'state' => 'required|string|size:2',
        //     'postal_code' => 'required|string|regex:/^\d{5}(-\d{4})?$/',
        //     'country' => 'required|string|in:USA,CAN,MEX',
        //     'address_1' => 'nullable|string|max:50',
        //     'address_2' => 'nullable|string|max:50',
        //     'contact_number' => 'nullable|string|regex:/^\+?1?\d{10}$/',
        // ]);

        $shipment = Shipment::where('id', $request->shipment_id)
                          ->where('user_id', Auth::id())
                          ->firstOrFail();

        PickupDetail::updateOrCreate(
            ['shipment_id' => $request->shipment_id],
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
        //     'shipment_id' => 'required|exists:shipments,id',
        //     'city' => 'required|string|max:255',
        //     'state' => 'required|string|size:2',
        //     'postal_code' => 'required|string|regex:/^\d{5}(-\d{4})?$/',
        //     'country' => 'required|string|in:USA,CAN,MEX',
        //     'address_1' => 'nullable|string|max:50',
        //     'address_2' => 'nullable|string|max:50',
        //     'contact_number' => 'nullable|string|regex:/^\+?1?\d{10}$/',
        // ]);

        $shipment = Shipment::where('id', $request->shipment_id)
                          ->where('user_id', Auth::id())
                          ->firstOrFail();

        DeliveryDetail::updateOrCreate(
            ['shipment_id' => $request->shipment_id],
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
        //     'shipment_id' => 'required|exists:shipments,id',
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

        $shipment = Shipment::where('id', $request->shipment_id)
                          ->where('user_id', Auth::id())
                          ->firstOrFail();

        Commodity::create([
            'shipment_id' => $request->shipment_id,
            'quantity' => $request->quantity ?? 1,
            'unit_type' => $request->unit_type ?? 'pallet',
            'freight_class_code' => $request->freight_class_code ?? '110',
            'weight' => $request->weight ?? 100, 
            'length' => $request->length ?? 48,
            'width' => $request->width ?? 48,
            'height' => $request->height ?? 48,
            'additional_services' => $request->additional_services ? json_encode($request->additional_services) : null,
        ]);

        $shipment = Shipment::with(['pickupDetail', 'deliveryDetail', 'commodities'])
                          ->where('user_id', Auth::id())
                          ->find($request->shipment_id);
        
        $tqlService = new TQLApiService();
        $commodity = $shipment->commodities->first();
        
        $apiData = [
            'pickup_location' => $shipment->pickup_location,
            'drop_location' => $shipment->drop_location,
            'shipment_date' => $shipment->shipment_date,
            'pickup_city' => $shipment->pickupDetail->city,
            'pickup_state' => $shipment->pickupDetail->state,
            'pickup_postal_code' => $shipment->pickupDetail->postal_code,
            'pickup_country' => $shipment->pickupDetail->country ?? 'USA',
            'delivery_city' => $shipment->deliveryDetail->city,
            'delivery_state' => $shipment->deliveryDetail->state,
            'delivery_postal_code' => $shipment->deliveryDetail->postal_code,
            'delivery_country' => $shipment->deliveryDetail->country ?? 'USA',
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
            'shipment_id' => $shipment->id,
            'response' => $apiResponse,
            'tql_quote_id' => $apiResponse['quoteId'] ?? $apiResponse['id'] ?? null,
            'status_code' => $apiResponse['status_code'] ?? (isset($apiResponse['error']) ? 400 : 200),
            'status' => isset($apiResponse['error']) ? 'failed' : 'success',
            'error_message' => $apiResponse['error'] ?? null,
        ]);

        if (isset($apiResponse['error']) || is_null($apiResponse)) {
            $shipment->update(['status' => 'failed']);
            return response()->json([
                'success' => false,
                'message' => 'Shipment saved but API call failed',
                'api_response' => $apiResponse,
                'tql_response_id' => $tqlResponse->id,
                'error_details' => $apiResponse['error'] ?? 'API response was null'
            ]);
        }

        $shipment->update(['status' => 'completed']);

        return response()->json([
            'success' => true,
            'message' => 'Quote Generated Successfully!',
            'api_response' => $apiResponse,
            'tql_response_id' => $tqlResponse->id,
            'quote_id' => $tqlResponse->tql_quote_id
        ]);
    }

    // Optional: Method to get TQL response for a shipment
    public function getTqlResponse($shipmentId)
    {
        $shipment = Shipment::where('id', $shipmentId)
                          ->where('user_id', Auth::id())
                          ->firstOrFail();

        $tqlResponse = $shipment->latestTqlResponse;

        return response()->json([
            'success' => true,
            'tql_response' => $tqlResponse
        ]);
    }
}