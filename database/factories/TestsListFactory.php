<?php

namespace Database\Factories;

use App\Models\TestsList;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestsListFactory extends Factory
{
    protected $model = TestsList::class;

    public function definition()
    {
        // Struktur data untuk berbagai jenis tes
        $tests = [
            ['name' => 'TIU', 'description' => 'Tes Intelejensi Umum'],
            ['name' => 'TWK', 'description' => 'Tes Wawasan Kebangsaan'],
            ['name' => 'TKB', 'description' => 'Tes Kemampuan Bidang'],
            ['name' => 'TW', 'description' => 'Tes Wawancara'],
        ];

        // Memilih secara acak dari daftar tes di atas
        $testData = $this->faker->randomElement($tests);

        return [
            'name' => $testData['name'],
            'description' => $testData['description'],
        ];
    }
}
