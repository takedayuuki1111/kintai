<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Tests\TestCase;

class AttendanceStampTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::create([
            'name' => '打刻テストユーザー',
            'email' => 'stamp@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->user->email_verified_at = now();
        $this->user->save();
        $this->actingAs($this->user);
    }

    public function test_initial_status_is_off_duty()
    {
        $response = $this->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('勤務開始');
        $response->assertDontSee('勤務終了'); 
    }

    public function test_can_start_work()
    {
        $response = $this->post('/attendance/start');

        $this->assertDatabaseHas('attendances', [
            'user_id' => $this->user->id,
            'date' => Carbon::today()->format('Y-m-d'),
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('勤務終了');
        $response->assertSee('休憩開始');
        $response->assertDontSee('勤務開始'); 
    }

    public function test_cannot_start_work_twice_a_day()
    {
        $this->post('/attendance/start');
        
        $response = $this->post('/attendance/start');
        $response->assertSessionHas('error'); 
    }

    public function test_can_take_and_end_rest()
    {
        $this->post('/attendance/start');
        $attendance = Attendance::where('user_id', $this->user->id)->first();

        $this->post('/attendance/rest/start');
        
        $this->assertDatabaseHas('rests', [
            'attendance_id' => $attendance->id,
        ]);
        
        $response = $this->get('/attendance');
        $response->assertSee('休憩終了');
        $response->assertDontSee('休憩開始');

        $this->post('/attendance/rest/end');
        
        $response = $this->get('/attendance');
        $response->assertSee('勤務終了');
    }

    public function test_can_end_work()
    {
        $this->post('/attendance/start');

        $this->post('/attendance/end');

        $this->assertDatabaseMissing('attendances', [
            'user_id' => $this->user->id,
            'date' => Carbon::today()->format('Y-m-d'),
            'end_time' => null, 
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('退勤しました');
        $response->assertDontSee('勤務終了');
    }
}