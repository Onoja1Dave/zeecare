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
        Schema::create('caregiver_patient_assignments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('caregiver_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
    $table->timestamps();

    $table->unique(['caregiver_id', 'patient_id']); 
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caregiver_patient_assignments');
    }
};
