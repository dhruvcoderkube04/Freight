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
<div class="card shadow-sm mt-4">
    <div class="card-body p-0">
        <table id="quotesTable" class="table table-hover mb-0" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th>Quote ID</th>
                    <th>Date</th>
                    <th>Origin</th>
                    <th>Destination</th>
                    <th>Available Rates</th>
                    <th>Best Rate</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@else
<div class="empty-state text-center py-5">
    <h3>No quotes yet</h3>
    <p>Create your first freight quote to get started.</p>
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
        <p id="popupMessage">Your Quote details have been submitted for review. Our team will verify the information and update the status shortly.</p>
        <div class="spinner-border text-primary" id="popupSpinner" role="status"></div>
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
                                <select id="pickup_location" name="pickup_location" required class="js-states form-control">
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
                                <select id="drop_location" name="drop_location" required class="js-states form-control">
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
                                <input type="text" id="pickup_city" name="pickup_city" required placeholder="Enter pickup city" value="Holbrook" />
                                <span tooltip="Please enter a city" class="error-message">
                                    <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                </span>
                            </div>
                            <div class="form-group">
                                <label for="pickup_state">State <span class="required">*</span></label>
                                <select id="pickup_state" name="pickup_state" class="js-states form-control" required>
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
                                <input type="text" id="pickup_postal_code" name="pickup_postal_code" required placeholder="Enter pickup postal code" value="11741" />
                                <span tooltip="Please enter a valid postal code" class="error-message">
                                    <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                </span>
                            </div>
                            <div class="form-group">
                                <label for="pickup_country">Country <span class="required">*</span></label>
                                <select id="pickup_country" name="pickup_country" required class="js-states form-control">
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
                                <input type="text" maxlength="50" id="pickup_address_1" name="pickup_address_1" required placeholder="Enter address line 1" />
                                <span class="error-message">
                                    <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                </span>
                            </div>
                            <div class="form-group">
                                <label for="pickup_address_2">Address Line 2</label>
                                <input type="text" maxlength="50" id="pickup_address_2" name="pickup_address_2" placeholder="Suite, apartment, or additional details" />
                            </div>
                            <div class="form-group">
                                <label for="pickup_contact_number">Contact Phone <span class="required">*</span></label>
                                <input type="tel" id="pickup_contact_number" name="pickup_contact_number" maxlength="10" pattern="[0-9]{10}" required placeholder="Enter 10-digit phone number" />
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
                                <input type="text" id="delivery_city" name="delivery_city" required placeholder="Enter delivery city" value="Cincinnati" />
                                <span class="error-message">
                                    <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                </span>
                            </div>
                            <div class="form-group">
                                <label for="delivery_state">State <span class="required">*</span></label>
                                <select id="delivery_state" name="delivery_state" class="js-states form-control" required>
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
                                <input type="text" id="delivery_postal_code" name="delivery_postal_code" required placeholder="Enter delivery postal code" value="45203" />
                                <span class="error-message">Please enter a valid postal code.</span>
                            </div>
                            <div class="form-group">
                                <label for="delivery_country">Country <span class="required">*</span></label>
                                <select id="delivery_country" name="delivery_country" required class="js-states form-control">
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
                                <input type="text" maxlength="50" id="delivery_address_1" name="delivery_address_1" required placeholder="Enter delivery address" />
                                <span class="error-message">
                                    <img src="{{ asset('assets/images/input-error.svg') }}" alt="">
                                </span>
                            </div>
                            <div class="form-group">
                                <label for="delivery_address_2">Address Line 2</label>
                                <input type="text" maxlength="50" id="delivery_address_2" name="delivery_address_2" placeholder="Suite, apartment, or additional details" />
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
                                <select id="unit_type" name="unit_type" required class="js-states form-control">
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
                                <select id="freight_class_code" name="freight_class_code" required class="js-states form-control">
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


<!-- Carrier Rates Modal -->
<div class="modal fade" id="ratesModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Select Carrier Rate</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table id="carrierRatesTable" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Carrier</th>
                            <th>Service</th>
                            <th>Transit Days</th>
                            <th>Rate</th>
                            <th>Preferred</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openRatesModal(button) {
    const encryptedId = button.getAttribute('data-quote-id');
    const carriersJson = button.getAttribute('data-carriers');

    // Safely parse JSON
    let carriers = [];
    try {
        carriers = JSON.parse(carriersJson);
    } catch (e) {
        console.error('Invalid JSON:', carriersJson);
        alert('Error loading carrier data');
        return;
    }

    // Open Modal
    const modal = new bootstrap.Modal(document.getElementById('ratesModal'));
    const tbody = $('#carrierRatesTable tbody');
    tbody.empty();

    if (carriers.length === 0) {
        tbody.append('<tr><td colspan="6" class="text-center text-muted">No carrier rates available</td></tr>');
    } else {
        carriers.forEach((c, i) => {
            const preferredBadge = c.isPreferred 
                ? '<span class="badge bg-warning ms-2">Preferred</span>' 
                : '';
            const cotyBadge = c.isCarrierOfTheYear 
                ? '<span class="badge bg-info ms-2">Carrier of Year</span>' 
                : '';

            const row = `
                <tr>
                    <td>
                        <strong>${c.carrier || 'Unknown'}</strong><br>
                        <small class="text-muted">SCAC: ${c.scac || '—'}</small>
                        ${preferredBadge} ${cotyBadge}
                    </td>
                    <td>${c.serviceLevelDescription || c.serviceLevel || 'Standard'}</td>
                    <td>${c.transitDays ? c.transitDays + ' day' + (c.transitDays > 1 ? 's' : '') : '—'}</td>
                    <td><strong class="text-success">$${parseFloat(c.customerRate || 0).toFixed(2)}</strong></td>
                    <td>${c.isPreferred ? 'Yes' : 'No'}</td>
                    <td>
                        <form action="/quotes/${encryptedId}/payment" method="POST" style="display:inline">
                            @csrf
                            <input type="hidden" name="selected_carrier_index" value="${i}">
                            <button type="submit" class="btn btn-sm btn-success">
                                Select & Pay
                            </button>
                        </form>

                    </td>
                </tr>`;
            tbody.append(row);
        });
    }

    // Re-init DataTable
    if ($.fn.DataTable.isDataTable('#carrierRatesTable')) {
        $('#carrierRatesTable').DataTable().destroy();
    }
    $('#carrierRatesTable').DataTable({
        paging: carriers.length > 10,
        searching: false,
        info: false,
        ordering: true,
        order: [[3, 'asc']],
        columnDefs: [{ targets: 5, orderable: false }]
    });

    modal.show();
}

// Initialize main table
$(document).ready(function() {
    $('#quotesTable').DataTable({
        data: @json($quoteTableData),
        pageLength: 10,
        responsive: true,
        order: [[1, 'desc']],
        columns: [
            { data: 'id', render: data => `<strong>#${data}</strong>` },
            { data: 'created_at' },
            { data: 'origin' },
            { data: 'destination' },
            { 
                data: 'carrier_count', 
                render: count => `<span class="badge bg-info">${count} option${count !== 1 ? 's' : ''}</span>`
            },
            { 
                data: 'best_rate', 
                render: rate => rate !== '—' ? `<strong class="text-success">${rate}</strong>` : rate
            },
            { 
                data: 'status', 
                render: status => status === 'Ready' 
                    ? '<span class="badge bg-success">Ready</span>'
                    : '<span class="badge bg-danger">Failed</span>'
            },
            {
                data: null,
                orderable: false,
                render: function(row) {
                    if (row.has_rates) {
                        return `<button class="btn btn-sm btn-primary"
                                      data-quote-id="${row.encrypted_id}"
                                      data-carriers="${row.carriers_json}"
                                      onclick="openRatesModal(this)">
                            View Rates
                        </button>`;
                    }
                    return '<span class="text-muted">No rates</span>';
                }
            }
        ]
    });
});
</script>
<script>
    // Fixed and optimized script for quote form
    document.addEventListener('DOMContentLoaded', function () {
        let currentStep = 1;
        const totalSteps = 4;

        // Get ALL elements safely
        const drawer = document.getElementById('sideDrawer');
        const overlay = document.getElementById('drawerOverlay');
        const successPopup = document.getElementById('successPopup');
        const nextBtn = document.getElementById('nextBtn');
        const backBtn = document.getElementById('backBtn');
        const nextText = nextBtn ? nextBtn.querySelector('p') : null;
        const nextSpinner = document.getElementById('nextSpinner');
        const popupMessage = document.getElementById('popupMessage');
        const popupSpinner = document.getElementById('popupSpinner');

        // ============================================
        // OPEN/CLOSE DRAWER
        // ============================================
        const openBtn = document.getElementById('openDrawerBtn');
        if (openBtn) {
            openBtn.addEventListener('click', () => {
                drawer.style.display = 'block';
                overlay.style.display = 'block';
                document.body.style.overflow = 'hidden';
                currentStep = 1;
                updateStep();
                // setTomorrowDate();
                clearErrors();
            });
        }

        // Close everything
        document.querySelectorAll('#closeDrawerBtn, #drawerOverlay, #closePopup').forEach(el => {
            if (el) {
                el.addEventListener('click', () => {
                    drawer.style.display = 'none';
                    overlay.style.display = 'none';
                    document.body.style.overflow = '';
                    if (successPopup) successPopup.style.display = 'none';
                });
            }
        });

        // Prevent drawer close on drawer click
        if (drawer) {
            drawer.addEventListener('click', e => e.stopPropagation());
        }

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && drawer && drawer.style.display === 'block') {
                drawer.style.display = 'none';
                overlay.style.display = 'none';
                document.body.style.overflow = '';
            }
        });

        // ============================================
        // NAVIGATION BUTTONS
        // ============================================
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                if (validateStep(currentStep)) {
                    if (currentStep < totalSteps) {
                        currentStep++;
                        updateStep();
                    } else {
                        submitQuote();
                    }
                }
            });
        }

        if (backBtn) {
            backBtn.addEventListener('click', () => {
                if (currentStep > 1) {
                    currentStep--;
                    updateStep();
                }
            });
        }

        // ============================================
        // DATE PICKER - SET TOMORROW AS DEFAULT
        // ============================================
        // function setTomorrowDate() {
        //     const shipmentDate = document.getElementById('shipment_date');
        //     const dateDisplay = document.getElementById('dateDisplay');
        //     if (shipmentDate && dateDisplay) {
        //         const tomorrow = new Date();
        //         tomorrow.setDate(tomorrow.getDate() + 1);
        //         const tomorrowStr = tomorrow.toISOString().split('T')[0];
        //         shipmentDate.value = tomorrowStr;
        //         dateDisplay.textContent = tomorrow.toLocaleDateString('en-US', {
        //             month: '2-digit',
        //             day: '2-digit',
        //             year: 'numeric'
        //         });
        //         dateDisplay.classList.remove('placeholder');
        //     }
        // }

        // ============================================
        // UPDATE STEP DISPLAY
        // ============================================
        function updateStep() {
            // Hide all steps
            document.querySelectorAll('.wizard-step').forEach(step => {
                step.classList.remove('active', 'completed');
            });
            document.querySelectorAll('.form-fields').forEach(field => {
                field.classList.remove('active');
            });

            // Show current and completed steps
            document.querySelectorAll('.wizard-step').forEach((step, index) => {
                const stepNum = index + 1;
                if (stepNum < currentStep) {
                    step.classList.add('completed');
                } else if (stepNum === currentStep) {
                    step.classList.add('active');
                }
            });

            // Show current fields
            const currentStepEl = document.querySelector(`.wizard-step[data-step="${currentStep}"]`);
            const currentFieldEl = document.getElementById(`step${currentStep}-fields`);
            if (currentStepEl) currentStepEl.classList.add('active');
            if (currentFieldEl) currentFieldEl.classList.add('active');

            // Update buttons
            if (backBtn) backBtn.disabled = currentStep === 1;
            
            // Update next button text and appearance
            if (nextText) {
                if (currentStep === totalSteps) {
                    nextText.textContent = 'Submit';
                    nextBtn.classList.add('submit-mode');
                } else {
                    nextText.textContent = 'Next';
                    nextBtn.classList.remove('submit-mode');
                }
            }

            // Update step number icons
            document.querySelectorAll('.step-number').forEach((el, index) => {
                const stepNum = index + 1;
                el.classList.remove('completed', 'active');
                if (stepNum < currentStep) {
                    el.classList.add('completed');
                } else if (stepNum === currentStep) {
                    el.classList.add('active');
                }
            });

            clearErrors();
        }

        // ============================================
        // CLEAR ERROR MESSAGES
        // ============================================
        function clearErrors() {
            document.querySelectorAll('.form-group').forEach(group => {
                group.classList.remove('error');
            });
            document.querySelectorAll('.error-message').forEach(error => {
                error.classList.remove('show');
            });
        }

        // ============================================
        // VALIDATE STEP - SKIP OPTIONAL ADDRESS LINE 2
        // ============================================
        function validateStep(step) {
            let isValid = true;
            const groups = document.querySelectorAll(`#step${step}-fields .form-group`);

            groups.forEach(group => {
                const inputs = group.querySelectorAll('input, select, textarea');
                if (!inputs.length) return;

                // Clear previous errors
                group.classList.remove('error');
                const errorElem = group.querySelector('.error-message');
                if (errorElem) errorElem.classList.remove('show');

                // Check if any input in this group is required
                const hasRequiredInput = [...inputs].some(i => i.required);
                if (!hasRequiredInput) return; // Skip optional fields

                // For radio/checkbox groups
                if (inputs.length > 1 && (inputs[0].type === 'checkbox' || inputs[0].type === 'radio')) {
                    const isChecked = [...inputs].some(i => i.checked);
                    if (!isChecked && hasRequiredInput) {
                        group.classList.add('error');
                        if (errorElem) errorElem.classList.add('show');
                        isValid = false;
                    }
                } else {
                    // For regular inputs
                    const hasValue = [...inputs].some(i => {
                        const value = (i.value || '').trim();
                        return value.length > 0;
                    });
                    
                    if (!hasValue && hasRequiredInput) {
                        group.classList.add('error');
                        if (errorElem) errorElem.classList.add('show');
                        isValid = false;
                    }
                }
            });

            return isValid;
        }

        // ============================================
        // SHOW/HIDE LOADING SPINNER
        // ============================================
        function showLoading(show = true) {
            if (nextSpinner) {
                nextSpinner.classList.toggle('d-none', !show);
            }
            if (nextBtn) {
                nextBtn.disabled = show;
                if (show) {
                    nextBtn.style.opacity = '0.7';
                    nextBtn.style.cursor = 'not-allowed';
                } else {
                    nextBtn.style.opacity = '1';
                    nextBtn.style.cursor = 'pointer';
                }
            }
        }

        // ============================================
        // SUBMIT QUOTE FORM
        // ============================================
        function submitQuote() {
            // Final validation for ALL steps
            let allValid = true;
            for (let step = 1; step <= totalSteps; step++) {
                if (!validateStep(step)) {
                    allValid = false;
                    if (step !== currentStep) {
                        currentStep = step;
                        updateStep();
                    }
                    break;
                }
            }

            if (!allValid) {
                alert('Please fill all required fields before submitting.');
                return;
            }

            const form = document.getElementById('quoteForm');
            if (!form) return;

            const formData = new FormData(form);

            // Handle additional_services - remove duplicates
            const checkboxes = document.querySelectorAll('input[name="additional_services[]"]:checked');
            const uniqueServices = [...new Set([...checkboxes].map(cb => cb.value))];
            formData.delete('additional_services[]');
            uniqueServices.forEach(service => {
                formData.append('additional_services[]', service);
            });

            showLoading(true);

            fetch(form.action || '/quotes/store', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Hide drawer
                    drawer.style.display = 'none';
                    overlay.style.display = 'none';
                    document.body.style.overflow = '';
                    
                    // Show success popup
                    if (successPopup && popupMessage) {
                        popupMessage.textContent = 'Quote Created Successfully!';
                        if (popupSpinner) popupSpinner.style.display = 'none';
                        successPopup.style.display = 'flex';
                    }

                    // Redirect after 2 seconds
                    setTimeout(() => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            window.location.reload();
                        }
                    }, 2000);
                } else {
                    throw new Error(data.message || 'Unknown error occurred');
                }
            })
            .catch(error => {
                console.error('Quote submission error:', error);
                alert(`Error: ${error.message}`);
            })
            .finally(() => {
                showLoading(false);
            });
        }

        // ============================================
        // CUSTOM DATE PICKER
        // ============================================
        const dateInputContainer = document.getElementById('dateInputContainer');
        const calendarPopup = document.getElementById('calendarPopup');
        const shipmentDate = document.getElementById('shipment_date');
        const dateDisplay = document.getElementById('dateDisplay');
        const calendarGrid = document.getElementById('calendarGrid');
        const calendarTitle = document.getElementById('calendarTitle');
        const prevMonthBtn = document.getElementById('prevMonth');
        const nextMonthBtn = document.getElementById('nextMonth');

        let currentCalendarDate = new Date();
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'];

        function renderCalendar() {
            if (!calendarGrid) return;
            
            const year = currentCalendarDate.getFullYear();
            const month = currentCalendarDate.getMonth();

            calendarTitle.textContent = `${monthNames[month]} ${year}`;

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const daysInPrevMonth = new Date(year, month, 0).getDate();

            calendarGrid.innerHTML = '';

            // Day headers
            const dayHeaders = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
            dayHeaders.forEach(day => {
                const header = document.createElement('div');
                header.className = 'calendar-day-header';
                header.textContent = day;
                calendarGrid.appendChild(header);
            });

            // Previous month days
            for (let i = firstDay - 1; i >= 0; i--) {
                const day = document.createElement('div');
                day.className = 'calendar-day disabled';
                day.textContent = daysInPrevMonth - i;
                calendarGrid.appendChild(day);
            }

            // Current month days
            const today = new Date();
            const selectedDateValue = shipmentDate.value;
            
            for (let i = 1; i <= daysInMonth; i++) {
                const day = document.createElement('div');
                day.className = 'calendar-day';
                day.textContent = i;

                const dayDate = new Date(year, month, i);
                const dayDateStr = dayDate.toISOString().split('T')[0];

                // Mark today
                if (dayDate.toDateString() === today.toDateString()) {
                    day.classList.add('today');
                }

                // Mark selected
                if (selectedDateValue === dayDateStr) {
                    day.classList.add('selected');
                }

                // Disable past dates
                if (dayDate < today.setHours(0, 0, 0, 0)) {
                    day.classList.add('disabled');
                } else {
                    day.addEventListener('click', () => selectDate(dayDate));
                }

                calendarGrid.appendChild(day);
            }

            // Next month days
            const remainingDays = 42 - (firstDay + daysInMonth);
            for (let i = 1; i <= remainingDays; i++) {
                const day = document.createElement('div');
                day.className = 'calendar-day disabled';
                day.textContent = i;
                calendarGrid.appendChild(day);
            }
        }

        function selectDate(date) {
            const isoDate = date.toISOString().split('T')[0];
            shipmentDate.value = isoDate;
            dateDisplay.textContent = date.toLocaleDateString('en-US', {
                month: '2-digit',
                day: '2-digit',
                year: 'numeric'
            });
            dateDisplay.classList.remove('placeholder');

            // Remove error state
            const formGroup = dateInputContainer.closest('.form-group');
            if (formGroup) formGroup.classList.remove('error');

            // Close calendar
            calendarPopup.style.display = 'none';
            dateInputContainer.classList.remove('focused');
        }

        // Toggle calendar
        if (dateInputContainer) {
            dateInputContainer.addEventListener('click', (e) => {
                e.stopPropagation();
                const isVisible = calendarPopup.style.display === 'block';
                
                if (isVisible) {
                    calendarPopup.style.display = 'none';
                    dateInputContainer.classList.remove('focused');
                } else {
                    calendarPopup.style.display = 'block';
                    dateInputContainer.classList.add('focused');
                    renderCalendar();
                }
            });
        }

        // Navigate months
        if (prevMonthBtn) {
            prevMonthBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                currentCalendarDate.setMonth(currentCalendarDate.getMonth() - 1);
                renderCalendar();
            });
        }

        if (nextMonthBtn) {
            nextMonthBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                currentCalendarDate.setMonth(currentCalendarDate.getMonth() + 1);
                renderCalendar();
            });
        }

        // Close calendar when clicking outside
        document.addEventListener('click', (e) => {
            if (calendarPopup && !calendarPopup.contains(e.target) && 
                !dateInputContainer.contains(e.target)) {
                calendarPopup.style.display = 'none';
                if (dateInputContainer) dateInputContainer.classList.remove('focused');
            }
        });

        // ============================================
        // REMOVE ERROR ON INPUT
        // ============================================
        document.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('input', () => {
                const formGroup = field.closest('.form-group');
                if (formGroup && field.value.trim()) {
                    formGroup.classList.remove('error');
                    const errorElem = formGroup.querySelector('.error-message');
                    if (errorElem) errorElem.classList.remove('show');
                }
            });
        });

        // ============================================
        // INITIALIZE SELECT2 IF AVAILABLE
        // ============================================
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('.js-states').select2({
                placeholder: 'Select an option',
                width: '100%'
            });
        }
    });
</script>
@endpush