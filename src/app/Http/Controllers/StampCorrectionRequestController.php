<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;
use Carbon\Carbon;

class StampCorrectionRequestController extends Controller
{
    public function store(\App\Http\Requests\AttendanceCorrectionRequest $request)
    {
        $attendance = Attendance::findOrFail($request->attendance_id);

        AttendanceCorrectionRequest::create([
            'user_id'       => Auth::id(),
            'attendance_id' => $attendance->id,
            'reason'        => $request->reason,
            'date'          => $attendance->date,
            'start_time'    => \Carbon\Carbon::parse($attendance->date . ' ' . $request->start_time)->format('Y-m-d H:i:s'),
            'end_time'      => \Carbon\Carbon::parse($attendance->date . ' ' . $request->end_time)->format('Y-m-d H:i:s'),
            'status'        => 'pending',
        ]);

        return redirect()->route('attendance.show', $attendance->id)->with('message', '修正申請を送信しました。');
    }

    public function index()
    {
        $user_id = Auth::id();
        
        $pending_requests = AttendanceCorrectionRequest::where('user_id', $user_id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $approved_requests = AttendanceCorrectionRequest::where('user_id', $user_id)
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('stamp_correction_request_list', compact('pending_requests', 'approved_requests'));
    }

    public function adminIndex()
    {
        $pending_requests = AttendanceCorrectionRequest::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $approved_requests = AttendanceCorrectionRequest::with('user')
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.correction_request_list', compact('pending_requests', 'approved_requests'));
    }

    public function approveShow($id)
    {
        $request_data = AttendanceCorrectionRequest::with(['attendance.rests', 'user'])
                            ->findOrFail($id);
        
        $attendance = $request_data->attendance;
        return view('admin.correction_approve', compact('request_data', 'attendance'));
    }

    public function approve($id)
    {
        $correction_request = AttendanceCorrectionRequest::findOrFail($id);
        $attendance = Attendance::findOrFail($correction_request->attendance_id);

        if ($correction_request->status !== 'pending') {
            return redirect()->route('admin.correction.approve.show', $id)->with('message', 'この申請はすでに承認済みです。');
        }

        DB::transaction(function () use ($correction_request, $attendance) {
            $attendance->update([
                'start_time' => Carbon::parse($correction_request->start_time)->format('H:i:s'),
                'end_time'   => $correction_request->end_time
                    ? Carbon::parse($correction_request->end_time)->format('H:i:s')
                    : null,
            ]);

            $correction_request->update(['status' => 'approved']);
        });

        return redirect()->route('admin.correction.list')->with('message', '修正申請を承認しました。');
    }
}