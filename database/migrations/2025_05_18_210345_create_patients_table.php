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
      Schema::create('patients', function (Blueprint $table) {
    $table->id();
    $table->string('name'); 
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->date('date_of_birth')->nullable();
    $table->string('gender')->nullable();
    $table->string('contact_number')->nullable();
    $table->text('medical_history')->nullable();
    $table->foreignId('assigned_doctor_id')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
