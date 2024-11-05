<?php

namespace Database\Factories;

use App\Models\TestResult;
use App\Models\User;
use App\Models\TestsList;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestResultFactory extends Factory
{
    protected $model = TestResult::class;

    public function definition()
    {
        // Buat beberapa tipe nilai untuk berbagai jenis tes
        $testScores = [
            ['name' => 'TIU', 'score' => $this->faker->numberBetween(90, 120)],
            ['name' => 'TWK', 'score' => $this->faker->numberBetween(70, 100)],
            ['name' => 'TKB', 'score' => $this->faker->numberBetween(150, 200)],
            ['name' => 'TW', 'score' => $this->faker->numberBetween(250, 300)],
        ];

        // Pilih acak satu test
        $testData = $this->faker->randomElement($testScores);

        return [
            'user_id' => User::factory(), // Buat user baru atau sesuaikan dengan ID yang sudah ada
            'test_id' => TestsList::factory()->state(['name' => $testData['name']]),
            'score' => $testData['score'],
        ];
    }
}
