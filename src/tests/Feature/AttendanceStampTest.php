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
        $response->assertSee('出勤');
        $response->assertDontSee('勤務終了'); 
    }

    public function test_can_start_work()
    {
        $this->post('/attendance/start');

        $this->assertTrue(
            Attendance::where('user_id', $this->user->id)
                ->whereDate('date', Carbon::today())
                ->exists()
        );

        $response = $this->get('/attendance');
        $response->assertSee('勤務終了');
        $response->assertSee('休憩入');
        $response->assertDontSee('>出勤<', false); 
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
        $response->assertSee('休憩戻');
        $response->assertDontSee('休憩入');

        $this->post('/attendance/rest/end');
        
        $response = $this->get('/attendance');
        $response->assertSee('勤務終了');
    }

    public function test_can_end_work()
    {
        $this->post('/attendance/start');

        $this->post('/attendance/end');

        $this->assertFalse(
            Attendance::where('user_id', $this->user->id)
                ->whereDate('date', Carbon::today())
                ->whereNull('end_time')
                ->exists()
        );

        $response = $this->get('/attendance');
        $response->assertSee('退勤しました');
        $response->assertDontSee('勤務終了');
    }
}