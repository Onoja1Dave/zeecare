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
        Schema::table('medication_doses', function (Blueprint $table) {
            // Add the 'is_taken' column as a boolean with a default of false (or 0)
            $table->boolean('is_taken')->default(false)->after('scheduled_at');
            // 'after('scheduled_at')' is optional, but helps with column order.
            // If you don't care about order, just use $table->boolean('is_taken')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medication_doses', function (Blueprint $table) {
            // Drop the column if rolling back the migration
            $table->dropColumn('is_taken');
        });
    }
};