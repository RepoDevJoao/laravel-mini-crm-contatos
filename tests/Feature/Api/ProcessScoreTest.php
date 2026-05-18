<?php

namespace Tests\Feature\Api;

use App\Infrastructure\Broadcasting\Events\ContactScoreProcessed;
use App\Infrastructure\Persistence\Models\Contact;
use App\Infrastructure\Queue\Jobs\ProcessContactScoreJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProcessScoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_process_score_dispatches_job(): void
    {
        Queue::fake();

        $contact = Contact::factory()->create();

        $response = $this->postJson("/api/contacts/{$contact->id}/process-score");

        $response->assertStatus(202)
            ->assertJsonFragment(['message' => 'Score processing queued.']);

        Queue::assertPushed(ProcessContactScoreJob::class);
    }

    public function test_process_score_returns_404_for_nonexistent_contact(): void
    {
        $response = $this->postJson('/api/contacts/999/process-score');

        $response->assertStatus(404);
    }

    public function test_job_processes_score_and_fires_event(): void
    {
        Event::fake();

        $contact = Contact::factory()->create([
            'name'  => 'João Silva',
            'email' => 'joao@empresa.com.br',
            'phone' => '11999999999',
        ]);

        $job = new ProcessContactScoreJob($contact->id);
        $job->handle(app(\App\Application\Contact\UseCases\ProcessContactScoreUseCase::class));

        $updated = Contact::find($contact->id);

        $this->assertSame('active', $updated->status);
        $this->assertSame(60, $updated->score);
        $this->assertNotNull($updated->processed_at);

        Event::assertDispatched(ContactScoreProcessed::class);
    }

    public function test_job_sets_status_failed_on_invalid_email(): void
    {
        Event::fake();

        $contact = Contact::factory()->create([
            'email' => 'not-valid',
        ]);

        $contact->email = 'not-valid';
        $contact->saveQuietly();

        $job = new ProcessContactScoreJob($contact->id);

        try {
            $job->handle(app(\App\Application\Contact\UseCases\ProcessContactScoreUseCase::class));
        } catch (\Throwable) {
        }

        $updated = Contact::find($contact->id);
        $this->assertSame('failed', $updated->status);
    }
}