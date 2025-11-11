<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'site_name', 'logo', 'favicon', 'project_description',
        'facebook_url', 'twitter_url', 'instagram_url', 'tiktok_url', 'wechat_url',
        'business_hours_preset', 'business_hours_custom',
        'main_address', 'alternate_address',
        'main_phone', 'alternate_phone',
        'general_email', 'support_email',
        'location_iframe','quote_markup',
    ];

    public static function getSettings()
    {
        return self::first();
    }
}