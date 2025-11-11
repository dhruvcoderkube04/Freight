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
            $table->string('site_name')->default('My Website');
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->text('project_description')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->decimal('quote_markup_amount', 8, 2)->default(0.00); // e.g., 15.50 %
            $table->decimal('quote_markup_percent', 5, 2)->default(0.00); // optional: 15.00%
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        // Insert default row
        DB::table('site_settings')->insert([
            'site_name' => 'QuoteApp',
            'quote_markup_amount' => 10.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};