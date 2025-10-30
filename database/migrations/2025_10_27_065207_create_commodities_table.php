<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commodities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->string('unit_type');
            $table->string('freight_class_code');
            $table->decimal('weight', 8, 2);
            $table->integer('length');
            $table->integer('width');
            $table->integer('height');
            $table->json('additional_services')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commodities');
    }
};