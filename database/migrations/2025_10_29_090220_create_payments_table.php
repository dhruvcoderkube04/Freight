<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Selected carrier information
            $table->string('carrier_name'); // e.g., "Estes Express LTL"
            $table->string('carrier_scac'); // e.g., "EXLA"
            $table->boolean('is_preferred')->default(false);
            $table->boolean('is_carrier_of_the_year')->default(false);
            $table->decimal('customer_rate', 10, 2); // e.g., 550.45
            $table->integer('transit_days')->nullable(); // e.g., 1
            $table->string('service_level'); // e.g., "Volume and Truckload Basic SMC3"
            $table->string('service_type')->nullable(); // e.g., "Direct"
            $table->decimal('max_liability_new', 10, 2)->nullable();
            $table->decimal('max_liability_used', 10, 2)->nullable();
            $table->json('price_charges')->nullable();
            
            // Payment information
            $table->string('payment_status')->default('pending');
            $table->string('stripe_payment_intent_id')->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->string('stripe_charge_id')->nullable();
            
            // Admin approval
            $table->boolean('requires_approval')->default(false);
            $table->text('approval_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            
            // Payment details (simplified - using user info)
            $table->string('currency')->default('usd');
            $table->decimal('amount', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('payment_status');
            $table->index('stripe_payment_intent_id');
            $table->index(['user_id', 'payment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};