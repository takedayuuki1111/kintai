@extends('layouts.admin')

@section('title', '申請承認')

@section('content')
<div class="container-centered py-40 admin-approve-page">
    <h1 class="page-title page-title-with-bar">勤怠詳細</h1>

    <div class="content-card admin-approve-card">
    <div class="detail-form admin-approve-form">

        <div class="form-group">
            <div class="form-label">名前</div>
            <div class="form-content">{{ $request_data->user->name }}</div>
        </div>

        <div class="form-group">
            <div class="form-label">日付</div>
            <div class="form-content flex-align-center">
                <span>{{ \Carbon\Carbon::parse($request_data->date)->format('Y年') }}</span>
                <span>{{ \Carbon\Carbon::parse($request_data->date)->format('n月j日') }}</span>
            </div>
        </div>

        <div class="form-group">
            <div class="form-label">出勤・退勤</div>
            <div class="form-content flex-align-center">
                {{ \Carbon\Carbon::parse($request_data->start_time)->format('H:i') }}
                <span>～</span>
                {{ $request_data->end_time ? \Carbon\Carbon::parse($request_data->end_time)->format('H:i') : '' }}
            </div>
        </div>

        @php
            $rests = $attendance->rests ?? collect();
        @endphp

        @foreach($rests as $index => $rest)
            <div class="form-group">
                <div class="form-label">{{ $index === 0 ? '休憩' : '休憩' . ($index + 1) }}</div>
                <div class="form-content flex-align-center">
                    {{ $rest->start_time ? \Carbon\Carbon::parse($rest->start_time)->format('H:i') : '' }}
                    <span>～</span>
                    {{ $rest->end_time ? \Carbon\Carbon::parse($rest->end_time)->format('H:i') : '' }}
                </div>
            </div>
        @endforeach

        @for($i = $rests->count(); $i < 2; $i++)
            <div class="form-group">
                <div class="form-label">{{ $i === 0 ? '休憩' : '休憩' . ($i + 1) }}</div>
                <div class="form-content flex-align-center">
                    <span></span>
                </div>
            </div>
        @endfor

        <div class="form-group">
            <div class="form-label">備考</div>
            <div class="form-content">
                <p class="small-muted request-reason-box admin-approve-reason">
                    {{ $request_data->reason }}
                </p>
            </div>
        </div>

    </div>
</div>

    <div class="admin-approve-action">
        @if($request_data->status === 'pending')
            <form action="{{ route('admin.correction.approve', $request_data->id) }}" method="post">
                @csrf
                <button type="submit" class="btn-black">承認する</button>
            </form>
        @else
            <button type="button" class="btn-black" disabled>承認済み</button>
        @endif
    </div>
</div>
@endsection