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
        Schema::table('caregiver_applications', function (Blueprint $table) {
            // Add new columns
            $table->string('email')->unique()->after('name'); // Add email after name
            $table->string('registration_token')->nullable()->unique()->after('status');
            $table->timestamp('token_expires_at')->nullable()->after('registration_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('caregiver_applications', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn('token_expires_at');
            $table->dropUnique(['registration_token']); // Drop unique constraint before dropping column
            $table->dropColumn('registration_token');
            $table->dropUnique(['email']); // Drop unique constraint before dropping column
            $table->dropColumn('email');
        });
    }
};