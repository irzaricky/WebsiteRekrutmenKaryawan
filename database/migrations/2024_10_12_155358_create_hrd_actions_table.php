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
        Schema::create('hrd_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('hrd_id')->constrained('users')->onDelete('cascade');
            $table->enum('action_type', ['create', 'update', 'update_file_status']);
            $table->foreignId('test_result_id')->nullable()->constrained('test_results')->onDelete('cascade');
            $table->text('details')->nullable();  // optional details for actions
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hrd_actions');
    }
};
