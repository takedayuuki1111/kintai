@extends('layouts.admin')

@section('title', '勤怠一覧')

@section('content')
<div class="container-centered py-40 attendance-list-page admin-attendance-page">
    <h1 class="page-title page-title-with-bar">{{ $currentDate->format('Y年n月j日') }}の勤怠</h1>

    <div class="admin-date-nav mb-30">
        <a href="{{ route('admin.attendance.list', ['date' => $currentDate->copy()->subDay()->format('Y-m-d')]) }}" class="admin-date-link">← 前日</a>
        <div class="admin-date-center">
            <span class="admin-date-icon">📅</span>
            <span class="month-display">{{ $currentDate->format('Y/m/d') }}</span>
        </div>
        <a href="{{ route('admin.attendance.list', ['date' => $currentDate->copy()->addDay()->format('Y-m-d')]) }}" class="admin-date-link">翌日 →</a>
    </div>

    <div class="content-card admin-attendance-card">
    <table class="attendance-table mt-20">
        <thead>
            <tr class="no-border">
                <th>名前</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $attendance)
                <tr>
                    <td class="font-bold">{{ $attendance->user->name }}</td>
                    <td>{{ optional($attendance->start_time)->format('H:i') }}</td>
                    <td>{{ $attendance->end_time ? optional($attendance->end_time)->format('H:i') : '' }}</td>
                    <td>{{ $attendance->total_rest }}</td>
                    <td>{{ $attendance->total_work }}</td>
                    <td>
                        <a href="{{ route('admin.attendance.show', $attendance->id) }}" class="link-detail">詳細</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-40 text-muted">
                        この日の打刻データはありません
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
@endsection