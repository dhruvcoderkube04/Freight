@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="display-5 text-primary">My Quotes</h1>
                <a href="{{ route('shipments.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create New Quote
                </a>
            </div>

            @if($shipments->count() > 0)
                <div class="row">
                    @foreach($shipments as $shipment)
                        @php
                            $latestResponse = $shipment->tqlResponses->last();
                            $carrierCount = $latestResponse && isset($latestResponse->response['content']['carrierPrices']) 
                                ? count($latestResponse->response['content']['carrierPrices']) 
                                : 0;
                            $lowestPrice = $latestResponse && isset($latestResponse->response['content']['carrierPrices'])
                                ? min(array_column($latestResponse->response['content']['carrierPrices'], 'customerRate'))
                                : 0;
                        @endphp
                        
                        <div class="col-lg-6 mb-4">
                            <div class="card quote-card h-100">
                                <div class="card-header bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Quote #{{ $shipment->id }}</h5>
                                        <span class="badge bg-{{ $shipment->status === 'completed' ? 'success' : 'warning' }}">
                                            {{ ucfirst($shipment->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Location Types -->
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <small class="text-muted">Pickup Type</small>
                                            <p class="mb-1 fw-bold text-primary">{{ $shipment->pickup_location }}</p>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Delivery Type</small>
                                            <p class="mb-1 fw-bold text-success">{{ $shipment->drop_location }}</p>
                                        </div>
                                    </div>

                                    <!-- Address Information -->
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <small class="text-muted">From</small>
                                            <p class="mb-1">
                                                @if($shipment->pickupDetail && $shipment->pickupDetail->city)
                                                    <strong>{{ $shipment->pickupDetail->city }}, {{ $shipment->pickupDetail->state }}</strong>
                                                    @if($shipment->pickupDetail->postal_code)
                                                        <br><small class="text-muted">{{ $shipment->pickupDetail->postal_code }}</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted fst-italic">Address not provided</span>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">To</small>
                                            <p class="mb-1">
                                                @if($shipment->deliveryDetail && $shipment->deliveryDetail->city)
                                                    <strong>{{ $shipment->deliveryDetail->city }}, {{ $shipment->deliveryDetail->state }}</strong>
                                                    @if($shipment->deliveryDetail->postal_code)
                                                        <br><small class="text-muted">{{ $shipment->deliveryDetail->postal_code }}</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted fst-italic">Address not provided</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Dates -->
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <small class="text-muted">Shipment Date</small>
                                            <p class="mb-1">{{ \Carbon\Carbon::parse($shipment->shipment_date)->format('M d, Y') }}</p>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Created</small>
                                            <p class="mb-1">{{ $shipment->created_at->format('M d, Y') }}</p>
                                        </div>
                                    </div>

                                    <!-- Quote Summary -->
                                    @if($latestResponse && $latestResponse->status === 'success')
                                        <div class="quote-summary bg-light p-3 rounded">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="fw-bold">Best Price:</span>
                                                <span class="h5 text-success mb-0">${{ number_format($lowestPrice, 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <small class="text-muted">Carrier Options:</small>
                                                <small class="fw-bold">{{ $carrierCount }} carriers</small>
                                            </div>
                                        </div>
                                    @elseif($latestResponse && $latestResponse->status === 'failed')
                                        <div class="alert alert-danger mb-0">
                                            <small><i class="fas fa-exclamation-triangle me-1"></i>Quote generation failed</small>
                                        </div>
                                    @else
                                        <div class="alert alert-warning mb-0">
                                            <small><i class="fas fa-clock me-1"></i>Quote in progress</small>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="d-flex justify-content-between">
                                        @if($latestResponse && $latestResponse->status === 'success')
                                            <a href="{{ route('quotes.show', $shipment->id) }}" class="btn btn-success btn-sm">
                                                <i class="fas fa-credit-card me-1"></i>Book Now
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-shipping-fast fa-4x text-muted mb-4"></i>
                    <h3 class="text-muted">No Quotes Yet</h3>
                    <p class="text-muted">Create your first shipping quote to get started.</p>
                    <a href="{{ route('shipments.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>Create First Quote
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection