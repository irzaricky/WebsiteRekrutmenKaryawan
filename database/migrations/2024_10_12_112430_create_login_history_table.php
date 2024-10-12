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
        Schema::create('login_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('candidates')->onDelete('cascade')->nullable(); // Untuk calon karyawan
            $table->foreignId('hrd_id')->constrained('hrd')->onDelete('cascade')->nullable(); // Untuk HRD
            $table->timestamp('login_time');
            $table->timestamp('logout_time')->nullable(); // Dapat bernilai null jika masih login
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_history');
    }
};
