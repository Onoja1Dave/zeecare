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
        Schema::create('prescriptions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
    $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
    $table->string('drug_name');
    $table->string('dosage')->nullable();
    $table->string('frequency')->nullable();
    $table->string('duration')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
