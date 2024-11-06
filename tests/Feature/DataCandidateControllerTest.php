<?php

namespace Tests\Feature;

use App\Models\TestsList;
use App\Models\User;
use App\Models\TestResult;
use Database\Factories\TestResultFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataCandidateControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $test;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat user dengan role 'Candidate' untuk keperluan test
        $this->user = User::factory()->create([
            'role' => 'HRD',
        ]);

        // Buat sebuah test untuk keperluan test
        $this->test = TestsList::factory()->create();
    }

    /**
     * Test untuk membaca data Test Result
     */
    public function test_hrd_dapat_membaca_data_hasil_test_candidate()
    {
        // Buat beberapa TestResult untuk kandidat
        $testResults = TestResult::factory()->count(5)->create([
            'user_id' => $this->user->id,
        ]);

        // Lakukan autentikasi sebagai admin atau user yang memiliki akses yang tepat
        $response = $this->actingAs($this->user)->get(route('dashboard.data-candidate'));

        // Pastikan status respons 200 (berhasil)
        $response->assertStatus(status: 200);

        // Periksa apakah view memiliki data kandidat
        // $response->assertViewHas(key: 'candidates');
    }

    /**
     * Test untuk memperbarui nilai Test Result kandidat
     */
    public function test_hrd_dapat_mengupdate_data_hasil_tes_candidate()
    {
        $testResult = TestResult::factory()->create([
            'user_id' => $this->user->id,
            'test_id' => $this->test->id,
            'score' => 50,
        ]);

        // Request data update
        $response = $this->actingAs($this->user)->put(route('dashboard.data-candidate-put', $this->user->id), [
            'test_results' => [
                $this->test->id => 80,  // Nilai baru untuk score
            ],
        ]);

        $response->assertRedirect(route('dashboard.data-candidate'));
        $response->assertSessionHas('message', 'Nilai kandidat berhasil diperbarui');

        $this->assertDatabaseHas('test_results', [
            'user_id' => $this->user->id,
            'test_id' => $this->test->id,
            'score' => 80,
        ]);
    }

    /**
     * Test untuk menghapus data Test Result
     */
    // public function test_can_delete_candidate_test_result()
    // {
    //     $testResult = TestResult::factory()->create([
    //         'user_id' => $this->user->id,
    //         'test_id' => $this->test->id,
    //         'score' => 75,
    //     ]);

    //     // Pastikan data ada di database sebelum penghapusan
    //     $this->assertDatabaseHas('test_results', [
    //         'user_id' => $this->user->id,
    //         'test_id' => $this->test->id,
    //     ]);

    //     // Hapus Test Result
    //     $testResult->delete();

    //     // Pastikan data sudah terhapus
    //     $this->assertDatabaseMissing('test_results', [
    //         'user_id' => $this->user->id,
    //         'test_id' => $this->test->id,
    //     ]);
    // }
}
