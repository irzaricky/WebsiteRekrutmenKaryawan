<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TestsList;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tests = [
            ['name' => 'TIU', 'description' => 'Tes Intelejensi Umum'],
            ['name' => 'TWK', 'description' => 'Tes Wawasan Kebangsaan'],
            ['name' => 'TKB', 'description' => 'Tes Kemampuan Bidang'],
            ['name' => 'TW', 'description' => 'Tes Wawancara'],
        ];

        foreach ($tests as $test) {
            TestsList::create($test);
        }
    }
}
