<?php

namespace Tests\Feature;

use App\Models\Idea;
use App\Models\TeamPosition;
use App\Models\User;
use App\Models\WorkflowStage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InnovationPlatformTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic stages
        WorkflowStage::create([
            'name' => 'Ideation',
            'slug' => 'ideation',
            'order' => 1,
            'auto_advance_threshold' => 1, // 1 upvote needed to advance
            'color' => '#3b82f6',
        ]);
        WorkflowStage::create([
            'name' => 'Peer Voting',
            'slug' => 'peer-voting',
            'order' => 2,
            'auto_advance_threshold' => 50,
            'color' => '#8b5cf6',
        ]);
    }

    public function test_can_view_ideas_index(): void
    {
        $user = User::factory()->create();
        Idea::create([
            'user_id' => $user->id,
            'title' => 'Inovasi AI Cerdas',
            'description' => 'Solusi menggunakan AI untuk efisiensi energi terbarukan.',
            'category' => 'teknologi',
            'current_stage' => 'ideation',
            'is_published' => true,
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Inovasi AI Cerdas');
    }

    public function test_can_create_idea_when_authenticated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/ideas', [
            'title' => 'Platform Riset Kolaboratif',
            'description' => 'Platform ini membantu mahasiswa mencari partner riset lintas jurusan secara otomatis.',
            'category' => 'pendidikan',
            'tags' => ['riset', 'kampus'],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('ideas', [
            'title' => 'Platform Riset Kolaboratif',
            'category' => 'pendidikan',
        ]);
    }

    public function test_can_vote_and_auto_advance_stage(): void
    {
        $author = User::factory()->create();
        $voter = User::factory()->create();

        $idea = Idea::create([
            'user_id' => $author->id,
            'title' => 'Sistem IoT Kampus',
            'description' => 'Deskripsi panjang sistem IoT kampus yang hemat energi dan ramah lingkungan.',
            'category' => 'teknologi',
            'current_stage' => 'ideation',
            'is_published' => true,
        ]);

        $response = $this->actingAs($voter)->postJson("/ideas/{$idea->id}/vote", [
            'type' => 'upvote',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'upvotes_count' => 1,
            'user_vote_type' => 'upvote',
        ]);

        // Assert auto-advance to peer-voting because threshold is 1
        $idea->refresh();
        $this->assertEquals('peer-voting', $idea->current_stage);
    }

    public function test_can_add_comment(): void
    {
        $user = User::factory()->create();
        $idea = Idea::create([
            'user_id' => $user->id,
            'title' => 'Inovasi Aplikasi Pengolahan Sampah',
            'description' => 'Pengolahan sampah dengan sistem reward token digital.',
            'category' => 'sosial',
            'current_stage' => 'ideation',
            'is_published' => true,
        ]);

        $response = $this->actingAs($user)->post("/ideas/{$idea->id}/comments", [
            'body' => 'Ide yang sangat menarik dan solutif!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'idea_id' => $idea->id,
            'body' => 'Ide yang sangat menarik dan solutif!',
        ]);
    }

    public function test_can_create_and_apply_for_team_position(): void
    {
        $owner = User::factory()->create();
        $applicant = User::factory()->create();

        $idea = Idea::create([
            'user_id' => $owner->id,
            'title' => 'Platform Inkubasi Startup Mahasiswa',
            'description' => 'Membantu mahasiswa memulai startup dengan mentoring langsung.',
            'category' => 'bisnis',
            'current_stage' => 'ideation',
            'is_published' => true,
        ]);

        // Owner creates position
        $response = $this->actingAs($owner)->post("/ideas/{$idea->id}/positions", [
            'title' => 'UI/UX Designer',
            'description' => 'Mendesain prototipe figma',
            'skills_required' => ['Figma', 'Prototyping'],
        ]);
        $response->assertRedirect();

        $position = TeamPosition::where('idea_id', $idea->id)->first();
        $this->assertNotNull($position);

        // Applicant applies
        $response = $this->actingAs($applicant)->post("/positions/{$position->id}/apply", [
            'message' => 'Saya tertarik bergabung sebagai desainer.',
        ]);
        $response->assertRedirect();

        $this->assertDatabaseHas('team_applications', [
            'team_position_id' => $position->id,
            'user_id' => $applicant->id,
        ]);
    }

    public function test_admin_can_access_workflow_management(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $regularUser = User::factory()->create(['role' => 'member']);

        // Regular user should be forbidden
        $this->actingAs($regularUser)->get('/admin/workflow')->assertForbidden();

        // Admin should be allowed
        $this->actingAs($admin)->get('/admin/workflow')->assertStatus(200);
    }
}
