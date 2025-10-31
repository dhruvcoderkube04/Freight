@extends('layouts.app')

@section('title', 'ArkAnu - Get A Quote')

@push('styles')
<style>
    .quote-card {
        transition: transform 0.2s ease-in-out;
        border: 1px solid #e9ecef;
    }
    .quote-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .carrier-card {
        border-left: 4px solid #007bff;
    }
    .carrier-card.preferred {
        border-left-color: #ffc107;
        background-color: #fffaf0;
    }
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
    }
    .step-progress {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
        position: relative;
    }
    .step-progress::before {
        content: '';
        position: absolute;
        top: 15px;
        left: 0;
        right: 0;
        height: 2px;
        background: #e9ecef;
        z-index: 1;
    }
    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 2;
    }
    .step-circle {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #e9ecef;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }
    .step.active .step-circle {
        background: #007bff;
        color: white;
    }
    .step.completed .step-circle {
        background: #28a745;
        color: white;
    }
    .step-label {
        font-size: 0.875rem;
        color: #6c757d;
        text-align: center;
    }
    .step.active .step-label {
        color: #007bff;
        font-weight: 600;
    }
    .form-step {
        display: none;
    }
    .form-step.active {
        display: block;
    }
    .btn-action {
        min-width: 120px;
    }
</style>
@endpush

@section('content')
<div class="container">
    <!-- Header Section -->
    <div class="ark__h1--wrap">
        <div class="page-title">
            <h1>My Freight Quotes</h1>
            <p>View the status and details for all your requested quotes</p>
        </div>
        <button class="ark__create--quote-btn" id="openDrawerBtn">
            <img src="{{ asset('assets/images/plus.svg') }}" alt="img">
            <p>Create Quote</p>
        </button>
    </div>

    <!-- Quotes List Section -->
    <div class="content-wrapper">
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
                                    <a href="{{ route('quotes.show', $shipment->id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i>View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="empty-state">
                <h3>You haven't created any freight quote yet.</h3>
                <p>Start by entering your shipment details to get an instant quote.</p>
            </div>
        @endif
    </div>
</div>

<!-- Success Popup -->
<div class="ark__popup-overlay" id="successPopup">
    <div class="ark__popup">
        <div class="ark__popup-close" id="closePopup">
            <img src="{{ asset('assets/images/close-gray.svg') }}" alt="img">
        </div>
        <h2>Quote Request Submitted Successfully!</h2>
        <div class="ark__popup-icon-20">
            <div class="ark__popup-icon-30">
                <div class="ark__popup-icon">
                    <img src="{{ asset('assets/images/QuoteRequest.svg') }}" alt="Success" />
                </div>
            </div>
        </div>
        <p>Your Quote details have been submitted for review. Our team will verify the information and update the status shortly.</p>
    </div>
</div>

<!-- Overlay -->
<div class="drawer-overlay" id="drawerOverlay"></div>

<!-- Side Drawer with Quote Form -->
<div class="side-drawer" id="sideDrawer">
    <div class="ark__drawer-header">
        <div class="ark__close--drawer" id="closeDrawerBtn">
            <img src="{{ asset('assets/images/close-gray.svg') }}" alt="img">
        </div>
    </div>
    <div class="drawer-content">
        <div class="form-section">
            <form id="shipmentForm">
                @csrf
                <input type="hidden" name="shipment_id" id="shipment_id">

                <!-- Progress Steps -->
                <div class="step-progress mb-4">
                    <div class="step active" data-step="1">
                        <div class="step-circle">1</div>
                        <div class="step-label">Shipment Info</div>
                    </div>
                    <div class="step" data-step="2">
                        <div class="step-circle">2</div>
                        <div class="step-label">Pickup Details</div>
                    </div>
                    <div class="step" data-step="3">
                        <div class="step-circle">3</div>
                        <div class="step-label">Delivery Details</div>
                    </div>
                    <div class="step" data-step="4">
                        <div class="step-circle">4</div>
                        <div class="step-label">Commodities</div>
                    </div>
                </div>

                <!-- Step 1: Shipment Information -->
                <div class="form-step active" id="step1">
                    <div class="ark__steps-wrap wizard-step active" data-step="1">
                        <div class="ark__steps-content-wrap">
                            <div class="step-number-20">
                                <div class="step-number-30">
                                    <div class="step-number">
                                        <p class="step-count">1</p>
                                        <img src="{{ asset('assets/images/right-arrow.svg') }}" alt="" class="step-number step-right-arrow">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h3 class="ark__step-title">Shipment Information</h3>
                                <p class="ark__step-description">Select the pickup and drop location types and choose the requested shipment date.</p>
                            </div>
                        </div>
                        <div class="ark__fields--wrap">
                            <div class="form-fields active" id="step1-fields">
                                <div class="form-group">
                                    <label>Pickup Location Type<span class="required">*</span></label>
                                    <select class="js-states form-control" id="pickup_location" name="pickup_location">
                                        <option value="">Select pickup location type</option>
                                        @foreach($locationTypes as $locationType)
                                            <option value="{{ $locationType->code }}">{{ $locationType->name }}</option>
                                        @endforeach
                                    </select>
                                    <span tooltip="Please select a pickup location type" class="error-message">
                                        <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label>Drop Location Type<span class="required">*</span></label>
                                    <select class="js-states form-control" id="drop_location" name="drop_location">
                                        <option value="">Select drop location type</option>
                                        @foreach($locationTypes as $locationType)
                                            <option value="{{ $locationType->code }}">{{ $locationType->name }}</option>
                                        @endforeach
                                    </select>
                                    <span tooltip="Please select a Drop location type" class="error-message">
                                        <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label>Shipment Date<span class="required">*</span></label>
                                    <div class="date-picker-wrapper">
                                        <div class="date-input-container" id="dateInputContainer">
                                            <img src="{{ asset('assets/images/calendar-icon.svg') }}" alt="" class="calendar-icon">
                                            <span class="date-display placeholder" id="dateDisplay">MM/DD/YYYY</span>
                                        </div>
                                        <input type="date" class="form-control" id="shipment_date" name="shipment_date" min="{{ date('Y-m-d') }}">
                                        <div class="calendar-popup" id="calendarPopup">
                                            <div class="calendar-header">
                                                <button class="calendar-nav" id="prevMonth">‹</button>
                                                <span class="calendar-title" id="calendarTitle">January 2024</span>
                                                <button class="calendar-nav" id="nextMonth">›</button>
                                            </div>
                                            <div class="calendar-grid" id="calendarGrid"></div>
                                        </div>
                                        <span tooltip="Please select a shipment date" class="error-message">
                                            <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Pickup Details -->
                <div class="form-step" id="step2">
                    <div class="ark__steps-wrap wizard-step" data-step="2">
                        <div class="ark__steps-content-wrap">
                            <div class="step-number-20">
                                <div class="step-number-30">
                                    <div class="step-number">
                                        <p class="step-count">2</p>
                                        <img src="{{ asset('assets/images/right-arrow.svg') }}" alt="" class="step-number step-right-arrow">
                                    </div>
                                </div>
                            </div>
                            <div class="ark__step-content-wrap">
                                <h3 class="ark__step-title">Pickup Details</h3>
                                <p class="ark__step-description">Enter the shipper's location, contact information</p>
                            </div>
                        </div>
                        <div class="ark__fields--wrap">
                            <div class="form-fields active" id="step2-fields">
                                <div class="form-group">
                                    <label for="pickup_city">City <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="pickup_city" name="city" value="Holbrook" required placeholder="Enter delivery city" />
                                    <span tooltip="Please Enter a City" class="error-message">
                                        <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label for="pickup_state">State <span class="required">*</span></label>
                                    <select class="form-control" id="pickup_state" name="state">
                                        <option value="">Select State</option>
                                        <option value="NY" selected>New York</option>
                                        <!-- Add other states as needed -->
                                    </select>
                                    <span tooltip="Please Enter a State" class="error-message">
                                        <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label for="pickup_postal_code">Postal Code <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="pickup_postal_code" name="postal_code" value="11741" required placeholder="Enter pickup postal code" />
                                    <span tooltip="A valid US, Canadian, or Mexican zip code." class="error-message">
                                        <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label for="pickup_country">Country <span class="required">*</span></label>
                                    <select class="form-control" id="pickup_country" name="country">
                                        <option value="">Select Country</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->code }}" {{ $country->code == 'USA' ? 'selected' : '' }}>
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span tooltip="Origin country cannot be MEX when destination is MEX." class="error-message">
                                        <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label for="pickup_address_1">Address Line 1 <span class="required">*</span></label>
                                    <input type="text" maxlength="50" class="form-control" id="pickup_address_1" name="address_1" required placeholder="Enter delivery address" />
                                    <span class="error-message">
                                        <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label for="pickup_address_2">Address Line 2</label>
                                    <input type="text" maxlength="50" class="form-control" id="pickup_address_2" name="address_2" placeholder="Suite, apartment, or additional details" />
                                </div>
                                <div class="form-group">
                                    <label for="pickup_contact_number">Contact Phone <span class="required">*</span></label>
                                    <input type="tel" class="form-control" id="pickup_contact_number" name="contact_number" maxlength="10" pattern="[0-9]{10}" required placeholder="Enter 10-digit phone number" />
                                    <span class="error-message">
                                        <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Delivery Details -->
                <div class="form-step" id="step3">
                    <!-- Similar structure as step 2 but for delivery -->
                    <div class="ark__steps-wrap wizard-step" data-step="3">
                        <div class="ark__steps-content-wrap">
                            <div class="step-number-20">
                                <div class="step-number-30">
                                    <div class="step-number">
                                        <p class="step-count">3</p>
                                        <img src="{{ asset('assets/images/right-arrow.svg') }}" alt="" class="step-number step-right-arrow">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h3 class="ark__step-title">Delivery Details</h3>
                                <p class="ark__step-description">Enter the receiver's location, contact information</p>
                            </div>
                        </div>
                        <div class="ark__fields--wrap">
                            <div class="form-fields active" id="step3-fields">
                                <!-- Delivery form fields similar to pickup -->
                                <div class="form-group">
                                    <label for="delivery_city">City <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="delivery_city" name="city" value="Cincinnati" required placeholder="Enter delivery city" />
                                </div>
                                <div class="form-group">
                                    <label for="delivery_state">State <span class="required">*</span></label>
                                    <select class="form-control" id="delivery_state" name="state">
                                        <option value="">Select State</option>
                                        <option value="OH" selected>Ohio</option>
                                    </select>
                                </div>
                                <!-- Add other delivery fields similarly -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Add Quote Commodities -->
                <div class="form-step" id="step4">
                    <div class="ark__steps-wrap wizard-step" data-step="4">
                        <div class="ark__steps-content-wrap">
                            <div class="step-number-20">
                                <div class="step-number-30">
                                    <div class="step-number">
                                        <p class="step-count">4</p>
                                        <img src="{{ asset('assets/images/right-arrow.svg') }}" alt="" class="step-number step-right-arrow">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h3 class="ark__step-title">Add Quote Commodities</h3>
                                <p class="ark__step-description">Provide details for each commodity including description, quantity, unit type, and dimensions.</p>
                            </div>
                        </div>
                        <div class="ark__fields--wrap">
                            <div class="form-fields active" id="step4-fields">
                                <div class="form-group">
                                    <label for="quantity">Quantity <span class="required">*</span></label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" value="2" required placeholder="Enter quantity" />
                                </div>
                                <div class="form-group">
                                    <label for="unit_type">Unit Type<span class="required">*</span></label>
                                    <select class="form-control" id="unit_type" name="unit_type">
                                        <option value="">Select Unit Type</option>
                                        @foreach($unitTypes as $unitType)
                                            <option value="{{ $unitType->code }}" {{ $unitType->code == 'pallet' ? 'selected' : '' }}>
                                                {{ $unitType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="freight_class_code">Freight Class Code<span class="required">*</span></label>
                                    <select class="form-control" id="freight_class_code" name="freight_class_code">
                                        <option value="">Select Freight Class</option>
                                        @foreach($freightClasses as $freightClass)
                                            <option value="{{ $freightClass->code }}" {{ $freightClass->code == '110' ? 'selected' : '' }}>
                                                {{ $freightClass->code }} - {{ $freightClass->description }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="weight">Weight (lbs) <span class="required">*</span></label>
                                    <input type="number" class="form-control" id="weight" name="weight" value="294" step="0.1" required placeholder="Enter total weight in lbs" />
                                </div>
                                <div class="form-group">
                                    <label for="length">Length (inches) <span class="required">*</span></label>
                                    <input type="number" class="form-control" id="length" name="length" value="102" required placeholder="Enter length (1–636)" />
                                </div>
                                <div class="form-group">
                                    <label for="width">Width (inches) <span class="required">*</span></label>
                                    <input type="number" class="form-control" id="width" name="width" value="62" required placeholder="Enter width (1–102)" />
                                </div>
                                <div class="form-group">
                                    <label for="height">Height (inches) <span class="required">*</span></label>
                                    <input type="number" class="form-control" id="height" name="height" value="41" required placeholder="Enter height (1–102)" />
                                </div>
                                <div class="form-group">
                                    <label>Additional Services</label>
                                    <div class="ark__radio-group">
                                        <label class="ark__custom-radio">
                                            <input type="checkbox" name="additional_services[]" value="devanning" id="devanning">
                                            <span class="ark__radio-label">Devanning</span>
                                        </label>
                                        <label class="ark__custom-radio">
                                            <input type="checkbox" name="additional_services[]" value="transshipment" id="transshipment">
                                            <span class="ark__radio-label">Transshipment</span>
                                        </label>
                                        <label class="ark__custom-radio">
                                            <input type="checkbox" name="additional_services[]" value="labeling" id="labeling">
                                            <span class="ark__radio-label">Labeling</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="button" class="btn btn-back" id="backBtn" disabled>
                        <svg width="23" height="20" viewBox="0 0 23 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.0159 0.625L0.625 9.625M0.625 9.625L10.0159 18.625M0.625 9.625H21.625" stroke="black" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <p>Back</p>
                    </button>
                    <button type="button" class="btn btn-next" id="nextBtn">
                        <p>Next</p>
                    </button>
                    <button type="button" class="btn btn-submit d-none" id="submitBtn">
                        <p>Submit Quote</p>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentStep = 1;
    const totalSteps = 4;

    // Initialize tooltips
    $(document).ready(function () {
        tippy('[tooltip]', {
            arrow: true,
            placement: 'top',
            delay: 5,
            distance: 15,
            maxWidth: 300,
            followCursor: true,
            allowHTML: true,
            theme: 'custom',
            ignoreAttributes: true,
            content(reference) {
                const tooltip = reference.getAttribute('tooltip');
                reference.removeAttribute('tooltip');
                return tooltip;
            },
        });

        // Set default shipment date to tomorrow
        document.getElementById('shipment_date').valueAsDate = new Date(Date.now() + 86400000);
    });

    function updateProgress() {
        // Update progress steps
        document.querySelectorAll('.step').forEach((step, index) => {
            const stepNumber = index + 1;
            if (stepNumber < currentStep) {
                step.classList.add('completed');
                step.classList.remove('active');
            } else if (stepNumber === currentStep) {
                step.classList.add('active');
                step.classList.remove('completed');
            } else {
                step.classList.remove('active', 'completed');
            }
        });

        // Update form steps
        document.querySelectorAll('.form-step').forEach((step, index) => {
            step.classList.toggle('active', (index + 1) === currentStep);
        });

        // Update buttons
        const backBtn = document.getElementById('backBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');

        backBtn.disabled = currentStep === 1;
        
        if (currentStep === totalSteps) {
            nextBtn.classList.add('d-none');
            submitBtn.classList.remove('d-none');
        } else {
            nextBtn.classList.remove('d-none');
            submitBtn.classList.add('d-none');
        }
    }

    function nextStep() {
        if (currentStep < totalSteps) {
            currentStep++;
            updateProgress();
        }
    }

    function previousStep() {
        if (currentStep > 1) {
            currentStep--;
            updateProgress();
        }
    }

    function saveStep(step) {
        const form = document.getElementById('shipmentForm');
        const formData = new FormData(form);
        
        let url = '';
        switch(step) {
            case 1:
                url = '{{ route("shipments.store.step1") }}';
                break;
            case 2:
                url = '{{ route("shipments.store.step2") }}';
                formData.append('shipment_id', document.getElementById('shipment_id').value);
                break;
            case 3:
                url = '{{ route("shipments.store.step3") }}';
                formData.append('shipment_id', document.getElementById('shipment_id').value);
                break;
            case 4:
                url = '{{ route("shipments.store.step4") }}';
                formData.append('shipment_id', document.getElementById('shipment_id').value);
                break;
        }

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (step === 1 && data.shipment_id) {
                    document.getElementById('shipment_id').value = data.shipment_id;
                }
                
                if (step === 4) {
                    // Show success popup and reload page
                    document.getElementById('successPopup').style.display = 'block';
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);
                } else {
                    nextStep();
                }
            } else {
                alert(data.message || 'An error occurred. Please check your input.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }

    // Event Listeners
    document.getElementById('nextBtn').addEventListener('click', function() {
        saveStep(currentStep);
    });

    document.getElementById('backBtn').addEventListener('click', previousStep);

    document.getElementById('submitBtn').addEventListener('click', function() {
        saveStep(currentStep);
    });

    // Drawer functionality
    document.getElementById('openDrawerBtn').addEventListener('click', function() {
        document.getElementById('sideDrawer').classList.add('active');
        document.getElementById('drawerOverlay').classList.add('active');
    });

    document.getElementById('closeDrawerBtn').addEventListener('click', function() {
        document.getElementById('sideDrawer').classList.remove('active');
        document.getElementById('drawerOverlay').classList.remove('active');
    });

    document.getElementById('drawerOverlay').addEventListener('click', function() {
        document.getElementById('sideDrawer').classList.remove('active');
        this.classList.remove('active');
    });

    // Initialize
    updateProgress();
</script>
@endpush