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
        Admin::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => '管理者A',
                'password' => Hash::make('password'),
            ]
        );

        $user = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'テスト太郎',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        for ($i = 1; $i <= 5; $i++) {
            $date = Carbon::today()->subDays($i);
            $attendance = Attendance::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'date' => $date->toDateString(),
                ],
                [
                    'start_time' => $date->copy()->setTime(9, 0),
                    'end_time' => $date->copy()->setTime(18, 0),
                ]
            );

            Rest::updateOrCreate(
                [
                    'attendance_id' => $attendance->id,
                    'start_time' => $date->copy()->setTime(12, 0),
                ],
                [
                    'end_time' => $date->copy()->setTime(13, 0),
                ]
            );
        }
    }
}
