<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class AdminAttendanceController extends Controller
{
        public function index(Request $request)
    {
        $dateStr = $request->query('date', Carbon::today()->format('Y-m-d'));
        $currentDate = Carbon::parse($dateStr);

        $attendances = Attendance::with(['user', 'rests'])
            ->whereDate('date', $currentDate->format('Y-m-d'))
            ->get();


        return view('admin.attendance_list', compact('currentDate', 'attendances'));
    }

        public function show(Attendance $attendance)
    {
        $attendance->load(['user', 'rests']);

        return view('admin.attendance_detail', compact('attendance'));
    }

        public function update(Request $request, Attendance $attendance)
    {
            $beforeStart = optional($attendance->start_time)->format('H:i:s');
            $beforeEnd = optional($attendance->end_time)->format('H:i:s');

            $yearInput = $request->input('year');
            $monthInput = $request->input('month');
            $dayInput = $request->input('day');

            $normalizeNumber = function ($value) {
                if (is_null($value) || $value === '') {
                    return null;
                }

                return mb_convert_kana(trim((string) $value), 'n');
            };

            $request->merge([
                'year' => $normalizeNumber($yearInput),
                'month' => $normalizeNumber($monthInput),
                'day' => $normalizeNumber($dayInput),
            ]);

        $validated = $request->validate([
                'year' => ['nullable', 'regex:/^\d{4}$/'],
                'month' => ['nullable', 'regex:/^\d{1,2}$/'],
                'day' => ['nullable', 'regex:/^\d{1,2}$/'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'rests' => ['nullable', 'array'],
            'rests.*.start_time' => ['nullable', 'date_format:H:i'],
            'rests.*.end_time' => ['nullable', 'date_format:H:i'],
        ], [
                'year.regex' => '年は4桁の数字で入力してください。',
                'month.regex' => '月は数字で入力してください。',
                'day.regex' => '日は数字で入力してください。',
            'start_time.required' => '出勤時刻を入力してください。',
            'start_time.date_format' => '出勤時刻の形式が正しくありません。',
            'end_time.date_format' => '退勤時刻の形式が正しくありません。',
            'rests.*.start_time.date_format' => '休憩開始時刻の形式が正しくありません。',
            'rests.*.end_time.date_format' => '休憩終了時刻の形式が正しくありません。',
        ]);

        $redirectStaffId = $request->input('redirect_staff_id');
        if (!is_null($redirectStaffId) && !ctype_digit((string) $redirectStaffId)) {
            $redirectStaffId = null;
        }

        $redirectMonth = $request->input('redirect_month');
        if (!is_null($redirectMonth) && !preg_match('/^\d{4}-\d{2}$/', (string) $redirectMonth)) {
            $redirectMonth = null;
        }

        $targetDate = $attendance->date;
        if (
            isset($validated['year'], $validated['month'], $validated['day']) &&
            $validated['year'] && $validated['month'] && $validated['day']
        ) {
            $targetDate = Carbon::createFromDate(
                (int) $validated['year'],
                (int) $validated['month'],
                (int) $validated['day']
            )->format('Y-m-d');
        }

        $startTime = Carbon::createFromFormat('H:i', $validated['start_time'])->format('H:i:s');
        $endTime = isset($validated['end_time'])
            ? Carbon::createFromFormat('H:i', $validated['end_time'])->format('H:i:s')
            : null;

        try {
            DB::transaction(function () use ($attendance, $validated, $targetDate, $startTime, $endTime) {
                DB::table('attendances')
                    ->where('id', $attendance->id)
                    ->update([
                        'date' => $targetDate,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'updated_at' => now(),
                    ]);

                $attendance->rests()->delete();
                foreach ($validated['rests'] ?? [] as $restData) {
                    $restStart = $restData['start_time'] ?? null;
                    $restEnd = $restData['end_time'] ?? null;
                    if ($restStart && $restEnd) {
                        DB::table('rests')->insert([
                            'attendance_id' => $attendance->id,
                            'start_time' => Carbon::createFromFormat('H:i', $restStart)->format('H:i:s'),
                            'end_time' => Carbon::createFromFormat('H:i', $restEnd)->format('H:i:s'),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            });

            $fresh = DB::table('attendances')->where('id', $attendance->id)->first();
            if (!$fresh || $fresh->start_time !== $startTime || $fresh->end_time !== $endTime) {
                throw new \RuntimeException('Attendance time values were not persisted.');
            }

            Log::info('admin attendance updated', [
                'attendance_id' => $attendance->id,
                'before_start_time' => $beforeStart,
                'before_end_time' => $beforeEnd,
                'after_start_time' => $fresh->start_time,
                'after_end_time' => $fresh->end_time,
                'target_date' => $targetDate,
                'redirect_staff_id' => $redirectStaffId,
                'redirect_month' => $redirectMonth,
            ]);
        } catch (Throwable $e) {
            Log::error('admin attendance update failed', [
                'attendance_id' => $attendance->id,
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['update' => '更新に失敗しました。日付の重複や入力内容を確認してください。']);
        }

        if (!empty($redirectStaffId)) {
            return redirect()
                ->route('admin.staff.attendance', [
                    'id' => (int) $redirectStaffId,
                    'month' => $redirectMonth ?? Carbon::parse($targetDate)->format('Y-m'),
                ])
                ->with('message', '勤怠情報を更新しました。');
        }

        return redirect()->route('admin.attendance.show', $attendance->id)
            ->with('message', '勤怠情報を更新しました。');
    }
}
