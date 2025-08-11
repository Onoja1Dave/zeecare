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
        Schema::table('appointments', function (Blueprint $table) {
            // Add caregiver_id, linking to users table (as caregiver is a user role)
            // It's nullable because an appointment might not always have a directly assigned caregiver
            $table->foreignId('caregiver_id')->nullable()->constrained('users')->after('patient_id')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('caregiver_id'); // Drops the foreign key constraint
            $table->dropColumn('caregiver_id'); // Drops the column itself
        });
    }
};