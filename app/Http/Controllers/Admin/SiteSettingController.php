<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteSettingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::first(); // May be null first time
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'favicon' => 'nullable|image|mimes:png,ico,jpg,jpeg,webp|max:512',
            'quote_markup' => 'nullable|numeric',

            'facebook_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'tiktok_url' => 'nullable|url',
            'wechat_url' => 'nullable|url',

            'business_hours_preset' => 'nullable|string',
            'business_hours_custom' => 'nullable|string',

            'main_address' => 'nullable|string',
            'alternate_address' => 'nullable|string',
            'main_phone' => 'nullable|string',
            'alternate_phone' => 'nullable|string',
            'general_email' => 'nullable|email',
            'support_email' => 'nullable|email',
            'location_iframe' => 'nullable|string',
        ]);

        // Create or update the single row
        $settings = SiteSetting::firstOrCreate(['id' => 1]); // Ensures only one row

        $data = $request->except(['logo', 'favicon']);

        // Handle business hours
        if ($request->business_hours_preset === 'custom') {
            $data['business_hours_preset'] = 'Custom';
        }

        // Logo upload
        if ($request->hasFile('logo')) {
            if ($settings->logo && Storage::exists('public/' . $settings->logo)) {
                Storage::delete('public/' . $settings->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        // Favicon upload
        if ($request->hasFile('favicon')) {
            if ($settings->favicon && Storage::exists('public/' . $settings->favicon)) {
                Storage::delete('public/' . $settings->favicon);
            }
            $data['favicon'] = $request->file('favicon')->store('favicons', 'public');
        }

        $settings->update($data);

        return back()->with('success', 'All settings saved successfully!');
    }
}