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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            // Links to the patient the alert is about
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            // Optional: caregiver to whom the alert is directed/visible
            $table->foreignId('caregiver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('type'); // e.g., 'Missed Medication', 'Emergency', 'System'
            $table->text('message');
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('resolved_at')->nullable(); // When the alert was resolved
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};