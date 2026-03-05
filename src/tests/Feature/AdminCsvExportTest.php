<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Admin;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Tests\TestCase;

class AdminCsvExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_export_csv()
    {
        $admin = Admin::create([
            'name' => '管理者テスト',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        $user = User::create([
            'name' => 'ユーザーテスト',
            'email' => 'user@test.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'worked_on' => Carbon::today()->format('Y-m-d'),
            'start_time' => Carbon::today()->setTime(9,0),
            'end_time' => Carbon::today()->setTime(18,0),
            'rest_seconds' => 3600,
            'is_paid_rest' => false,
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/staff/export');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $content = $response->getContent();
        $this->assertStringContainsString('ユーザーテスト', $content);
        $this->assertStringContainsString(Carbon::today()->format('Y-m-d'), $content);
    }
}
