<?php

namespace Tests\Feature;

use App\Models\Organizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JsonResponseTest extends TestCase
{
    use RefreshDatabase;

    public function test_organizer_registration_returns_correct_json_structure()
    {
        $response = $this->postJson('/api/auth/organizer/register', [
            'name' => 'Test Organizer',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'organizer',
                    'token',
                    'token_type',
                ],
                'meta' => [
                    'timestamp',
                    'version',
                ],
            ])
            ->assertJson([
                'status' => 'success',
                'message' => 'Organizer registered successfully',
            ]);
    }

    public function test_organizer_login_returns_correct_json_structure()
    {
        $organizer = Organizer::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/auth/organizer/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'user',
                    'token',
                    'token_type',
                ],
                'meta' => [
                    'timestamp',
                    'version',
                ],
            ])
            ->assertJson([
                'status' => 'success',
                'message' => 'Login successful',
            ]);
    }

    public function test_validation_error_returns_correct_json_structure()
    {
        $response = $this->postJson('/api/auth/organizer/register', [
            // Missing required fields
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors',
            ]);
        // Note: Laravel's default validation error response might not use our JsonResponse class unless we override the exception handler.
        // For now, we are testing our custom responses. 
        // If we want validation errors to also follow the structure, we need to handle ValidationException in Handler.php.
        // But the user asked to use the class for *building* responses, which we did in controllers.
    }
}
