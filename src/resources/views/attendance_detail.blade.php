@extends('layouts.app')

@section('title', '勤怠詳細')

@section('content')
<div class="detail-form">
    <h1 class="page-title">勤怠詳細</h1>

    @if(session('message'))
        <p class="message-success">{{ session('message') }}</p>
    @endif

    <div class="form-section content-card">
    <form action="{{ route('correction.store') }}" method="POST">
        @csrf
        <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">

        <div class="form-group">
            <div class="form-label">名前</div>
            <div class="form-content">{{ $attendance->user->name }}</div>
        </div>

        <div class="form-group">
            <div class="form-label">日付</div>
            <div class="form-content flex-align-center">
                @php
                    $date = $attendance->date;
                @endphp
                <input type="text" name="year" class="input-year" value="{{ old('year', $date->year) }}" {{ $is_pending ? 'readonly' : '' }}> 年
                <input type="text" name="month" class="input-month" value="{{ old('month', sprintf('%02d', $date->month)) }}" {{ $is_pending ? 'readonly' : '' }}> 月
                <input type="text" name="day" class="input-day" value="{{ old('day', sprintf('%02d', $date->day)) }}" {{ $is_pending ? 'readonly' : '' }}> 日
            </div>
            @error('year') <p class="error-message text-left mt-5 mb-0">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <div class="form-label">出勤・退勤</div>
            <div class="form-content flex-align-center">
                <input type="time" name="start_time" value="{{ old('start_time', $attendance->start_time->format('H:i')) }}" {{ $is_pending ? 'readonly' : '' }}>
                <span>～</span>
                <input type="time" name="end_time" value="{{ old('end_time', $attendance->end_time ? $attendance->end_time->format('H:i') : '') }}" {{ $is_pending ? 'readonly' : '' }}>
            </div>
            @error('start_time') <p class="error-message text-left mt-5 mb-0">{{ $message }}</p> @enderror
            @error('end_time') <p class="error-message text-left mt-5 mb-0">{{ $message }}</p> @enderror
        </div>

        @foreach($attendance->rests as $index => $rest)
        <div class="form-group">
            <div class="form-label">休憩{{ $index + 1 }}</div>
            <div class="form-content flex-align-center">
                <input type="hidden" name="rests[{{ $index }}][id]" value="{{ $rest->id }}">
                <input type="time" name="rests[{{ $index }}][start_time]" value="{{ old('rests.'.$index.'.start_time', $rest->start_time->format('H:i')) }}" {{ $is_pending ? 'readonly' : '' }}>
                <span>～</span>
                <input type="time" name="rests[{{ $index }}][end_time]" value="{{ old('rests.'.$index.'.end_time', $rest->end_time ? $rest->end_time->format('H:i') : '') }}" {{ $is_pending ? 'readonly' : '' }}>
            </div>
            @error('rests.'.$index.'.start_time') <p class="error-message text-left mt-5 mb-0">{{ $message }}</p> @enderror
            @error('rests.'.$index.'.end_time') <p class="error-message text-left mt-5 mb-0">{{ $message }}</p> @enderror
        </div>
        @endforeach

        <div class="form-group no-border">
            <div class="form-label">備考</div>
            <div class="form-content">
                <textarea name="reason" rows="4" placeholder="修正理由を入力してください" {{ $is_pending ? 'readonly' : '' }}>{{ old('reason') }}</textarea>
                @error('reason') <p class="error-message text-left mt-5 mb-0">{{ $message }}</p> @enderror
            </div>
        </div>

        @if($is_pending)
            <p class="pending-message">＊承認待ちのため修正できません。</p>
        @else
            <div class="detail-action-right">
                <button type="submit" class="btn-black">修正</button>
            </div>
        @endif
    </form>
    </div>
</div>
@endsection