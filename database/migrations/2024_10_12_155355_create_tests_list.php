<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('tests_list', function (Blueprint $table) {
            $table->id();
            $table->enum('name', ['TIU', 'TWK', 'TKB', 'TW']);
            $table->text('description');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tests_list');
    }
};