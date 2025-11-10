<?php

namespace Tests\Feature\Api;

use App\Enums\UserRoleEnum;
use App\Models\Author;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthorControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => UserRoleEnum::Admin->value]);
        Sanctum::actingAs($this->user, ['*']);
    }

    public function test_index_returns_paginated_authors(): void
    {
        Author::factory()->count(15)->create();

        $response = $this->getJson('/api/authors');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data',
        ]);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Authors retrieved successfully.',
        ]);
    }

    public function test_store_creates_new_author(): void
    {
        $payload = [
            'name' => 'John Doe',
            'bio' => 'American software engineer.',
        ];

        $response = $this->postJson('/api/authors', $payload);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'status',
            'message',
            'data',
        ]);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Author created successfully.',
            'data' => [
                'name' => 'John Doe',
                'bio' => 'American software engineer.',
            ],
        ]);

        $this->assertDatabaseHas('authors', [
            'name' => 'John Doe',
            'bio' => 'American software engineer.',
        ]);
    }

    public function test_non_admin_user_cannot_access_authors(): void
    {
        $user = User::factory()->create(['role' => UserRoleEnum::User->value]);
        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson('/api/authors');

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Access denied. Admins only.',
        ]);
    }
}
