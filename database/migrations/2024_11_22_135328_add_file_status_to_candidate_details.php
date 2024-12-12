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
            $table->string('ijazah_smp_path')->nullable();
            $table->string('ijazah_sma_path')->nullable();
            $table->string('ijazah_d3_path')->nullable();
            $table->string('ijazah_s1_path')->nullable();
            $table->string('ijazah_s2_path')->nullable();
            $table->string('ijazah_s3_path')->nullable();
            $table->enum('ijazah_smp_status', ['pending', 'accepted', 'rejected'])->nullable();
            $table->enum('ijazah_sma_status', ['pending', 'accepted', 'rejected'])->nullable();
            $table->enum('ijazah_d3_status', ['pending', 'accepted', 'rejected'])->nullable();
            $table->enum('ijazah_s1_status', ['pending', 'accepted', 'rejected'])->nullable();
            $table->enum('ijazah_s2_status', ['pending', 'accepted', 'rejected'])->nullable();
            $table->enum('ijazah_s3_status', ['pending', 'accepted', 'rejected'])->nullable();
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
            $table->dropColumn([
                'ijazah_smp_path',
                'ijazah_sma_path',
                'ijazah_d3_path',
                'ijazah_s1_path',
                'ijazah_s2_path',
                'ijazah_s3_path'
            ]);
            $table->dropColumn([
                'ijazah_smp_status',
                'ijazah_sma_status',
                'ijazah_d3_status',
                'ijazah_s1_status',
                'ijazah_s2_status',
                'ijazah_s3_status'
            ]);
        });
    }
};
