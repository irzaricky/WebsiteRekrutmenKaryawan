<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreatePositionTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('position_types', function (Blueprint $table) {
            $table->id();
            $table->string('position')->nullable();
            $table->timestamps();
        });

        DB::table('position_types')->insert([
            ['position' => 'CEO'],
            ['position' => 'CFO'],
            ['position' => 'Project Manager'],
            ['position' => 'Web Designer'],
            ['position' => 'Web Developer'],
            ['position' => 'Android Developer'],
            ['position' => 'IOS Developer'],
            ['position' => 'Team Leader'],
            ['position' => 'React Developer'],
            ['position' => 'Angular Developer'],
            ['position' => 'VueJs Developer'],
            ['position' => 'NodeJS Developer'],
            ['position' => 'ASP.Net Developer'],
            ['position' => 'UI / UX Designer'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('position_types');
    }
}
