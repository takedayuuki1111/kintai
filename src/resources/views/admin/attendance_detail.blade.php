@extends('layouts.admin')

@section('title', '勤怠詳細')

@section('content')
<div class="container-centered py-40 admin-attendance-detail-page">
    <h1 class="page-title page-title-with-bar">勤怠詳細</h1>

    @if(session('message'))
        <p class="message-success">{{ session('message') }}</p>
    @endif
    @if($errors->any())
        <div class="error-message">
            @foreach($errors->all() as $error)
                <p class="mb-0">{{ $error }}</p>
            @endforeach
        </div>
    @endif
    @error('update')
        <p class="error-message">{{ $message }}</p>
    @enderror

    <form action="{{ route('admin.attendance.update', $attendance->id) }}" method="POST" class="detail-form admin-attendance-detail-form">
        @csrf
        @method('PUT')
        <input type="hidden" name="redirect_staff_id" value="{{ old('redirect_staff_id', request('staff_id')) }}">
        <input type="hidden" name="redirect_month" value="{{ old('redirect_month', request('month')) }}">
        @php
            $date = $attendance->date;
            $restRows = max(2, $attendance->rests->count());
        @endphp

        <input type="hidden" name="year" value="{{ old('year', $date->year) }}">
        <input type="hidden" name="month" value="{{ old('month', sprintf('%02d', $date->month)) }}">
        <input type="hidden" name="day" value="{{ old('day', sprintf('%02d', $date->day)) }}">

    <div class="content-card admin-attendance-detail-card">

        <div class="form-group">
            <div class="form-label">名前</div>
            <div class="form-content">{{ $attendance->user->name }}</div>
        </div>

        <div class="form-group">
            <div class="form-label">日付</div>
            <div class="form-content admin-attendance-date-display">
                <span>{{ old('year', $date->year) }}年</span>
                <span>{{ ltrim((string) old('month', sprintf('%02d', $date->month)), '0') }}月{{ ltrim((string) old('day', sprintf('%02d', $date->day)), '0') }}日</span>
            </div>
            @error('year')
                <p class="error-message text-left mt-5 mb-0">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <div class="form-label">出勤・退勤</div>
            <div class="form-content flex-align-center">
                <input type="time" name="start_time" value="{{ old('start_time', $attendance->start_time->format('H:i')) }}">
                <span>～</span>
                <input type="time" name="end_time" value="{{ old('end_time', $attendance->end_time ? $attendance->end_time->format('H:i') : '') }}">
            </div>
            @error('start_time')
                <p class="error-message text-left mt-5 mb-0">{{ $message }}</p>
            @enderror
            @error('end_time')
                <p class="error-message text-left mt-5 mb-0">{{ $message }}</p>
            @enderror
        </div>

        @for($index = 0; $index < $restRows; $index++)
        @php $rest = $attendance->rests[$index] ?? null; @endphp
        <div class="form-group">
            <div class="form-label">{{ $index === 0 ? '休憩' : '休憩'.($index + 1) }}</div>
            <div class="form-content flex-align-center">
                @if($rest)
                    <input type="hidden" name="rests[{{ $index }}][id]" value="{{ $rest->id }}">
                @endif
                <input type="time" name="rests[{{ $index }}][start_time]" value="{{ old('rests.'.$index.'.start_time', $rest && $rest->start_time ? $rest->start_time->format('H:i') : '') }}">
                <span>～</span>
                <input type="time" name="rests[{{ $index }}][end_time]" value="{{ old('rests.'.$index.'.end_time', $rest && $rest->end_time ? $rest->end_time->format('H:i') : '') }}">
            </div>
            @error('rests.'.$index.'.start_time')
                <p class="error-message text-left mt-5 mb-0">{{ $message }}</p>
            @enderror
            @error('rests.'.$index.'.end_time')
                <p class="error-message text-left mt-5 mb-0">{{ $message }}</p>
            @enderror
        </div>
        @endfor

        <div class="form-group">
            <div class="form-label">備考</div>
            <div class="form-content">
                <textarea class="admin-attendance-remark" rows="2" readonly></textarea>
            </div>
        </div>

    </div>

        <div class="admin-attendance-detail-action-row">
            <div class="admin-attendance-detail-action">
                <button type="submit" class="btn-black">修正</button>
            </div>
        </div>
    </form>
</div>
@endsection