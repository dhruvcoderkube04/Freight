<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();

            // Basic Info
            $table->string('site_name')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->text('project_description')->nullable();

            // Social Links
            $table->string('facebook_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('tiktok_url')->nullable();
            $table->string('wechat_url')->nullable();

            // Business Hours
            $table->string('business_hours_preset')->nullable();
            $table->string('business_hours_custom')->nullable();

            // Addresses
            $table->text('main_address')->nullable();
            $table->text('alternate_address')->nullable();

            // Contact Numbers
            $table->string('main_phone')->nullable();
            $table->string('alternate_phone')->nullable();

            // Emails
            $table->string('general_email')->nullable();
            $table->string('support_email')->nullable();

            // Location Embed
            $table->text('location_iframe')->nullable();

            // Quote Markups
            $table->decimal('quote_markup', 8, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};