<?php

namespace Tests\Feature\Api\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    public function test_user_registration()
    {
        $userData = [
            'name' => $name = fake()->name,
            'email' => $email = fake()->email,
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'name' => $name,
            'email' => $email,
        ]);

        $user = User::query()
            ->where('email', $email)
            ->first();

        $this->assertNotNull($user->id);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $name,
            'email' => $email
        ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'auth_token',
        ]);
    }
}
