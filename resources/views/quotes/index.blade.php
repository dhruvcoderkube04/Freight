@extends('layouts.app')

@section('title', 'ArkAnu - Get A Quote')

@section('content')
<div class="container">
    <div class="ark__h1--wrap">
        <div class="page-title">
            <h1>My Freight Quotes</h1>
            <p>View the status and details for all your requested</p>
        </div>
        <button class="ark__create--quote-btn" id="openDrawerBtn">
            <img src="{{ asset('assets/images/plus.svg') }}" alt="img">
            <p>Create Quote</p>
        </button>
    </div>
    
@if ($quotes->count() > 0)
    <div class="row g-4">
        @foreach ($quotes as $quote)
            @php
                $latestResponse = $quote->tqlResponses->last();
                $carrierCount   = $latestResponse && isset($latestResponse->response['content']['carrierPrices'])
                                 ? count($latestResponse->response['content']['carrierPrices'])
                                 : 0;
                $lowestPrice    = $latestResponse && isset($latestResponse->response['content']['carrierPrices'])
                                 ? min(array_column($latestResponse->response['content']['carrierPrices'], 'customerRate'))
                                 : 0;
            @endphp

            <div class="col-lg-6 col-xxl-4">
                <!-- ==== CARD ==== -->
                <div class="card h-100 shadow-sm border-0 quote-card">
                    <!-- Header – Quote # + Status -->
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0 fw-semibold text-primary">
                            Quote #{{ $quote->id }}
                        </h5>

                        <span class="badge rounded-pill px-3 py-2
                            {{ $quote->status === 'completed' ? 'bg-success' : 'bg-warning' }}">
                            {{ ucfirst($quote->status) }}
                        </span>
                    </div>

                    <!-- Body -->
                    <div class="card-body pt-2">

                        <!-- ==== LOCATION TYPES ==== -->
                        <div class="row mb-3 text-center text-md-start">
                            <div class="col-6">
                                <small class="text-muted d-block">Pickup</small>
                                <p class="mb-0 fw-bold text-primary">
                                    {{ $locationTypes->firstWhere('code', $quote->pickup_location)->name
                                        ?? ucfirst($quote->pickup_location) }}
                                </p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Delivery</small>
                                <p class="mb-0 fw-bold text-success">
                                    {{ $locationTypes->firstWhere('code', $quote->drop_location)->name
                                        ?? ucfirst($quote->drop_location) }}
                                </p>
                            </div>
                        </div>

                        <!-- ==== ADDRESS SUMMARY ==== -->
                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted d-block">From</small>
                                @if ($quote->pickupDetail && $quote->pickupDetail->city)
                                    <p class="mb-0 fw-semibold">
                                        {{ $quote->pickupDetail->city }}, {{ $quote->pickupDetail->state }}
                                    </p>
                                    @if ($quote->pickupDetail->postal_code)
                                        <small class="text-muted">{{ $quote->pickupDetail->postal_code }}</small>
                                    @endif
                                @else
                                    <span class="text-muted fst-italic">—</span>
                                @endif
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">To</small>
                                @if ($quote->deliveryDetail && $quote->deliveryDetail->city)
                                    <p class="mb-0 fw-semibold">
                                        {{ $quote->deliveryDetail->city }}, {{ $quote->deliveryDetail->state }}
                                    </p>
                                    @if ($quote->deliveryDetail->postal_code)
                                        <small class="text-muted">{{ $quote->deliveryDetail->postal_code }}</small>
                                    @endif
                                @else
                                    <span class="text-muted fst-italic">—</span>
                                @endif
                            </div>
                        </div>

                        <!-- ==== DATES ==== -->
                        <div class="row mb-3 text-muted small">
                            <div class="col-6">
                                <i class="fas fa-calendar-alt me-1"></i>
                                {{ \Carbon\Carbon::parse($quote->shipment_date)->format('M d, Y') }}
                            </div>
                            <div class="col-6">
                                <i class="fas fa-clock me-1"></i>
                                {{ $quote->created_at->format('M d, Y') }}
                            </div>
                        </div>

                        <!-- ==== QUOTE RESULT ==== -->
                        @if ($latestResponse && $latestResponse->status === 'success')
                            <div class="bg-light rounded-3 p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-semibold text-dark">Best Price</span>
                                    <span class="h4 text-success mb-0 fw-bold">
                                        ${{ number_format($lowestPrice, 2) }}
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between small text-muted">
                                    <span>Carrier Options</span>
                                    <span class="fw-semibold">{{ $carrierCount }} carrier{{ $carrierCount != 1 ? 's' : '' }}</span>
                                </div>
                            </div>
                        @elseif ($latestResponse && $latestResponse->status === 'failed')
                            <div class="alert alert-danger p-2 mb-0 small">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Quote generation failed
                            </div>
                        @else
                            <div class="alert alert-warning p-2 mb-0 small">
                                <i class="fas fa-clock me-1"></i>
                                Quote in progress…
                            </div>
                        @endif
                    </div>

                    <!-- Footer – CTA -->
                    <div class="card-footer bg-transparent border-0 pt-0">
                        @if ($latestResponse && $latestResponse->status === 'success')
                        @php
                        $quote_id = encrypt($quote->id);
                        @endphp
                            <a href="{{ route('quotes.show',$quote_id) }}"
                               class="btn btn-success w-100 d-flex align-items-center justify-content-center">
                                <i class="fas fa-credit-card me-2"></i> Book Now
                            </a>
                        @endif
                    </div>
                </div>
                <!-- ==== END CARD ==== -->
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

<!-- Success Popup -->
<div class="ark__popup-overlay" id="successPopup" style="display: none;">
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
<div class="drawer-overlay" id="drawerOverlay" style="display: none;"></div>

<!-- Side Drawer with All Steps -->
<div class="side-drawer" id="sideDrawer" style="display: none;">
    <div class="ark__drawer-header">
        <div class="ark__close--drawer" id="closeDrawerBtn">
            <img src="{{ asset('assets/images/close-gray.svg') }}" alt="img">
        </div>
    </div>
    <div class="drawer-content">
        <div class="form-section">
            <form id="quoteForm">
                @csrf
                <input type="hidden" name="quote_id" id="quote_id">

                <!-- Step 1: quote Information -->
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
                                <select id="pickup_location" name="pickup_location" class="js-states form-control">
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
                                <select id="drop_location" name="drop_location" class="js-states form-control">
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
                                        <span class="date-display" id="dateDisplay">MM/DD/YYYY</span>
                                    </div>
                                    <input type="date" id="shipment_date" name="shipment_date" required min="{{ date('Y-m-d') }}">
                                    <div class="calendar-popup" id="calendarPopup" style="display: none;">
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

                <!-- Step 2: Pickup Details -->
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
                        <div class="form-fields" id="step2-fields">
                            <div class="form-group">
                                <label for="pickup_city">City <span class="required">*</span></label>
                                <input type="text" id="pickup_city" name="city" required placeholder="Enter pickup city" value="Holbrook" />
                                <span tooltip="Please enter a city" class="error-message">
                                    <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                </span>
                            </div>
                            <div class="form-group">
                                <label for="pickup_state">State <span class="required">*</span></label>
                                <select id="pickup_state" name="state" class="js-states form-control" required>
                                    <option value="">Select State</option>
                                    <option value="NY" selected>New York</option>
                                    <!-- Other states -->
                                    <option value="AL">Alabama</option>
                                    <option value="AK">Alaska</option>
                                    <option value="AZ">Arizona</option>
                                    <option value="AR">Arkansas</option>
                                    <option value="CA">California</option>
                                    <option value="CO">Colorado</option>
                                    <option value="CT">Connecticut</option>
                                    <option value="DE">Delaware</option>
                                    <option value="FL">Florida</option>
                                    <option value="GA">Georgia</option>
                                    <option value="HI">Hawaii</option>
                                    <option value="ID">Idaho</option>
                                    <option value="IL">Illinois</option>
                                    <option value="IN">Indiana</option>
                                    <option value="IA">Iowa</option>
                                    <option value="KS">Kansas</option>
                                    <option value="KY">Kentucky</option>
                                    <option value="LA">Louisiana</option>
                                    <option value="ME">Maine</option>
                                    <option value="MD">Maryland</option>
                                    <option value="MA">Massachusetts</option>
                                    <option value="MI">Michigan</option>
                                    <option value="MN">Minnesota</option>
                                    <option value="MS">Mississippi</option>
                                    <option value="MO">Missouri</option>
                                    <option value="MT">Montana</option>
                                    <option value="NE">Nebraska</option>
                                    <option value="NV">Nevada</option>
                                    <option value="NH">New Hampshire</option>
                                    <option value="NJ">New Jersey</option>
                                    <option value="NM">New Mexico</option>
                                    <option value="NC">North Carolina</option>
                                    <option value="ND">North Dakota</option>
                                    <option value="OH">Ohio</option>
                                    <option value="OK">Oklahoma</option>
                                    <option value="OR">Oregon</option>
                                    <option value="PA">Pennsylvania</option>
                                    <option value="RI">Rhode Island</option>
                                    <option value="SC">South Carolina</option>
                                    <option value="SD">South Dakota</option>
                                    <option value="TN">Tennessee</option>
                                    <option value="TX">Texas</option>
                                    <option value="UT">Utah</option>
                                    <option value="VT">Vermont</option>
                                    <option value="VA">Virginia</option>
                                    <option value="WA">Washington</option>
                                    <option value="WV">West Virginia</option>
                                    <option value="WI">Wisconsin</option>
                                    <option value="WY">Wyoming</option>
                                </select>
                                <span tooltip="Please select a state" class="error-message">
                                    <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                </span>
                            </div>
                            <div class="form-group">
                                <label for="pickup_postal_code">Postal Code <span class="required">*</span></label>
                                <input type="text" id="pickup_postal_code" name="postal_code" required placeholder="Enter pickup postal code" value="11741" />
                                <span tooltip="Please enter a valid postal code" class="error-message">
                                    <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                </span>
                            </div>
                            <div class="form-group">
                                <label for="pickup_country">Country <span class="required">*</span></label>
                                <select id="pickup_country" name="country" class="js-states form-control">
                                    <option value="">Select Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->code }}" {{ $country->code == 'USA' ? 'selected' : '' }}>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                                <span tooltip="Please select a country" class="error-message">
                                    <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                </span>
                            </div>
                            <div class="form-group">
                                <label for="pickup_address_1">Address Line 1 <span class="required">*</span></label>
                                <input type="text" maxlength="50" id="pickup_address_1" name="address_1" required placeholder="Enter address line 1" />
                                <span class="error-message">
                                    <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                </span>
                            </div>
                            <div class="form-group">
                                <label for="pickup_address_2">Address Line 2</label>
                                <input type="text" maxlength="50" id="pickup_address_2" name="address_2" placeholder="Suite, apartment, or additional details" />
                            </div>
                            <div class="form-group">
                                <label for="pickup_contact_number">Contact Phone <span class="required">*</span></label>
                                <input type="tel" id="pickup_contact_number" name="contact_number" maxlength="10" pattern="[0-9]{10}" required placeholder="Enter 10-digit phone number" />
                                <span class="error-message">
                                    <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Delivery Details -->
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
                        <div class="form-fields" id="step3-fields">
                            <div class="form-group">
                                <label for="delivery_city">City <span class="required">*</span></label>
                                <input type="text" id="delivery_city" name="city" required placeholder="Enter delivery city" value="Cincinnati" />
                                <span class="error-message">
                                    <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                </span>
                            </div>
                            <div class="form-group">
                                <label for="delivery_state">State <span class="required">*</span></label>
                                <select id="delivery_state" name="state" class="js-states form-control" required>
                                    <option value="">Select State</option>
                                    <option value="OH" selected>Ohio</option>
                                    <!-- Other states -->
                                    <option value="AL">Alabama</option>
                                    <option value="AK">Alaska</option>
                                    <option value="AZ">Arizona</option>
                                    <option value="AR">Arkansas</option>
                                    <option value="CA">California</option>
                                    <option value="CO">Colorado</option>
                                    <option value="CT">Connecticut</option>
                                    <option value="DE">Delaware</option>
                                    <option value="FL">Florida</option>
                                    <option value="GA">Georgia</option>
                                    <option value="HI">Hawaii</option>
                                    <option value="ID">Idaho</option>
                                    <option value="IL">Illinois</option>
                                    <option value="IN">Indiana</option>
                                    <option value="IA">Iowa</option>
                                    <option value="KS">Kansas</option>
                                    <option value="KY">Kentucky</option>
                                    <option value="LA">Louisiana</option>
                                    <option value="ME">Maine</option>
                                    <option value="MD">Maryland</option>
                                    <option value="MA">Massachusetts</option>
                                    <option value="MI">Michigan</option>
                                    <option value="MN">Minnesota</option>
                                    <option value="MS">Mississippi</option>
                                    <option value="MO">Missouri</option>
                                    <option value="MT">Montana</option>
                                    <option value="NE">Nebraska</option>
                                    <option value="NV">Nevada</option>
                                    <option value="NH">New Hampshire</option>
                                    <option value="NJ">New Jersey</option>
                                    <option value="NM">New Mexico</option>
                                    <option value="NY">New York</option>
                                    <option value="NC">North Carolina</option>
                                    <option value="ND">North Dakota</option>
                                    <option value="OK">Oklahoma</option>
                                    <option value="OR">Oregon</option>
                                    <option value="PA">Pennsylvania</option>
                                    <option value="RI">Rhode Island</option>
                                    <option value="SC">South Carolina</option>
                                    <option value="SD">South Dakota</option>
                                    <option value="TN">Tennessee</option>
                                    <option value="TX">Texas</option>
                                    <option value="UT">Utah</option>
                                    <option value="VT">Vermont</option>
                                    <option value="VA">Virginia</option>
                                    <option value="WA">Washington</option>
                                    <option value="WV">West Virginia</option>
                                    <option value="WI">Wisconsin</option>
                                    <option value="WY">Wyoming</option>
                                </select>
                                <span class="error-message">
                                    <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                </span>
                            </div>
                            <div class="form-group">
                                <label for="delivery_postal_code">Postal Code <span class="required">*</span></label>
                                <input type="text" id="delivery_postal_code" name="postal_code" required placeholder="Enter delivery postal code" value="45203" />
                                <span class="error-message">Please enter a valid postal code.</span>
                            </div>
                            <div class="form-group">
                                <label for="delivery_country">Country <span class="required">*</span></label>
                                <select id="delivery_country" name="country" class="js-states form-control">
                                    <option value="">Select Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->code }}" {{ $country->code == 'USA' ? 'selected' : '' }}>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                                <span class="error-message">
                                    <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                </span>
                            </div>
                            <div class="form-group">
                                <label for="delivery_address_1">Address Line 1 <span class="required">*</span></label>
                                <input type="text" maxlength="50" id="delivery_address_1" name="address_1" required placeholder="Enter delivery address" />
                                <span class="error-message">
                                    <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                </span>
                            </div>
                            <div class="form-group">
                                <label for="delivery_address_2">Address Line 2</label>
                                <input type="text" maxlength="50" id="delivery_address_2" name="address_2" placeholder="Suite, apartment, or additional details" />
                                <span class="error-message">
                                    <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Add Quote Commodities -->
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
                        <div class="form-fields" id="step4-fields">
                            <div class="form-group">
                                <label for="quantity">Quantity <span class="required">*</span></label>
                                <input type="number" id="quantity" name="quantity" required placeholder="Enter quantity" value="2" />
                                <span class="error-message">
                                    <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                </span>
                            </div>
                            <div class="form-group">
                                <label for="unit_type">Unit Type<span class="required">*</span></label>
                                <select id="unit_type" name="unit_type" class="js-states form-control">
                                    <option value="">Select Unit Type</option>
                                    @foreach($unitTypes as $unitType)
                                        <option value="{{ $unitType->code }}" {{ $unitType->code == 'PLT' ? 'selected' : '' }}>{{ $unitType->name }}</option>
                                    @endforeach
                                </select>
                                <span class="error-message">
                                    <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                </span>
                            </div>
                            <div class="form-group">
                                <label for="freight_class_code">Freight Class Code<span class="required">*</span></label>
                                <select id="freight_class_code" name="freight_class_code" class="js-states form-control">
                                    <option value="">Select Freight Class</option>
                                    @foreach($freightClasses as $freightClass)
                                        <option value="{{ $freightClass->code }}" {{ $freightClass->code == '110' ? 'selected' : '' }}>{{ $freightClass->code }}</option>
                                    @endforeach
                                </select>
                                <span class="error-message">
                                    <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                </span>
                            </div>
                            <div class="form-group">
                                <label for="weight">Weight (lbs) <span class="required">*</span></label>
                                <input type="number" id="weight" name="weight" required placeholder="Enter total weight in lbs" value="294" step="0.1" />
                                <span class="error-message">
                                    <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                </span>
                            </div>
                            <div class="form-group">
                                <label for="length">Length (inches) <span class="required">*</span></label>
                                <input type="number" id="length" name="length" required placeholder="Enter length (1–636)" value="102" />
                                <span class="error-message">
                                    <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                </span>
                            </div>
                            <div class="form-group">
                                <label for="width">Width (inches) <span class="required">*</span></label>
                                <input type="number" id="width" name="width" required placeholder="Enter width (1–102)" value="62" />
                                <span class="error-message">
                                    <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                </span>
                            </div>
                            <div class="form-group">
                                <label for="height">Height (inches) <span class="required">*</span></label>
                                <input type="number" id="height" name="height" required placeholder="Enter height (1–102)" value="41" />
                                <span class="error-message">
                                    <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                </span>
                            </div>
                            <div class="form-group">
                                <label for="ark__additionalServices">Additional Services <span class="required">*</span></label>
                                <div class="ark__radio-group" id="ark__additionalServices">
                                    <label class="ark__custom-radio">
                                        <input type="radio" name="additional_services[]" value="devanning" id="devanning">
                                        <span class="ark__radio-label">Devanning</span>
                                    </label>
                                    <label class="ark__custom-radio">
                                        <input type="radio" name="additional_services[]" value="labeling" id="labeling">
                                        <span class="ark__radio-label">Labeling</span>
                                    </label>
                                    <label class="ark__custom-radio">
                                        <input type="radio" name="additional_services[]" value="transshipment" id="transshipment">
                                        <span class="ark__radio-label">Transshipment</span>
                                    </label>
                                </div>
                                <span class="error-message">
                                    <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                </span>
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
                        <span class="spinner-border spinner-border-sm ms-2 d-none" id="nextSpinner"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    // Initialize Tippy for tooltips
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

    let currentStep = 1;
    const totalSteps = 4;

    // Open Drawer
    $('#openDrawerBtn').on('click', function () {
        $('#sideDrawer, #drawerOverlay').fadeIn(300);
        currentStep = 1;
        updateStep();
        // Set default quote date to tomorrow and update display
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const formattedDate = formatDate(tomorrow);
        $('#shipment_date').val(tomorrow.toISOString().split('T')[0]);
        $('#dateDisplay').text(formattedDate).removeClass('placeholder');
    });

    // Close Drawer
    $('#closeDrawerBtn, #drawerOverlay, #closePopup').on('click', function () {
        $('#sideDrawer, #drawerOverlay, #successPopup').fadeOut(300);
    });

    // Prevent closing when clicking inside drawer
    $('#sideDrawer').on('click', function (e) {
        e.stopPropagation();
    });

    // Next Button
    $('#nextBtn').on('click', function () {
        if (validateStep(currentStep)) {
            saveStep(currentStep);
        }
    });

    // Back Button
    $('#backBtn').on('click', function () {
        if (currentStep > 1) {
            currentStep--;
            updateStep();
        }
    });

    // Update Step UI
    function updateStep() {
        $('.wizard-step').removeClass('active');
        $(`.wizard-step[data-step="${currentStep}"]`).addClass('active');

        $('.form-fields').removeClass('active');
        $(`#step${currentStep}-fields`).addClass('active');

        $('#backBtn').prop('disabled', currentStep === 1);
        $('#nextBtn p').text(currentStep === totalSteps ? 'Submit' : 'Next');

        // Update progress indicators
        $('.step-number').removeClass('active completed');
        for (let i = 1; i <= totalSteps; i++) {
            const $step = $(`.wizard-step[data-step="${i}"] .step-number`);
            if (i < currentStep) $step.addClass('completed');
            if (i === currentStep) $step.addClass('active');
        }
    }

    // Validate current step
    function validateStep(step) {
        let isValid = true;
        const $fields = $(`#step${step}-fields .form-group`);

        $fields.each(function () {
            const $input = $(this).find('input, select').first();
            const value = $input.val()?.trim();
            const isRequired = $input.prop('required');
            
            if (!isRequired) {
                $(this).removeClass('error');
                return true;
            }

            if (isRequired && !value) {
                showError($(this));
                isValid = false;
            } else {
                hideError($(this));
            }
        });

        return isValid;
    }

    // Show/Hide error
    function showError($group) {
        $group.find('.error-message').addClass('show');
    }
    function hideError($group) {
        $group.find('.error-message').removeClass('show');
    }

    // Save Step via AJAX
    function saveStep(step) {
        const formData = new FormData($('#quoteForm')[0]);
        let url = '';

        switch (step) {
            case 1:
                url = '{{ route("quotes.store.step1") }}';
                break;
            case 2:
                url = '{{ route("quotes.store.step2") }}';
                formData.append('quote_id', $('#quote_id').val());
                break;
            case 3:
                url = '{{ route("quotes.store.step3") }}';
                formData.append('quote_id', $('#quote_id').val());
                break;
            case 4:
                url = '{{ route("quotes.store.step4") }}';
                formData.append('quote_id', $('#quote_id').val());
                $('#nextSpinner').removeClass('d-none');
                $('#nextBtn').prop('disabled', true);
                break;
        }

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (data) {
                if (data.success) {
                    if (step === 1 && data.quote_id) {
                        $('#quote_id').val(data.quote_id);
                    }

                    if (step === totalSteps) {
                        $('#nextSpinner').addClass('d-none');
                        $('#nextBtn').prop('disabled', false);
                        $('#sideDrawer, #drawerOverlay').fadeOut(300);
                        $('#successPopup').fadeIn(300);
                        // Optionally reload quotes list
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        currentStep = data.next_step || currentStep + 1;
                        updateStep();
                    }
                } else {
                    alert(data.message || 'Validation failed. Please check your inputs.');
                    if (step === totalSteps) {
                        $('#nextSpinner').addClass('d-none');
                        $('#nextBtn').prop('disabled', false);
                    }
                }
            },
            error: function () {
                // alert('An error occurred. Please try again.');
                if (step === totalSteps) {
                    $('#nextSpinner').addClass('d-none');
                    $('#nextBtn').prop('disabled', false);
                }
            }
        });
    }

    // Initialize Select2 (if using)
    if (typeof $.fn.select2 !== 'undefined') {
        $('.js-states').select2({
            placeholder: function () {
                return $(this).data('placeholder') || 'Select an option';
            }
        });
    }

    // Date Picker Functionality
    function formatDate(date) {
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const year = date.getFullYear();
        return `${month}/${day}/${year}`;
    }

    // Initialize date display
    function initializeDateDisplay() {
        const shipmentDate = $('#shipment_date').val();
        if (shipmentDate) {
            const date = new Date(shipmentDate);
            $('#dateDisplay').text(formatDate(date)).removeClass('placeholder');
        }
    }

    // Update date display when date changes
    $('#shipment_date').on('change', function () {
        const val = $(this).val();
        if (val) {
            const date = new Date(val);
            $('#dateDisplay').text(formatDate(date)).removeClass('placeholder');
        } else {
            $('#dateDisplay').text('MM/DD/YYYY').addClass('placeholder');
        }
    });

    // Open native date picker when clicking on the display
    $('#dateInputContainer').on('click', function() {
        $('#shipment_date')[0].showPicker();
    });

    // Initialize date display on page load
    initializeDateDisplay();
});
</script>
@endpush