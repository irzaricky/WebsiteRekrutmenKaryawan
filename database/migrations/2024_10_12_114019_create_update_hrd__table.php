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
        Schema::table('hrd', function (Blueprint $table) {
            $table->enum('role', ['admin'])->change(); // Hanya menyisakan role 'admin'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hrd', function (Blueprint $table) {
            $table->enum('role', ['admin', 'staff'])->change(); // Mengembalikan ke semula jika perlu
        });
    }
};
