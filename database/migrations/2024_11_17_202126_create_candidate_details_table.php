<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('candidate_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nik', 16)->unique();
            $table->string('full_name');
            $table->text('address');
            $table->date('birth_date');
            $table->string('photo_path')->nullable();
            $table->string('cv_path')->nullable();
            $table->string('certificate_path')->nullable();
            $table->enum('education_level', ['SMA', 'D3', 'S1', 'S2', 'S3']);
            $table->string('major');
            $table->string('institution');
            $table->year('graduation_year');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('candidate_details');
    }
};