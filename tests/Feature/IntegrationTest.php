<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\CandidateDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class IntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected $candidate;
    protected $hrd;
    protected $candidateDetail;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        // Create users
        $this->candidate = User::factory()->create(['role' => 'Candidate']);
        $this->hrd = User::factory()->create(['role' => 'HRD']);

        // Create candidate profile
        $this->candidateDetail = CandidateDetail::factory()->create([
            'user_id' => $this->candidate->id,
            'nik' => '1234567890123456'
        ]);
    }


    public function test_when_candidate_uploads_file_hrd_can_see_it()
    {
        // 1. Candidate uploads files
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->actingAs($this->candidate)
            ->post(route('candidate.files.update'), [
                'cv' => $file
            ]);

        $response->assertSessionHasNoErrors();

        // 2. HRD checks pending files
        $response = $this->actingAs($this->hrd)
            ->get(route('dashboard.pending-files'));

        $response->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->has('candidates.data', 1)
                    ->where('candidates.data.0.candidate_detail.cv_status', 'pending')
            );
    }


    public function test_when_hrd_reviews_files_candidate_can_see_status_changes()
    {
        // 1. Setup initial file upload
        $this->candidateDetail->update([
            'cv_path' => 'path/to/cv.pdf',
            'cv_status' => 'pending'
        ]);

        // 2. HRD reviews and updates status
        $response = $this->actingAs($this->hrd)
            ->post(route('dashboard.update-file-status'), [
                'candidate_id' => $this->candidate->id,
                'file_type' => 'cv',
                'status' => 'accepted'
            ]);

        $response->assertSuccessful();

        // 3. Candidate checks file status
        $response = $this->actingAs($this->candidate)
            ->get(route('candidate.file-status'));

        $response->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->where('candidateDetail.cv_status', 'accepted')
            );
    }


    public function test_candidate_cannot_modify_accepted_files()
    {
        // 1. Setup accepted file
        $this->candidateDetail->update([
            'cv_path' => 'path/to/cv.pdf',
            'cv_status' => 'accepted'
        ]);

        // 2. Attempt to update accepted file
        $newFile = UploadedFile::fake()->create('new_cv.pdf', 100);

        $response = $this->actingAs($this->candidate)
            ->post(route('candidate.files.update'), [
                'cv' => $newFile
            ]);

        // 3. Verify rejection
        $response->assertRedirect()
            ->assertSessionHas('message', 'Cv has been accepted and cannot be modified.');

        // 4. Verify file remains unchanged
        $this->assertDatabaseHas('candidate_details', [
            'user_id' => $this->candidate->id,
            'cv_path' => 'path/to/cv.pdf',
            'cv_status' => 'accepted'
        ]);
    }
}
