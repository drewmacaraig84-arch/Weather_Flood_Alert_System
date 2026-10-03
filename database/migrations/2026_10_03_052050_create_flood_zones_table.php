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
        Schema::create('flood_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. Rizal Avenue, Lumangbayan
            $table->string('barangay')->nullable();
            $table->text('description')->nullable();
            $table->string('risk_level')->default('MODERATE'); // LOW, MODERATE, HIGH
            $table->integer('water_level_cm')->nullable();
            $table->string('passable_status')->default('Passable to all'); // Passable to all, Light vehicles not advised, Impassable
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->integer('radius_meters')->default(150);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flood_zones');
    }
};
