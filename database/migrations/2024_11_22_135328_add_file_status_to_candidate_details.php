<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('candidate_details', function (Blueprint $table) {
            $table->enum('photo_status', ['pending', 'accepted', 'rejected'])->nullable();
            $table->enum('cv_status', ['pending', 'accepted', 'rejected'])->nullable();
            $table->enum('certificate_status', ['pending', 'accepted', 'rejected'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidate_details', function (Blueprint $table) {
            // Drop only the columns we added in up()
            $table->dropColumn([
                'photo_status',
                'cv_status',
                'certificate_status'
            ]);
        });
    }
};
