<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\Rest;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->first();

        if (!$attendance) {
            $status = 0;
            $status_text = '勤務外';
        } elseif ($attendance->end_time) {
            $status = 3;
            $status_text = '退勤済';
        } elseif ($attendance->rests()->whereNull('end_time')->exists()) {
            $status = 2;
            $status_text = '休憩中';
        } else {
            $status = 1;
            $status_text = '出勤中';
        }

        return view('attendance', compact('status', 'status_text'));
    }

    public function startWork()
    {
        $user_id = Auth::id();
        $today = Carbon::today()->format('Y-m-d');

        if (Attendance::forUser($user_id)->today()->exists()) {
            return redirect()->back()->with('error', '本日は既に出勤しています。');
        }

        Attendance::create([
            'user_id' => $user_id,
            'date' => $today,
            'start_time' => Carbon::now(),
        ]);

        return redirect()->back()->with('message', '出勤しました。');
    }

    public function endWork()
    {
        $user_id = Auth::id();
        $attendance = Attendance::forUser($user_id)->today()->first();

        if ($attendance && !$attendance->end_time) {
            $attendance->update(['end_time' => Carbon::now()]);
            return redirect()->back()->with('message', '退勤しました。お疲れ様でした。');
        }

        return redirect()->back()->with('error', 'ステータスが不正です。');
    }

    public function startRest()
    {
        $user_id = Auth::id();
        $attendance = Attendance::forUser($user_id)->today()->first();

        if ($attendance && !$attendance->end_time) {
            Rest::create([
                'attendance_id' => $attendance->id,
                'start_time' => Carbon::now(),
            ]);
            return redirect()->back()->with('message', '休憩に入りました。');
        }

        return redirect()->back()->with('error', '出勤していません。');
    }

    public function endRest()
    {
        $user_id = Auth::id();
        $attendance = Attendance::forUser($user_id)->today()->first();

        if ($attendance) {
            $rest = $attendance->rests()->whereNull('end_time')->first();
            if ($rest) {
                $rest->update(['end_time' => Carbon::now()]);
                return redirect()->back()->with('message', '休憩を終了しました。');
            }
        }

        return redirect()->back()->with('error', '休憩中ではありません。');
    }

    public function monthlyList(Request $request)
    {
        $user = Auth::user();

        $targetMonth = $request->query('month', now()->format('Y-m'));
        $currentDate = Carbon::parse($targetMonth . '-01');

        $attendances = Attendance::with('rests')
            ->where('user_id', $user->id)
            ->whereYear('date', $currentDate->year)
            ->whereMonth('date', $currentDate->month)
            ->get()
            ->keyBy(fn($item) => Carbon::parse($item->date)->format('Y-m-d'));

        $daysInMonth = $currentDate->daysInMonth;

        $monthlyData = collect(range(1, $daysInMonth))->map(function ($i) use ($currentDate, $attendances) {
            $dateStr = $currentDate->copy()->day($i)->format('Y-m-d');
            $attendance = $attendances->get($dateStr);
            return [
                'date'       => $dateStr,
                'attendance' => $attendance,
                'rest_time'  => $attendance ? $attendance->total_rest : '00:00:00',
                'work_time'  => $attendance ? $attendance->total_work : '00:00:00',
            ];
        });

        return view('attendance_list', compact('monthlyData', 'currentDate', 'user'));
    }

    public function show(Attendance $attendance)
    {
        if ($attendance->user_id !== Auth::id()) {
            return redirect()->route('attendance.list')->with('error', 'アクセス権限がありません。');
        }

        $is_pending = \App\Models\AttendanceCorrectionRequest::where('attendance_id', $attendance->id)
            ->where('status', 'pending')
            ->exists();

        return view('attendance_detail', compact('attendance', 'is_pending'));
    }
}