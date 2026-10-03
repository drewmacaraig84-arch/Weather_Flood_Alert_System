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
        Schema::create('flood_reports', function (Blueprint $table) {
            $table->id();
            $table->string('reporter_name');
            $table->string('role')->default('Student'); // Student, Faculty, Staff, Resident
            $table->string('contact_number')->nullable();
            $table->string('location_name');
            $table->string('severity')->default('MODERATE'); // LOW, MODERATE, HIGH
            $table->text('description')->nullable();
            $table->string('status')->default('Reported'); // Reported, Verified, Resolved
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flood_reports');
    }
};
