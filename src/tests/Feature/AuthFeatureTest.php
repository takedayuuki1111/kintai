<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Tests\TestCase;

class AuthFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);

        $response->assertRedirect('/attendance'); 
    }

    public function test_register_validation_errors()
    {
        $response = $this->post('/register', []);
        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function test_user_can_login()
    {
        $user = User::create([
            'name' => 'テスト太郎',
            'email' => 'login@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'login@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/attendance');
    }

    public function test_login_validation_and_auth_errors()
    {
        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'password123',
        ]);
        $response->assertSessionHasErrors('email'); 
        $this->assertGuest();
    }
}