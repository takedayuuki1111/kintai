<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verification_email_is_sent_upon_registration()
    {
        Event::fake();

        $response = $this->post('/register', [
            'name' => '認証テストユーザー',
            'email' => 'verify@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        Event::assertDispatched(Registered::class);
    }

    public function test_unverified_user_cannot_access_attendance_page()
    {
        $user = User::create([
            'name' => '未認証ユーザー',
            'email' => 'unverified@example.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => null, 
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertRedirect('/email/verify');
    }

    public function test_user_can_verify_email()
    {
        $user = User::create([
            'name' => '認証テストユーザー',
            'email' => 'verify2@example.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $this->assertTrue($user->fresh()->hasVerifiedEmail());

        $response->assertRedirect('/attendance?verified=1'); 
    }
}
