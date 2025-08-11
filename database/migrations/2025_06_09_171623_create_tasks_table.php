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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            // Links to the patient for whom the task is
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            // Links to the caregiver assigned (User with 'caregiver' role)
            $table->foreignId('caregiver_id')->constrained('users')->onDelete('cascade');
            $table->string('description'); // e.g., 'Blood pressure check', 'Insulin injection'
            $table->timestamp('due_at'); // When the task is due
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable(); // When the task was actually completed
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};