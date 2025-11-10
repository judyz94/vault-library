<?php

namespace Tests\Feature\Api;

use App\Enums\UserRoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $libraryUser;
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->libraryUser = User::factory()->create([
            'role' => UserRoleEnum::User->value,
        ]);

        $this->adminUser = User::factory()->create([
            'role' => UserRoleEnum::Admin->value,
        ]);
    }

    public function test_index_returns_paginated_users(): void
    {
        User::factory()->count(15)->create();

        $response = $this->actingAs($this->libraryUser, 'sanctum')
            ->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data',
            ])
            ->assertJson([
                'status' => 'success',
                'message' => 'Users retrieved successfully.'
            ]);
    }

    public function test_show_returns_user(): void
    {
        $response = $this->actingAs($this->libraryUser, 'sanctum')
            ->getJson("/api/users/{$this->libraryUser->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => ['id', 'name', 'email', 'library_id', 'role']
            ])
            ->assertJson([
                'status' => 'success',
                'message' => 'User retrieved successfully.'
            ]);
    }

    public function test_store_creates_user_successfully_as_admin(): void
    {
        $payload = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'library_id' => 'AAA1',
            'role' => UserRoleEnum::User->value,
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/users', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'User created successfully.'
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com'
        ]);
    }

    public function test_store_fails_for_non_admin(): void
    {
        $payload = [
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'library_id' => 'AAA1',
        ];

        $response = $this->actingAs($this->libraryUser, 'sanctum')
            ->postJson('/api/users', $payload);

        $response->assertStatus(403);
    }

    public function test_update_user_successfully_as_admin(): void
    {
        $payload = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'library_id' => 'AAA1',
            'role' => UserRoleEnum::Admin->value
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/users/{$this->libraryUser->id}", $payload);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'User updated successfully.'
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->libraryUser->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'library_id' => 'AAA1',
            'role' => UserRoleEnum::Admin->value
        ]);
    }

    public function test_update_fails_for_non_admin(): void
    {
        $payload = ['name' => 'Fail Update'];

        $response = $this->actingAs($this->libraryUser, 'sanctum')
            ->putJson("/api/users/{$this->libraryUser->id}", $payload);

        $response->assertStatus(403);
    }

    public function test_destroy_deletes_user_as_admin(): void
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/users/{$this->libraryUser->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'User deleted successfully.'
            ]);

        $this->assertDatabaseMissing('users', ['id' => $this->libraryUser->id]);
    }

    public function test_destroy_fails_for_non_admin(): void
    {
        $response = $this->actingAs($this->libraryUser, 'sanctum')
            ->deleteJson("/api/users/{$this->adminUser->id}");

        $response->assertStatus(403);
    }
}
