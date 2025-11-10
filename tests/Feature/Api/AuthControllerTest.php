<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_valid_credentials_returns_token(): void
    {
        $password = 'secret123';
        User::factory()->create([
            'email' => 'johndoe@example.com',
            'password' => Hash::make($password),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'johndoe@example.com',
            'password' => $password,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Login successful.',
        ]);
    }

    public function test_login_with_invalid_credentials_fails(): void
    {
        User::factory()->create([
            'email' => 'johndoe@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'johndoe@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    public function test_logout_revokes_user_token(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);
        $user->createToken('api-token')->plainTextToken;

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Logged out successfully.',
            'data' => [],
        ]);
    }
}
