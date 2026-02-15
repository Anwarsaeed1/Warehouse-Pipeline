<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_success_returns_token_and_user(): void
    {
        User::factory()->create([
            'email'    => 'test@example.com',
            'password' => 'secret123',
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => 'test@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'token',
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
            ],
        ]);
        $this->assertNotEmpty($response->json('data.token'));
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create(['email' => 'user@example.com']);

        $response = $this->postJson('/api/login', [
            'email'    => 'user@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);
    }

    public function test_login_validation_requires_email_and_password(): void
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_logout_requires_authentication(): void
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401);
    }

    public function test_logout_success_with_valid_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');

        $response->assertStatus(200);
    }
}
