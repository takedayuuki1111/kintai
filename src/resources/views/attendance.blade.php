@extends('layouts.app')

@section('title', 'ホーム')

@section('content')
<div class="attendance-container">
    <div class="status-display">
        <span class="status-label">{{ $status_text }}</span>
    </div>

    <p class="user-greeting">{{ Auth::user()->name }}さんお疲れ様です！</p>

    <div class="message-area">
        @if (session('message'))
            <p class="message-success">{{ session('message') }}</p>
        @endif
        @if ($status === 3)
            <p class="message-success">お疲れ様でした。</p>
        @endif
    </div>

    <div class="current-date-time">
        <p id="current-date" class="date-text"></p>
        <p id="current-time" class="time-text"></p>
    </div>

    <div class="attendance-panel">
        @if ($status === 0)
            <form action="{{ route('attendance.start') }}" method="post">
                @csrf
                <button type="submit" class="attendance-card">出勤</button>
            </form>
        @endif

        @if ($status === 1)
            <div class="button-row">
                <form action="{{ route('attendance.end') }}" method="post">
                    @csrf
                    <button type="submit" class="attendance-card">勤務終了</button>
                </form>
                <form action="{{ route('attendance.rest.start') }}" method="post">
                    @csrf
                    <button type="submit" class="attendance-card">休憩入</button>
                </form>
            </div>
        @endif

        @if ($status === 2)
            <form action="{{ route('attendance.rest.end') }}" method="post">
                @csrf
                <button type="submit" class="attendance-card">休憩戻</button>
            </form>
        @endif
    </div>
</div>
@endsection