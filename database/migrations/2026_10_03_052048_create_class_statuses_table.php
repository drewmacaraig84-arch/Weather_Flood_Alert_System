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
        Schema::create('class_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('NORMAL'); // NORMAL, SUSPENDED, ADVISORY
            $table->string('level_scope')->default('All Levels'); // All Levels, JHS, SHS, College
            $table->string('reason')->nullable();
            $table->text('announcement_text')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('effective_date')->nullable();
            $table->string('updated_by')->default('DWCC Administration');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_statuses');
    }
};
