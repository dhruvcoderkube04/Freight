@extends('layouts.app')

@section('content')
<div class="container">
    <li class="nav-item">
        <a class="nav-link" href="{{ route('quotes.index') }}">
            <i class="fas fa-receipt me-2"></i>My Quotes
        </a>
    </li>
    <div class="row">
        <div class="col-12 text-center mb-4">
            <h1 class="display-4 text-primary">Get A Quote</h1>
            <p class="lead">Complete the form below to get your instant shipping quote</p>
        </div>
    </div>

    <!-- Progress Steps -->
    <div class="row justify-content-center mb-5">
        <div class="col-lg-10">
            <div class="step-progress">
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
        </div>
    </div>

    <div class="row">
        <!-- Left Column - Form -->
        <div class="col-lg-6">
            <form id="shipmentForm">
                @csrf
                <input type="hidden" name="shipment_id" id="shipment_id">

                <!-- Step 1: Shipment Information -->
                <div class="form-step active" id="step1">
                    <div class="form-section">
                        <h3 class="mb-4">
                            <i class="fas fa-shipping-fast text-primary me-2"></i>
                            Shipment Information
                        </h3>
                        <p class="text-muted mb-4">Select the pickup and drop location types and choose the requested shipment date.</p>

                        <div class="mb-3">
                            <label for="pickup_location" class="form-label">Pickup Location Type</label>
                            <select class="form-select" id="pickup_location" name="pickup_location">
                                <option value="">Select pickup location type</option>
                                @foreach($locationTypes as $locationType)
                                    <option value="{{ $locationType->code }}">{{ $locationType->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="drop_location" class="form-label">Drop Location Type</label>
                            <select class="form-select" id="drop_location" name="drop_location">
                                <option value="">Select drop location type</option>
                                @foreach($locationTypes as $locationType)
                                    <option value="{{ $locationType->code }}">{{ $locationType->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="shipment_date" class="form-label">Shipment Date</label>
                            <input type="date" class="form-control" id="shipment_date" name="shipment_date" min="{{ date('Y-m-d') }}">
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary btn-action" disabled>
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </button>
                            <button type="button" class="btn btn-primary btn-action" onclick="saveStep(1)">
                                Next <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Pickup Details -->
                <div class="form-step" id="step2">
                    <div class="form-section">
                        <h3 class="mb-4">
                            <i class="fas fa-map-marker-alt text-warning me-2"></i>
                            Pickup Details
                        </h3>
                        <p class="text-muted mb-4">Enter the pickup location, contact information, and pickup hours.</p>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="pickup_city" class="form-label">City</label>
                                <input type="text" class="form-control" id="pickup_city" name="city" value="Holbrook">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pickup_state" class="form-label">State</label>
                                <select class="form-select" id="pickup_state" name="state">
                                    <option value="">Select State</option>
                                    <option value="NY" selected>New York</option>
                                    <!-- Other states remain static for now -->
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
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="pickup_postal_code" class="form-label">Postal Code</label>
                                <input type="text" class="form-control" id="pickup_postal_code" name="postal_code" value="11741">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pickup_country" class="form-label">Country</label>
                                <select class="form-select" id="pickup_country" name="country">
                                    <option value="">Select Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->code }}" {{ $country->code == 'USA' ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="pickup_address_1" class="form-label">Address Line 1</label>
                            <input type="text" class="form-control" id="pickup_address_1" name="address_1">
                        </div>

                        <div class="mb-3">
                            <label for="pickup_address_2" class="form-label">Address Line 2</label>
                            <input type="text" class="form-control" id="pickup_address_2" name="address_2">
                        </div>

                        <div class="mb-4">
                            <label for="pickup_contact_number" class="form-label">Contact Number</label>
                            <input type="tel" class="form-control" id="pickup_contact_number" name="contact_number">
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary btn-action" onclick="previousStep(2)">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </button>
                            <button type="button" class="btn btn-primary btn-action" onclick="saveStep(2)">
                                Next <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Delivery Details -->
                <div class="form-step" id="step3">
                    <div class="form-section">
                        <h3 class="mb-4">
                            <i class="fas fa-truck text-success me-2"></i>
                            Delivery Details
                        </h3>
                        <p class="text-muted mb-4">Enter the receiver's location and contact information.</p>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="delivery_city" class="form-label">City</label>
                                <input type="text" class="form-control" id="delivery_city" name="city" value="Cincinnati">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="delivery_state" class="form-label">State</label>
                                <select class="form-select" id="delivery_state" name="state">
                                    <option value="">Select State</option>
                                    <option value="OH" selected>Ohio</option>
                                    <!-- Other states remain static for now -->
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
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="delivery_postal_code" class="form-label">Postal Code</label>
                                <input type="text" class="form-control" id="delivery_postal_code" name="postal_code" value="45203">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="delivery_country" class="form-label">Country</label>
                                <select class="form-select" id="delivery_country" name="country">
                                    <option value="">Select Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->code }}" {{ $country->code == 'USA' ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="delivery_address_1" class="form-label">Address Line 1</label>
                            <input type="text" class="form-control" id="delivery_address_1" name="address_1">
                        </div>

                        <div class="mb-3">
                            <label for="delivery_address_2" class="form-label">Address Line 2</label>
                            <input type="text" class="form-control" id="delivery_address_2" name="address_2">
                        </div>

                        <div class="mb-4">
                            <label for="delivery_contact_number" class="form-label">Contact Number</label>
                            <input type="tel" class="form-control" id="delivery_contact_number" name="contact_number">
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary btn-action" onclick="previousStep(3)">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </button>
                            <button type="button" class="btn btn-primary btn-action" onclick="saveStep(3)">
                                Next <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Add Quote Commodities -->
                <div class="form-step" id="step4">
                    <div class="form-section">
                        <h3 class="mb-4">
                            <i class="fas fa-boxes text-info me-2"></i>
                            Add Quote Commodities
                        </h3>
                        <p class="text-muted mb-4">Provide details for any commodity including description, quantity, cart type, and dimensions.</p>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" value="2">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="unit_type" class="form-label">Unit Type</label>
                                <select class="form-select" id="unit_type" name="unit_type">
                                    <option value="">Select Unit Type</option>
                                    @foreach($unitTypes as $unitType)
                                        <option value="{{ $unitType->code }}" {{ $unitType->code == 'pallet' ? 'selected' : '' }}>
                                            {{ $unitType->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="freight_class_code" class="form-label">Freight Class Code</label>
                                <select class="form-select" id="freight_class_code" name="freight_class_code">
                                    <option value="">Select Freight Class</option>
                                    @foreach($freightClasses as $freightClass)
                                        <option value="{{ $freightClass->code }}" {{ $freightClass->code == '110' ? 'selected' : '' }}>
                                            {{ $freightClass->code }} - {{ $freightClass->description }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="weight" class="form-label">Weight (lbs)</label>
                                <input type="number" class="form-control" id="weight" name="weight" value="294" step="0.1">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="length" class="form-label">Length (inches)</label>
                                <input type="number" class="form-control" id="length" name="length" value="102">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="width" class="form-label">Width (inches)</label>
                                <input type="number" class="form-control" id="width" name="width" value="62">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="height" class="form-label">Height (inches)</label>
                                <input type="number" class="form-control" id="height" name="height" value="41">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Additional Services</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="additional_services[]" value="devanning" id="devanning">
                                <label class="form-check-label" for="devanning">Devanning</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="additional_services[]" value="transshipment" id="transshipment">
                                <label class="form-check-label" for="transshipment">Transshipment</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="additional_services[]" value="labeling" id="labeling">
                                <label class="form-check-label" for="labeling">Labeling</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary btn-action" onclick="previousStep(4)">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </button>
                            <button type="button"
                                    id="submitQuoteBtn"
                                    class="btn btn-success btn-action"
                                    onclick="saveStep(4)">
                                Submit Quote
                                <span class="spinner-border spinner-border-sm ms-2 d-none" id="btnSpinner"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Right column --}}
        <div class="col-lg-6">
            <div class="quote-box">
                <h4 class="mb-4"><i class="fas fa-receipt text-primary me-2"></i> Quote Summary</h4>

                {{-- Placeholder (hidden after first submit) --}}
                <div id="quotePlaceholder" class="text-center text-muted py-5">
                    <i class="fas fa-shipping-fast fa-3x mb-3"></i>
                    <h5>Enter details to get a quote</h5>
                </div>

                {{-- LOADER – hidden by default --}}
                <div id="quoteLoader" class="text-center py-5 d-none">
                    <div class="spinner-border text-primary mb-3" style="width:3rem;height:3rem;"></div>
                    <h5>Generating Quote…</h5>
                    <p class="text-muted">Please wait while we fetch the best rates for you.</p>
                </div>

                {{-- RESULT – hidden by default --}}
                <div id="apiResponse" class="d-none"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentStep = 1;
    const totalSteps = 4;

    function updateProgress() {
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

        document.querySelectorAll('.form-step').forEach((step, index) => {
            step.classList.toggle('active', (index + 1) === currentStep);
        });
    }

    function showLoader() {
        document.getElementById('quotePlaceholder').style.display = 'none';
        document.getElementById('apiResponse').style.display = 'none';
        document.getElementById('apiResponse').classList.add('d-none');
        document.getElementById('quoteLoader').classList.remove('d-none');
        
        // Also show spinner on submit button
        document.getElementById('btnSpinner').classList.remove('d-none');
        document.getElementById('submitQuoteBtn').disabled = true;
    }

    function hideLoader() {
        document.getElementById('quoteLoader').classList.add('d-none');
        document.getElementById('btnSpinner').classList.add('d-none');
        document.getElementById('submitQuoteBtn').disabled = false;
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
                // Show loader only for step 4
                showLoader();
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
            if (step === 4) {
                hideLoader(); // Hide loader when response comes
            }
            
            if (data.success) {
                if (step === 1 && data.shipment_id) {
                    document.getElementById('shipment_id').value = data.shipment_id;
                }
                
                if (step === 4) {
                    showApiResponse(data.api_response, data.error_details);
                    document.getElementById('quotePlaceholder').style.display = 'none';
                } else {
                    currentStep = data.next_step;
                    updateProgress();
                }
            } else {
                alert(data.message || 'An error occurred. Please check your input.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (step === 4) {
                hideLoader(); // Hide loader on error too
            }
            alert('An error occurred. Please try again.');
        });
    }

    function previousStep(step) {
        currentStep = step - 1;
        updateProgress();
    }

    function showApiResponse(response, errorDetails) {
        const apiResponseDiv = document.getElementById('apiResponse');
        
        if (!response || errorDetails) {
            let errorMessage = 'No response received from shipping API.';
            if (errorDetails) {
                try {
                    const errorData = JSON.parse(errorDetails.split(': ')[1] || '{}');
                    if (errorData.content && errorData.content.length > 0) {
                        errorMessage = errorData.content.map(err => err.message).join('; ');
                    } else {
                        errorMessage = errorDetails;
                    }
                } catch (e) {
                    errorMessage = errorDetails || 'Unknown error occurred.';
                }
            }
            apiResponseDiv.innerHTML = `
                <div class="alert alert-danger">
                    <h5><i class="fas fa-exclamation-triangle me-2"></i>API Error</h5>
                    <p>${errorMessage}</p>
                </div>
            `;
            apiResponseDiv.classList.remove('d-none');
            apiResponseDiv.style.display = 'block';
            return;
        }

        const quoteData = response.content;
        let carriersHtml = '';
        
        if (quoteData.carrierPrices && quoteData.carrierPrices.length > 0) {
            quoteData.carrierPrices.forEach((carrier, index) => {
                carriersHtml += `
                    <div class="carrier-card mb-3 p-3 border rounded ${carrier.isPreferred ? 'border-warning bg-light-warning' : ''}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">${carrier.carrier}</h6>
                                <small class="text-muted">SCAC: ${carrier.scac}</small>
                                ${carrier.isPreferred ? '<span class="badge bg-warning ms-2">Preferred</span>' : ''}
                                ${carrier.isCarrierOfTheYear ? '<span class="badge bg-info ms-1">Carrier of the Year</span>' : ''}
                            </div>
                            <div class="text-end">
                                <strong class="text-success h5">$${carrier.customerRate?.toFixed(2) || '0.00'}</strong>
                                <div><small class="text-muted">${carrier.transitDays || 'N/A'} transit days</small></div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <small><strong>Service:</strong> ${carrier.serviceLevelDescription || carrier.serviceLevel || 'Standard'}</small>
                        </div>
                        <div class="mt-1">
                            <small><strong>Type:</strong> ${carrier.serviceType || 'Direct'}</small>
                        </div>
                        <div class="mt-1">
                            <small><strong>Liability:</strong> $${carrier.maxLiabilityNew?.toFixed(2) || '0.00'} (New) / $${carrier.maxLiabilityUsed?.toFixed(2) || '0.00'} (Used)</small>
                        </div>
                        ${carrier.priceCharges && carrier.priceCharges.length > 0 ? `
                            <div class="mt-2">
                                <small><strong>Charges Breakdown:</strong></small>
                                <div class="small mt-1">
                                    ${carrier.priceCharges.map(charge => 
                                        `<div>${charge.description}: $${charge.amount?.toFixed(2) || '0.00'}</div>`
                                    ).join('')}
                                </div>
                            </div>
                        ` : ''}
                    </div>
                `;
            });
        } else {
            carriersHtml = '<div class="alert alert-warning">No carrier quotes available</div>';
        }

        let commoditiesHtml = '';
        if (quoteData.quoteCommodities && quoteData.quoteCommodities.length > 0) {
            quoteData.quoteCommodities.forEach((commodity, index) => {
                commoditiesHtml += `
                    <div class="commodity-info mb-3 p-3 border rounded">
                        <h6>Commodity ${index + 1}</h6>
                        <div class="row">
                            <div class="col-6"><small><strong>Description:</strong> ${commodity.description}</small></div>
                            <div class="col-6"><small><strong>Quantity:</strong> ${commodity.quantity}</small></div>
                            <div class="col-6"><small><strong>Weight:</strong> ${commodity.weight} lbs</small></div>
                            <div class="col-6"><small><strong>Dimensions:</strong> ${commodity.dimensionLength}" × ${commodity.dimensionWidth}" × ${commodity.dimensionHeight}"</small></div>
                            <div class="col-6"><small><strong>Freight Class:</strong> ${commodity.freightClassCode}</small></div>
                            <div class="col-6"><small><strong>Unit Type:</strong> ${commodity.unitTypeCode}</small></div>
                        </div>
                    </div>
                `;
            });
        }

        apiResponseDiv.innerHTML = `
            <div class="alert alert-success">
                <h5><i class="fas fa-check-circle me-2"></i>Quote Generated Successfully!</h5>
                <p class="mb-0">Quote ID: ${quoteData.quoteId}</p>
            </div>
            
            <div class="quote-summary mb-4">
                <h6 class="border-bottom pb-2">Quote Summary</h6>
                <div class="row">
                    <div class="col-6"><strong>Quote ID:</strong></div>
                    <div class="col-6">${quoteData.quoteId}</div>
                </div>
                <div class="row mt-2">
                    <div class="col-6"><strong>Shipment Date:</strong></div>
                    <div class="col-6">${new Date(quoteData.shipmentDate).toLocaleDateString()}</div>
                </div>
                <div class="row mt-2">
                    <div class="col-6"><strong>Created Date:</strong></div>
                    <div class="col-6">${new Date(quoteData.createdDate).toLocaleDateString()}</div>
                </div>
                ${quoteData.expirationDate ? `
                <div class="row mt-2">
                    <div class="col-6"><strong>Expiration Date:</strong></div>
                    <div class="col-6">${new Date(quoteData.expirationDate).toLocaleDateString()}</div>
                </div>
                ` : ''}
            </div>

            ${commoditiesHtml ? `
            <div class="commodities-info mb-4">
                <h6 class="border-bottom pb-2">Commodities</h6>
                ${commoditiesHtml}
            </div>
            ` : ''}

            <div class="carrier-quotes">
                <h6 class="border-bottom pb-2">Carrier Quotes</h6>
                ${carriersHtml}
            </div>
        `;
        apiResponseDiv.classList.remove('d-none');
        apiResponseDiv.style.display = 'block';
        document.getElementById('quotePlaceholder').style.display = 'none';
    }

    // Initialize date input to tomorrow (matching Postman)
    document.getElementById('shipment_date').valueAsDate = new Date(Date.now() + 86400000);
</script>
@endsection