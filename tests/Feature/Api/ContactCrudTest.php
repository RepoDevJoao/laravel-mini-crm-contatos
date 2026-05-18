<?php

namespace Tests\Feature\Api;

use App\Infrastructure\Persistence\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_contact(): void
    {
        $response = $this->postJson('/api/contacts', [
            'name'  => 'João Silva',
            'email' => 'joao@empresa.com.br',
            'phone' => '(11) 99999-9999',
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name'   => 'João Silva',
                'email'  => 'joao@empresa.com.br',
                'phone'  => '11999999999',
                'score'  => 0,
                'status' => 'pending',
            ]);
    }

    public function test_cannot_create_contact_with_duplicate_email(): void
    {
        Contact::factory()->create(['email' => 'joao@empresa.com.br']);

        $response = $this->postJson('/api/contacts', [
            'name'  => 'João Silva',
            'email' => 'joao@empresa.com.br',
            'phone' => '(11) 99999-9999',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_can_list_contacts(): void
    {
        Contact::factory()->count(3)->create();

        $response = $this->getJson('/api/contacts');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'total', 'per_page']);
    }

    public function test_can_show_contact(): void
    {
        $contact = Contact::factory()->create();

        $response = $this->getJson("/api/contacts/{$contact->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['email' => $contact->email]);
    }

    public function test_show_returns_404_for_nonexistent_contact(): void
    {
        $response = $this->getJson('/api/contacts/999');

        $response->assertStatus(404);
    }

    public function test_can_update_contact(): void
    {
        $contact = Contact::factory()->create();

        $response = $this->putJson("/api/contacts/{$contact->id}", [
            'name'  => 'Nome Atualizado',
            'email' => 'novo@empresa.com.br',
            'phone' => '(21) 98888-8888',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Nome Atualizado']);
    }

    public function test_can_delete_contact(): void
    {
        $contact = Contact::factory()->create();

        $response = $this->deleteJson("/api/contacts/{$contact->id}");

        $response->assertStatus(204);
        $this->assertSoftDeleted('contacts', ['id' => $contact->id]);
    }
}