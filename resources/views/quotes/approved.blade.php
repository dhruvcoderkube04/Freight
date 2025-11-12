@extends('layouts.app')

@section('title', 'Approved Bookings')

@section('content')
<div class="container py-4">
    <div class="ark__h1--wrap mb-4">
        <h1>Approved Bookings</h1>
        <p>These quotes have been approved by admin. You can now proceed to payment.</p>
    </div>

@if($requests->count() > 0)
    <div class="row g-4">
        @foreach($requests as $req)
            @php
                $quote = $req->quote;
                $resp = $quote->tqlResponses->last();
                $carrier = $req->carrier_data;
                $total = $req->total_amount;

                // Find carrier index in TQL response
                $carrierIndex = collect($resp?->response['content']['carrierPrices'] ?? [])
                    ->search(fn($c) => 
                        ($c['carrier'] ?? '') === ($carrier['carrier'] ?? '') &&
                        (float)($c['customerRate'] ?? 0) === (float)($carrier['customerRate'] ?? 0)
                    );

                $status = $req->status;
                $isPending = $status === 'pending';
                $isApproved = $status === 'approved';
                $isRejected = $status === 'rejected';
            @endphp

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0 {{ $isRejected ? 'border-danger' : '' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="badge 
                                {{ $isPending ? 'bg-warning' : 
                                   ($isApproved ? 'bg-success' : 
                                   ($isRejected ? 'bg-danger' : 'bg-secondary')) }}">
                                {{ ucfirst($status) }}
                            </span>
                            <small class="text-muted">
                                {{ $req->created_at->format('M d, Y') }}
                            </small>
                        </div>

                        <h5 class="card-title mb-3">#{{ $quote->id }}</h5>

                        <div class="mb-3">
                            <strong>Route:</strong><br>
                            @if($quote->pickupDetail && $quote->deliveryDetail)
                                <span class="text-success">
                                    {{ $quote->pickupDetail->city }}, {{ $quote->pickupDetail->state }}
                                </span>
                                → 
                                <span class="text-danger">
                                    {{ $quote->deliveryDetail->city }}, {{ $quote->deliveryDetail->state }}
                                </span>
                            @else
                                <span class="text-muted">Location details not available</span>
                            @endif
                        </div>

                        <div class="mb-3">
                            <strong>Carrier:</strong> {{ $carrier['carrier'] ?? 'Unknown' }}
                            @if($carrier['isPreferred'] ?? false)
                                <span class="badge bg-warning ms-1">Preferred</span>
                            @endif
                        </div>

                        @if($isRejected && $req->admin_notes)
                            <div class="alert alert-danger small py-2 mb-3">
                                <strong>Reason:</strong> {{ $req->admin_notes }}
                            </div>
                        @endif

                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="text-success fs-5">${{ number_format($total, 2) }}</strong>
                                    <small class="d-block text-muted">Total Amount</small>
                                </div>

                                @if($isApproved && $carrierIndex !== false)
                                    <form action="{{ route('quotes.payment.form', encrypt($quote->id)) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="selected_carrier_index" value="{{ $carrierIndex }}">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            Pay Now
                                        </button>
                                    </form>
                                @elseif($isPending)
                                    <button class="btn btn-outline-secondary btn-sm" disabled>
                                        Pending Approval
                                    </button>
                                @elseif($isRejected)
                                    <button class="btn btn-outline-danger btn-sm" disabled>
                                        Rejected
                                    </button>
                                @else
                                    <button class="btn btn-outline-secondary btn-sm" disabled>
                                        Not Available
                                    </button>
                                @endif
                            </div>

                            @if($isPending)
                                <div class="text-center mt-3">
                                    <small class="text-muted">
                                        Admin will review and update you soon.
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center py-5">
        <img src="{{ asset('assets/images/empty.svg') }}" alt="No bookings" width="100" class="mb-3 opacity-50">
        <h4>No booking requests yet</h4>
        <p>Create a quote and request approval to see it here.</p>
        <a href="{{ route('quotes.index') }}" class="btn btn-primary">View All Quotes</a>
    </div>
@endif
</div>
@endsection