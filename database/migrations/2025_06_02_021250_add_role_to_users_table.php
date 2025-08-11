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
        Schema::table('users', function (Blueprint $table) {
            // Add the 'role' column as a string, after the 'email' column (optional positioning)
            // Set a default value, e.g., 'patient', for existing users or new users if not specified
            $table->string('role')->default('patient')->after('email');
            // Alternatively, if you want it to be nullable and set manually:
            // $table->string('role')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the column if rolling back the migration
            $table->dropColumn('role');
        });
    }
};