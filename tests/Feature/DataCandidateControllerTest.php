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
    protected $candidate;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'HRD']);
        $this->test = TestsList::factory()->create();
        $this->candidate = User::factory()->create(['role' => 'Candidate']);
    }

    // Test unauthorized access
    public function test_non_hrd_tidak_dapat_mengakses_data_candidate()
    {
        $user = User::factory()->create(['role' => 'Candidate']);
        $response = $this->actingAs($user)
            ->from('/')
            ->get(route('dashboard.data-candidate'));

        $response->assertRedirect('/'); // Should redirect to home
    }

    // Test search functionality
    public function test_hrd_dapat_mencari_candidate_berdasarkan_nama()
    {
        $candidate1 = User::factory()->create([
            'name' => 'John Doe',
            'role' => 'Candidate'
        ]);
        $candidate2 = User::factory()->create([
            'name' => 'Jane Smith',
            'role' => 'Candidate'
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard.data-candidate', ['search' => 'John']));

        $response->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->has('candidates.data', 1)
                    ->where('candidates.data.0.name', 'John Doe')
            );
    }

    // Test empty scores filter
    public function test_hrd_dapat_memfilter_candidate_dengan_nilai_kosong()
    {
        $testResult = TestResult::factory()->create([
            'user_id' => $this->candidate->id,
            'test_id' => $this->test->id,
            'score' => 0
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard.data-candidate', ['empty_scores' => true]));

        $response->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->has('candidates.data', 1)
                    ->where('candidates.data.0.id', $this->candidate->id)
            );
    }

    // Test pagination
    public function test_data_candidate_terpaginasi_dengan_benar()
    {
        $candidates = User::factory()->count(10)->create(['role' => 'Candidate']);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard.data-candidate'));

        $response->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->has('candidates.data', 6) // Default pagination is 6
                    ->has('candidates.links')
            );
    }

    // Test validation rules for score updates
    public function test_hrd_tidak_dapat_mengupdate_score_dengan_nilai_negatif()
    {
        $testResult = TestResult::factory()->create();

        $response = $this->actingAs($this->user)
            ->put(route('dashboard.data-candidate-put', $testResult->user_id), [
                'test_results' => [
                    $this->test->id => -10
                ]
            ]);

        $response->assertSessionHasErrors(['test_results.*']);
    }


    //Test untuk membaca data Test Result
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

    //Test untuk memperbarui nilai Test Result kandidat

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

    public function test_hrd_tidak_dapat_mengupdate_data_hasil_tes_candidate_yang_invalid()
    {
        $testResult = TestResult::factory()->create([
            'user_id' => $this->user->id,
            'test_id' => $this->test->id,
            'score' => 50,
        ]);

        // Request data update dengan data invalid
        $response = $this->actingAs($this->user)->put(route('dashboard.data-candidate-put', $this->user->id), [
            'test_results' => [
                $this->test->id => -50,  // Nilai invalid untuk score
            ],
        ]);

        $response->assertSessionHasErrors(['test_results.*']);
        $this->assertDatabaseHas('test_results', [
            'user_id' => $this->user->id,
            'test_id' => $this->test->id,
            'score' => 50,  // Pastikan nilai tidak berubah
        ]);
    }

    public function test_hrd_tidak_dapat_mengupdate_data_hasil_tes_candidate_menjadi_kosong()
    {
        $testResult = TestResult::factory()->create([
            'user_id' => $this->user->id,
            'test_id' => $this->test->id,
            'score' => 50,
        ]);

        // Request data update dengan data invalid
        $response = $this->actingAs($this->user)->put(route('dashboard.data-candidate-put', $this->user->id), [
            'test_results' => [
                $this->test->id => null,  // Nilai invalid untuk score
            ],
        ]);

        $response->assertSessionHasErrors(['test_results.*']);
        $this->assertDatabaseHas('test_results', [
            'user_id' => $this->user->id,
            'test_id' => $this->test->id,
            'score' => 50,  // Pastikan nilai tidak berubah
        ]);
    }
}
