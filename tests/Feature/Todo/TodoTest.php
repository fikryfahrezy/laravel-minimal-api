<?php

namespace Tests\Feature\Todo;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TodoTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_todos(): void
    {
        $this->getJson('/api/v1/todos')->assertUnauthorized();
    }

    public function test_authenticated_user_can_crud_todos(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $createResponse = $this->postJson('/api/v1/todos', [
            'title' => 'Ship API',
            'description' => 'Finish CRUD endpoints',
            'is_completed' => false,
        ]);

        $todoId = $createResponse->json('id');

        $createResponse
            ->assertCreated()
            ->assertJsonPath('title', 'Ship API');

        $this->getJson('/api/v1/todos')
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.id', $todoId);

        $this->getJson("/api/v1/todos/{$todoId}")
            ->assertOk()
            ->assertJsonPath('description', 'Finish CRUD endpoints');

        $this->putJson("/api/v1/todos/{$todoId}", [
            'title' => 'Ship API v1',
            'is_completed' => true,
        ])->assertOk()->assertJsonPath('is_completed', true);

        $this->deleteJson("/api/v1/todos/{$todoId}")
            ->assertNoContent();

        $this->assertDatabaseMissing('todos', [
            'id' => $todoId,
        ]);
    }

    public function test_authenticated_user_can_paginate_filter_and_sort_todos(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        Todo::factory()->for($user)->create([
            'title' => 'Write docs',
            'is_completed' => false,
        ]);

        Todo::factory()->for($user)->create([
            'title' => 'Archive notes',
            'is_completed' => true,
        ]);

        Todo::factory()->for($user)->create([
            'title' => 'Build tests',
            'is_completed' => false,
        ]);

        $this->getJson('/api/v1/todos?search=Write&per_page=1&sort_by=title&sort_direction=asc')
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('meta.per_page', 1)
            ->assertJsonPath('data.0.title', 'Write docs');

        $this->getJson('/api/v1/todos?is_completed=1&sort_by=title&sort_direction=asc')
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.title', 'Archive notes');

        $sortedResponse = $this->getJson('/api/v1/todos?sort_by=title&sort_direction=asc')
            ->assertOk();

        $this->assertSame('Archive notes', $sortedResponse->json('data.0.title'));
        $this->assertSame('Build tests', $sortedResponse->json('data.1.title'));
        $this->assertSame('Write docs', $sortedResponse->json('data.2.title'));
    }

    public function test_user_cannot_access_another_users_todo(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $todo = Todo::factory()->for($owner)->create();

        Sanctum::actingAs($intruder);

        $this->getJson("/api/v1/todos/{$todo->id}")->assertNotFound();
    }
}
