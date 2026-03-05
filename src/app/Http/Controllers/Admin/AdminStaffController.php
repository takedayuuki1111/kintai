<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminStaffController extends Controller
{
        public function index()
    {
        $staffs = User::all();
        return view('admin.staff_list', compact('staffs'));
    }

        public function show(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $month = $request->query('month', Carbon::now()->format('Y-m'));
        $currentDate = Carbon::parse($month . '-01');

        $monthlyData = $this->getMonthlyData($user, $currentDate);

        return view('admin.staff_attendance', compact('user', 'currentDate', 'monthlyData'));
    }

        public function exportCsv(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $month = $request->query('month', Carbon::now()->format('Y-m'));
        $currentDate = Carbon::parse($month . '-01');

        $monthlyData = $this->getMonthlyData($user, $currentDate);

        $csvFilename = "{$user->name}_{$month}_勤怠.csv";
        $handle = fopen('php://memory', 'r+');

        fprintf($handle, "\xEF\xBB\xBF");
        fputcsv($handle, ['日付', '出勤', '退勤', '休憩', '合計']);

        foreach ($monthlyData as $data) {
            $date = Carbon::parse($data['date'])->format('Y/m/d');
            $start = $data['attendance'] ? $data['attendance']->start_time->format('H:i') : '';
            $end = $data['attendance'] && $data['attendance']->end_time ? $data['attendance']->end_time->format('H:i') : '';
            fputcsv($handle, [$date, $start, $end, $data['rest_time'], $data['work_time']]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', "attachment; filename=\"{$csvFilename}\"");
    }

        private function getMonthlyData(User $user, Carbon $currentDate)
    {
        $monthlyData = [];
        $daysInMonth = $currentDate->daysInMonth;

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = $currentDate->copy()->addDays($i - 1)->format('Y-m-d');
            $attendance = $user->attendances()->with('rests')->where('date', $date)->first();

            $rest_time = '0:00';
            $work_time = '0:00';

            if ($attendance && $attendance->end_time) {
                $total_rest = 0;
                foreach ($attendance->rests as $rest) {
                    if ($rest->end_time) {
                        $total_rest += Carbon::parse($rest->start_time)->diffInSeconds(Carbon::parse($rest->end_time));
                    }
                }
                $work_seconds = Carbon::parse($attendance->start_time)->diffInSeconds(Carbon::parse($attendance->end_time)) - $total_rest;
                $rest_time = gmdate('G:i', $total_rest);
                $work_time = gmdate('G:i', $work_seconds);
            }

            $monthlyData[] = [
                'date' => $date,
                'attendance' => $attendance,
                'rest_time' => $rest_time,
                'work_time' => $work_time,
            ];
        }

        return $monthlyData;
    }
}
