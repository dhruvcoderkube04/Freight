<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class TQLApiService
{
    private $baseUrl;
    private $subscriptionKey;
    private $clientId;
    private $clientSecret;
    private $username;
    private $password;
    private $scope;

    public function __construct()
    {
        $this->baseUrl = 'https://public.api.tql.com';
        $this->subscriptionKey = '0c6f0fad8c444add98d0693d86a9248c';
        $this->clientId = '39a4ddcb-7234-467e-8091-8a84d8fc27d2';
        $this->clientSecret = '39a4ddcb-7234-467e-8091-8a84d8fc27d2';
        $this->username = 'peagelyu@b612timainc.com';
        $this->password = 'FYZ005132025$pg';
        $this->scope = 'https://tqlidentity.onmicrosoft.com/services_combined/LTLQuotes.Read';
    }

    public function getAccessToken()
    {
        if (Cache::has('tql_access_token')) {
            return Cache::get('tql_access_token');
        }

        $response = Http::asForm()
            ->withHeaders([
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
            ])
            ->post($this->baseUrl . '/identity/token', [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'password',
                'username' => $this->username,
                'password' => $this->password,
                'scope' => $this->scope,
            ]);

        if ($response->successful()) {
            $data = $response->json();
            $token = $data['access_token'];
            Cache::put('tql_access_token', $token, 3500);
            return $token;
        }

        return null;
    }

    public function createQuote($quoteData)
    {
        $token = $this->getAccessToken();

        if (!$token) {
            return ['error' => 'Failed to retrieve access token'];
        }

        // Increase timeout to 60 seconds and add retry mechanism
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
            'Authorization' => 'Bearer ' . $token,
        ])
        ->timeout(60) // Increased from 30 to 60 seconds
        ->retry(3, 100) // Retry 3 times with 100ms delay
        ->post($this->baseUrl . '/ltl/quotes', $quoteData);

        if ($response->successful()) {
            return $response->json();
        }

        return [
            'error' => 'Quote creation failed',
            'status' => $response->status(),
            'body' => $response->json() ?? $response->body(),
            'status_code' => $response->status(),
        ];
    }

    public function formatQuoteData($shipmentData)
    {
        $shipmentDate = $shipmentData['shipment_date'] ?? now()->addDay()->format('Y-m-d');
        $formattedDate = date('Y-m-d\T14:51:35.6819953-05:00', strtotime($shipmentDate));

        return [
            'accessorials' => $this->getAccessorials($shipmentData['additional_services'] ?? []),
            'quoteCommodities' => [
                [
                    'freightClassCode' => $shipmentData['freight_class_code'] ?? '110',
                    'unitTypeCode' => $this->mapUnitType($shipmentData['unit_type'] ?? 'pallet'),
                    'description' => 'General Freight',
                    'quantity' => (int)($shipmentData['quantity'] ?? 2),
                    'weight' => (float)($shipmentData['weight'] ?? 294),
                    'dimensionLength' => (int)($shipmentData['length'] ?? 102),
                    'dimensionWidth' => (int)($shipmentData['width'] ?? 62),
                    'dimensionHeight' => (int)($shipmentData['height'] ?? 41),
                ]
            ],
            'pickLocationType' => $this->mapLocationType($shipmentData['pickup_location'] ?? 'Commercial'),
            'dropLocationType' => $this->mapLocationType($shipmentData['drop_location'] ?? 'Commercial'),
            'shipmentDate' => $formattedDate,
            'origin' => [
                'postalCode' => $shipmentData['pickup_postal_code'] ?? '11741',
                'city' => $shipmentData['pickup_city'] ?? 'Holbrook',
                'state' => $shipmentData['pickup_state'] ?? 'NY',
                'country' => 'USA',
            ],
            'destination' => [
                'postalCode' => $shipmentData['delivery_postal_code'] ?? '45203',
                'city' => $shipmentData['delivery_city'] ?? 'Cincinnati',
                'state' => $shipmentData['delivery_state'] ?? 'OH',
                'country' => 'USA',
            ]
        ];
    }

    private function mapUnitType($type)
    {
        $mapping = [
            'pallet' => 'PLT',
            'carton' => 'CARTON',
            'crate' => 'CRATE',
            'box' => 'BOX',
            'bundle' => 'BUNDLE',
            'drum' => 'DRUM',
            'roll' => 'ROLL',
            'case' => 'CASE',
        ];
        return $mapping[strtolower($type)] ?? 'PLT';
    }

    private function mapLocationType($type)
    {
        $mapping = [
            'commercial' => 'Commercial',
            'residential' => 'Residential',
            'limited_access' => 'Limited Access',
            'trade_show' => 'Trade Show',
        ];
        return $mapping[strtolower($type)] ?? 'Commercial';
    }

    private function getAccessorials($additionalServices)
    {
        $accessorials = [];
        $mapping = [
            'devanning' => 'INPU',
            'labelling' => 'NOTIFY',
            'transshipment' => 'BOND',
        ];

        if (is_array($additionalServices)) {
            foreach ($additionalServices as $service) {
                if (isset($mapping[$service])) {
                    $accessorials[] = $mapping[$service];
                }
            }
        }

        return $accessorials;
    }
}