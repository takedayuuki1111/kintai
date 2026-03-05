<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Admin;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Facades\Hash;
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
            'password' => Hash::make('password'),
        ]);

        $user = User::create([
            'name' => 'ユーザーテスト',
            'email' => 'user@test.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today()->format('Y-m-d'),
            'start_time' => Carbon::today()->setTime(9,0),
            'end_time' => Carbon::today()->setTime(18,0),
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.staff.attendance.export', [
            'id' => $user->id,
            'month' => Carbon::today()->format('Y-m'),
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $content = $response->getContent();
        $this->assertStringContainsString('日付,出勤,退勤,休憩,合計', $content);
        $this->assertStringContainsString(Carbon::today()->format('Y/m/d'), $content);
    }
}
