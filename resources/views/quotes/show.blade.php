@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="display-5 text-primary">Quote Details</h1>
                    <p class="text-muted">Quote #{{ $shipment->id }}</p>
                </div>
                <div>
                    <a href="{{ route('quotes.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-2"></i>Back to Quotes
                    </a>
                    @if($latestResponse && $latestResponse->status === 'success')
                        <button class="btn btn-success" onclick="showCarrierSelection()">
                            <i class="fas fa-credit-card me-2"></i>Proceed to Payment
                        </button>
                    @endif
                </div>
            </div>

            <!-- Location Types Summary -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-shipping-fast text-primary me-2"></i>Shipment Types</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary text-white rounded-circle p-3 me-3">
                                    <i class="fas fa-map-marker-alt fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Pickup Location</h6>
                                    <p class="mb-0 fw-bold text-primary">
                                        {{ $locationTypes->firstWhere('code', $shipment->pickup_location)->name ?? ucfirst($shipment->pickup_location) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success text-white rounded-circle p-3 me-3">
                                    <i class="fas fa-truck fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Delivery Location</h6>
                                    <p class="mb-0 fw-bold text-success">
                                        {{ $locationTypes->firstWhere('code', $shipment->drop_location)->name ?? ucfirst($shipment->drop_location) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipment Information -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-info-circle text-info me-2"></i>Address Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2">Pickup Details</h6>
                            @if($shipment->pickupDetail)
                                <p class="mb-1"><strong>Address:</strong> 
                                    {{ $shipment->pickupDetail->address_1 ?: 'Not provided' }}
                                    @if($shipment->pickupDetail->address_2)
                                        <br>{{ $shipment->pickupDetail->address_2 }}
                                    @endif
                                </p>
                                <p class="mb-1"><strong>City/State:</strong> {{ $shipment->pickupDetail->city }}, {{ $shipment->pickupDetail->state }}</p>
                                <p class="mb-1"><strong>Postal Code:</strong> {{ $shipment->pickupDetail->postal_code ?: 'Not provided' }}</p>
                                <p class="mb-1"><strong>Country:</strong> {{ $shipment->pickupDetail->country ?: 'Not provided' }}</p>
                                <p class="mb-0"><strong>Contact:</strong> {{ $shipment->pickupDetail->contact_number ?: 'Not provided' }}</p>
                            @else
                                <p class="text-muted fst-italic">Pickup details not available</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2">Delivery Details</h6>
                            @if($shipment->deliveryDetail)
                                <p class="mb-1"><strong>Address:</strong> 
                                    {{ $shipment->deliveryDetail->address_1 ?: 'Not provided' }}
                                    @if($shipment->deliveryDetail->address_2)
                                        <br>{{ $shipment->deliveryDetail->address_2 }}
                                    @endif
                                </p>
                                <p class="mb-1"><strong>City/State:</strong> {{ $shipment->deliveryDetail->city }}, {{ $shipment->deliveryDetail->state }}</p>
                                <p class="mb-1"><strong>Postal Code:</strong> {{ $shipment->deliveryDetail->postal_code ?: 'Not provided' }}</p>
                                <p class="mb-1"><strong>Country:</strong> {{ $shipment->deliveryDetail->country ?: 'Not provided' }}</p>
                                <p class="mb-0"><strong>Contact:</strong> {{ $shipment->deliveryDetail->contact_number ?: 'Not provided' }}</p>
                            @else
                                <p class="text-muted fst-italic">Delivery details not available</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Commodities Section -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-boxes text-warning me-2"></i>Commodities</h5>
                </div>
                <div class="card-body">
                    @forelse($shipment->commodities as $commodity)
                        <div class="border rounded p-3 mb-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Quantity:</strong> {{ $commodity->quantity }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Unit Type:</strong> {{ ucfirst($commodity->unit_type) }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Weight:</strong> {{ $commodity->weight }} lbs
                                </div>
                                <div class="col-md-3">
                                    <strong>Freight Class:</strong> {{ $commodity->freight_class_code }}
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-4">
                                    <strong>Dimensions:</strong> {{ $commodity->length }}" × {{ $commodity->width }}" × {{ $commodity->height }}"
                                </div>
                                <div class="col-md-8">
                                    <strong>Additional Services:</strong> 
                                    {{ $commodity->additional_services ? implode(', ', json_decode($commodity->additional_services, true)) : 'None' }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted fst-italic">No commodities added</p>
                    @endforelse
                </div>
            </div>

            <!-- TQL Response Section -->
            @if($latestResponse)
                <div class="card">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-receipt text-success me-2"></i>Carrier Quotes</h5>
                        <span class="badge bg-{{ $latestResponse->status === 'success' ? 'success' : 'danger' }}">
                            {{ ucfirst($latestResponse->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        @if($latestResponse->status === 'success' && isset($latestResponse->response['content']['carrierPrices']))
                            <div class="row">
                                @foreach($latestResponse->response['content']['carrierPrices'] as $index => $carrier)
                                    <div class="col-lg-6 mb-3">
                                        <div class="carrier-card p-3 border rounded {{ $carrier['isPreferred'] ? 'border-warning bg-light-warning' : '' }}">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">{{ $carrier['carrier'] }}</h6>
                                                    <small class="text-muted">SCAC: {{ $carrier['scac'] }}</small>
                                                    {{ $carrier['isPreferred'] ? '<span class="badge bg-warning ms-2">Preferred</span>' : '' }}
                                                    {{ $carrier['isCarrierOfTheYear'] ? '<span class="badge bg-info ms-1">Carrier of the Year</span>' : '' }}
                                                </div>
                                                <div class="text-end">
                                                    <strong class="text-success h5">${{ number_format($carrier['customerRate'], 2) }}</strong>
                                                    <div><small class="text-muted">{{ $carrier['transitDays'] ?? 'N/A' }} transit days</small></div>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <small><strong>Service:</strong> {{ $carrier['serviceLevelDescription'] ?? $carrier['serviceLevel'] ?? 'Standard' }}</small>
                                            </div>
                                            @if(isset($carrier['maxLiabilityNew']) || isset($carrier['maxLiabilityUsed']))
                                            <div class="mt-1">
                                                <small><strong>Liability:</strong> 
                                                    ${{ number_format($carrier['maxLiabilityNew'] ?? 0, 2) }} (New) / 
                                                    ${{ number_format($carrier['maxLiabilityUsed'] ?? 0, 2) }} (Used)
                                                </small>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-danger">
                                <h5><i class="fas fa-exclamation-triangle me-2"></i>Quote Generation Failed</h5>
                                <p class="mb-0">{{ $latestResponse->error_message ?? 'Unknown error occurred' }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Carrier Selection Modal -->
<div class="modal fade" id="carrierSelectionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Carrier for Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    @if($latestResponse && $latestResponse->status === 'success' && isset($latestResponse->response['content']['carrierPrices']))
                        @foreach($latestResponse->response['content']['carrierPrices'] as $index => $carrier)
                            <div class="col-12 mb-3">
                                <div class="card carrier-selection-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $carrier['carrier'] }}</h6>
                                                <small class="text-muted">SCAC: {{ $carrier['scac'] }}</small>
                                                @if($carrier['isPreferred'])
                                                    <span class="badge bg-warning ms-2">Preferred</span>
                                                @endif
                                                @if($carrier['isCarrierOfTheYear'])
                                                    <span class="badge bg-info ms-1">Carrier of the Year</span>
                                                @endif
                                                <div class="mt-2">
                                                    <small><strong>Service:</strong> {{ $carrier['serviceLevelDescription'] ?? $carrier['serviceLevel'] ?? 'Standard' }}</small>
                                                </div>
                                                <div class="mt-1">
                                                    <small><strong>Transit Days:</strong> {{ $carrier['transitDays'] ?? 'N/A' }}</small>
                                                </div>
                                                @if(isset($carrier['maxLiabilityNew']) || isset($carrier['maxLiabilityUsed']))
                                                <div class="mt-1">
                                                    <small><strong>Liability:</strong> 
                                                        ${{ number_format($carrier['maxLiabilityNew'] ?? 0, 2) }} (New) / 
                                                        ${{ number_format($carrier['maxLiabilityUsed'] ?? 0, 2) }} (Used)
                                                    </small>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="text-end">
                                                <strong class="text-success h4">${{ number_format($carrier['customerRate'], 2) }}</strong>
                                                <div class="mt-2">
                                                    <button class="btn btn-primary btn-sm" onclick="selectCarrier({{ $index }})">
                                                        Select & Continue
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-12">
                            <div class="alert alert-warning">
                                No carrier quotes available for selection.
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function showCarrierSelection() {
    var modal = new bootstrap.Modal(document.getElementById('carrierSelectionModal'));
    modal.show();
}

function selectCarrier(carrierIndex) {
    window.location.href = `/quotes/{{ $shipment->id }}/payment?carrier_index=${carrierIndex}`;
}

function initiatePayment(shipmentId) {
    showCarrierSelection();
}
</script>
@endsection