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
      
      Schema::create('medication_doses', function (Blueprint $table) {
    $table->id();
    $table->foreignId('prescription_id')->constrained('prescriptions')->onDelete('cascade');
    $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade'); // Redundant but useful for direct access
    $table->dateTime('scheduled_at');
    $table->dateTime('taken_at')->nullable();
    $table->enum('status', ['scheduled', 'taken', 'missed', 'skipped'])->default('scheduled');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medication_doses');
    }
};
