@extends('layouts.admin')

@section('title', '勤怠一覧')

@section('content')
<div class="container-centered py-40 admin-staff-attendance-page">
    <h1 class="page-title page-title-with-bar">{{ $user->name }}さんの勤怠</h1>

    @if(session('message'))
        <p class="message-success">{{ session('message') }}</p>
    @endif

    <div class="admin-date-nav mb-30">
        <a href="{{ route('admin.staff.attendance', ['id' => $user->id, 'month' => $currentDate->copy()->subMonth()->format('Y-m')]) }}" class="admin-date-link">← 前月</a>
        <div class="admin-date-center">
            <span class="admin-date-icon">📅</span>
            <span class="month-display">{{ $currentDate->format('Y/m') }}</span>
        </div>
        <a href="{{ route('admin.staff.attendance', ['id' => $user->id, 'month' => $currentDate->copy()->addMonth()->format('Y-m')]) }}" class="admin-date-link">翌月 →</a>
    </div>

    <div class="content-card admin-staff-attendance-card">
    <table class="attendance-table">
        <thead>
            <tr class="no-border">
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach($monthlyData as $data)
                @php
                    $date = \Carbon\Carbon::parse($data['date']);
                    $dayOfWeek = ['日', '月', '火', '水', '木', '金', '土'][$date->dayOfWeek];
                    $attendance = $data['attendance'];
                @endphp
                <tr>
                    <td>{{ $date->format('m/d') }}({{ $dayOfWeek }})</td>
                    
                    @if($attendance)
                        <td>{{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }}</td>
                        <td>{{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '' }}</td>
                        <td>{{ $data['rest_time'] }}</td>
                        <td>{{ $data['work_time'] }}</td>
                        <td>
                            <a href="{{ route('admin.attendance.show', ['attendance' => $attendance->id, 'staff_id' => $user->id, 'month' => $currentDate->format('Y-m')]) }}" class="link-detail">詳細</a>
                        </td>
                    @else
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    </div>

    <div class="staff-attendance-csv-wrap">
        <a href="{{ route('admin.staff.attendance.export', ['id'=>$user->id,'month'=>$currentDate->format('Y-m')]) }}" class="btn-black staff-attendance-csv-btn">CSV出力</a>
    </div>
</div>
@endsection