<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Admin::create([
            'name' => '管理者A',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $user = User::create([
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        for ($i = 1; $i <= 5; $i++) {
            $date = Carbon::today()->subDays($i);
            $attendance = Attendance::create([
                'user_id' => $user->id,
                'worked_on' => $date->toDateString(),
                'start_time' => $date->setTime(9, 0),
                'end_time' => $date->setTime(18, 0),
                'rest_seconds' => 3600, // 1h break
                'is_paid_rest' => false,
            ]);

            Rest::create([
                'attendance_id' => $attendance->id,
                'start_time' => $date->setTime(12, 0),
                'end_time' => $date->setTime(13, 0),
            ]);
        }
    }
}
