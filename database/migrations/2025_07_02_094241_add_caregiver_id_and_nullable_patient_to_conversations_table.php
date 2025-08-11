<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            // Make patient_id nullable (important if you have existing conversations that are patient-doctor)
            // Ensure no data loss if columns already exist and contain values
            $table->foreignId('patient_id')->nullable()->change();

            // Add caregiver_id
            $table->foreignId('caregiver_id')->nullable()->constrained('users')->onDelete('cascade')->after('doctor_id');

            // Adjust unique constraint if necessary:
            // The current unique(['patient_id', 'doctor_id']) won't work well if patient_id can be null
            // Or if you want a doctor-caregiver unique pair.
            // You might need to drop the old unique constraint and add new ones depending on combinations.
            // For now, let's just add the column and make nullable. We'll handle unique constraints if issues arise.
        });
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['caregiver_id']); // Drop foreign key first
            $table->dropColumn('caregiver_id');
            // If you had made patient_id nullable, you might want to revert it
            // $table->foreignId('patient_id')->nullable(false)->change();
        });
    }
};
