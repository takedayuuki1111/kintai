@extends('layouts.admin')

@section('title', '勤怠一覧')

@section('content')
<div class="container-centered py-40">
    <div class="content-card">
    <h1 class="page-title">勤怠一覧</h1>

    <div class="flex-align-center gap-20 mb-30">
        <a href="{{ route('admin.staff.attendance', ['id' => $user->id, 'month' => $currentDate->copy()->subMonth()->format('Y-m')]) }}" class="month-nav-link">← 前月</a>
        <span class="month-display">{{ $currentDate->format('Y/m') }}</span>
        <a href="{{ route('admin.staff.attendance', ['id' => $user->id, 'month' => $currentDate->copy()->addMonth()->format('Y-m')]) }}" class="month-nav-link">翌月 →</a>
        <a href="{{ route('admin.staff.attendance.export', ['id'=>$user->id,'month'=>$currentDate->format('Y-m')]) }}" class="btn-outline">CSV出力</a>
    </div>

    <div class="message-area">
        @if (session('error'))
            <p class="error-message">{{ session('error') }}</p>
        @endif
    </div>

    <table class="attendance-table mt-20">
        <thead>
            <tr class="no-border">
                <th class="text-left w-25">日付</th>
                <th class="w-15">出勤</th>
                <th class="w-15">退勤</th>
                <th class="w-15">休憩</th>
                <th class="w-15">合計</th>
                <th class="w-15">詳細</th>
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
                    <td class="text-left">{{ $date->format('m/d') }}({{ $dayOfWeek }})</td>
                    
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
</div>
@endsection