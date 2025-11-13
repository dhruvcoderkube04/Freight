@extends('admin.layouts.base')

@section('content')
<div id="kt_app_content_container" class="app-container">
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <h3 class="fw-bold">Website CMS Settings</h3>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Site Name -->
                <div class="mb-5">
                    <label class="form-label required">Site Name</label>
                    <input type="text" name="site_name" class="form-control"
                        value="{{ old('site_name', $settings->site_name ?? '') }}" required>
                </div>

                <!-- Quote Markups -->
                <div class="row mb-5">
                    <div class="col-md-6">
                        <label>Quote Markup (%)</label>
                        <input type="number" step="0.01" name="quote_markup" class="form-control"
                            value="{{ old('quote_markup', $settings->quote_markup ?? '') }}">
                    </div>
                </div>

                <!-- Logo & Favicon -->
                <div class="row mb-5">
                    <div class="col-md-6">
                        <label>Logo</label>
                        @if ($settings?->logo)
                            <div class="mb-2"><img src="{{ asset('storage/' . $settings->logo) }}" width="150"
                                    class="img-thumbnail"></div>
                        @endif
                        <input type="file" name="logo" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>Favicon</label>
                        @if ($settings?->favicon)
                            <div class="mb-2"><img src="{{ asset('storage/' . $settings->favicon) }}" width="64"></div>
                        @endif
                        <input type="file" name="favicon" class="form-control">
                    </div>
                </div>

                <!-- Social Links -->
                <h5>Social Media</h5>
                <div class="row mb-5">
                    @foreach (['facebook_url', 'twitter_url', 'instagram_url', 'tiktok_url', 'wechat_url'] as $field)
                        <div class="col-md-6 mb-3">
                            <label>{{ ucfirst(str_replace('_url', '', $field)) }}</label>
                            <input type="url" name="{{ $field }}" class="form-control"
                                value="{{ old($field, $settings->{$field} ?? '') }}">
                        </div>
                    @endforeach
                </div>

                <!-- Business Hours -->
                <h5>Business Hours</h5>
                <div class="row mb-5">
                    <div class="col-md-6">
                        <select name="business_hours_preset" class="form-select"
                            onchange="this.value==='custom' ? document.getElementById('custom_hours').style.display='block' : document.getElementById('custom_hours').style.display='none'">
                            <option value="">Select</option>
                            <option value="9 to 6"
                                {{ old('business_hours_preset', $settings->business_hours_preset ?? '') === '9 to 6' ? 'selected' : '' }}>
                                9:00 AM - 6:00 PM</option>
                            <option value="10 to 7"
                                {{ old('business_hours_preset', $settings->business_hours_preset ?? '') === '10 to 7' ? 'selected' : '' }}>
                                10:00 AM - 7:00 PM</option>
                            <option value="11 to 8"
                                {{ old('business_hours_preset', $settings->business_hours_preset ?? '') === '11 to 8' ? 'selected' : '' }}>
                                11:00 AM - 8:00 PM</option>
                            <option value="custom"
                                {{ old('business_hours_preset', $settings->business_hours_preset ?? '') === 'Custom' ? 'selected' : '' }}>
                                Custom</option>
                        </select>
                    </div>
                    <div class="col-md-6" id="custom_hours"
                        style="display: {{ old('business_hours_preset', $settings->business_hours_preset ?? '') === 'Custom' ? 'block' : 'none' }}">
                        <input type="text" name="business_hours_custom" class="form-control"
                            value="{{ old('business_hours_custom', $settings->business_hours_custom ?? '') }}"
                            placeholder="e.g. Mon-Fri: 9AM-5PM">
                    </div>
                </div>

                <!-- Addresses, Phones, Emails, Map -->
                <h5>Contact & Location</h5>
                <div class="row mb-5">
                    <div class="col-12 mb-3">
                        <textarea name="main_address" rows="2" class="form-control" placeholder="Main Address">{{ old('main_address', $settings->main_address ?? '') }}</textarea>
                    </div>
                    <div class="col-12 mb-3">
                        <textarea name="alternate_address" rows="2" class="form-control" placeholder="Alternate Address">{{ old('alternate_address', $settings->alternate_address ?? '') }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3"><input type="text" name="main_phone" class="form-control"
                            placeholder="Main Phone" value="{{ old('main_phone', $settings->main_phone ?? '') }}"></div>
                    <div class="col-md-6 mb-3"><input type="text" name="alternate_phone" class="form-control"
                            placeholder="Alternate Phone"
                            value="{{ old('alternate_phone', $settings->alternate_phone ?? '') }}"></div>
                    <div class="col-md-6 mb-3"><input type="email" name="general_email" class="form-control"
                            placeholder="General Email" value="{{ old('general_email', $settings->general_email ?? '') }}">
                    </div>
                    <div class="col-md-6 mb-3"><input type="email" name="support_email" class="form-control"
                            placeholder="Support Email" value="{{ old('support_email', $settings->support_email ?? '') }}">
                    </div>
                </div>

                <h5>Google Maps Embed</h5>
                <textarea name="location_iframe" rows="4" class="form-control" placeholder="<iframe src=...></iframe>">{{ old('location_iframe', $settings->location_iframe ?? '') }}</textarea>

                <div class="text-end mt-10">
                    <button type="submit" class="btn btn-primary btn-lg">Save All Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>
    <script>
        document.querySelector('[name=business_hours_preset]').addEventListener('change', function() {
            document.getElementById('custom_hours').style.display = this.value === 'custom' ? 'block' : 'none';
        });
    </script>
@endsection
