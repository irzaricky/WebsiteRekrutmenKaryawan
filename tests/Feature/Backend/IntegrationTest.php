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

    /**
     * Test flow:
     * 1. Candidate uploads CV file
     * 2. HRD should see the uploaded file with pending status 
     */
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


    /**
     * Test flow:
     * 1. Set initial CV status as pending
     * 2. HRD reviews and accepts the file
     * 3. Candidate checks and sees updated accepted status
     * 4. Verify status is saved in database
     */
    public function test_when_hrd_reviews_files_candidate_can_see_status_changes()
    {
        // 1. Setup initial file upload
        $this->candidateDetail->update([
            'cv_path' => 'path/to/cv.pdf',
            'cv_status' => 'pending'
        ]);

        // 2. HRD reviews and updates status
        $response = $this->actingAs($this->hrd)
            ->from(route('dashboard.pending-files'))
            ->post(route('dashboard.update-file-status'), [
                'candidate_id' => $this->candidate->id,
                'file_type' => 'cv',
                'status' => 'accepted'
            ]);

        // Assert redirect back with success
        $response->assertRedirect(route('dashboard.pending-files'));

        // 3. Candidate checks file status
        $response = $this->actingAs($this->candidate)
            ->followingRedirects()  // Follow any redirects
            ->get(route('candidate.file-status'));

        $response->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->where('candidateDetail.cv_status', 'accepted')
            );

        // 4. Verify database state
        $this->assertDatabaseHas('candidate_details', [
            'user_id' => $this->candidate->id,
            'cv_status' => 'accepted'
        ]);
    }


    /**
     * Test flow:
     * 1. Set CV status as accepted
     * 2. Candidate tries to modify accepted file
     * 3. System rejects modification
     * 4. Verify original file remains unchanged
     */
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


    /**
     * Test flow:
     * 1. Set CV status as rejected
     * 2. Candidate uploads new CV file
     * 3. System accepts new upload
     * 4. Verify new file is saved
     */
    public function test_candidate_can_reupload_rejected_files()
    {
        // 1. Setup rejected file
        $this->candidateDetail->update([
            'cv_path' => 'path/to/old_cv.pdf',
            'cv_status' => 'rejected'
        ]);

        // 2. Upload new file
        $newFile = UploadedFile::fake()->create('new_cv.pdf', 100);

        $response = $this->actingAs($this->candidate)
            ->post(route('candidate.files.update'), [
                'cv' => $newFile
            ]);

        // 3. Verify acceptance
        $response->assertSessionHasNoErrors();

        // 4. Check database updated
        $this->assertDatabaseMissing('candidate_details', [
            'user_id' => $this->candidate->id,
            'cv_path' => 'path/to/old_cv.pdf'
        ]);
    }
}
