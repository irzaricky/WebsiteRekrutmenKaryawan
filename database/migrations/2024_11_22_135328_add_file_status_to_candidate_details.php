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
            $table->enum('photo_status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->enum('cv_status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->enum('certificate_status', ['pending', 'accepted', 'rejected'])->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidate_details', function (Blueprint $table) {
            $table->dropColumn([
                'photo_confirmed',
                'photo_confirmed_at',
                'cv_confirmed',
                'cv_confirmed_at',
                'certificate_confirmed',
                'certificate_confirmed_at'
            ]);
        });
    }
};
