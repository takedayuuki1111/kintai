@extends('layouts.app')

@section('title', '申請一覧')

@section('content')
<div class="container-centered">
    <h1 class="page-title">申請一覧</h1>

    @php
        $tabs = [
            [
                'id' => 'pending-pane',
                'label' => '承認待ち',
                'active' => true,
                'rows' => $pending_requests,
                'statusClass' => 'status-pending',
                'statusText' => '承認待ち',
                'emptyText' => '承認待ちの申請はありません。',
            ],
            [
                'id' => 'approved-pane',
                'label' => '承認済み',
                'active' => false,
                'rows' => $approved_requests,
                'statusClass' => 'status-approved',
                'statusText' => '承認済み',
                'emptyText' => '承認済みの申請はありません。',
            ],
        ];
    @endphp

    <div class="message-area">
        @if (session('message'))
            <p class="message-success">{{ session('message') }}</p>
        @endif
    </div>

    <div class="request-tabs" role="tablist" aria-label="申請一覧タブ">
        @foreach($tabs as $tab)
            <button type="button" class="request-tab{{ $tab['active'] ? ' is-active' : '' }}" data-target="{{ $tab['id'] }}">{{ $tab['label'] }}</button>
        @endforeach
    </div>

    @foreach($tabs as $tab)
        <div id="{{ $tab['id'] }}" class="request-pane{{ $tab['active'] ? ' is-active' : '' }}">
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th>状態</th>
                        <th>名前</th>
                        <th>対象日時</th>
                        <th>申請理由</th>
                        <th>詳細</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tab['rows'] as $request)
                    <tr>
                        <td class="{{ $tab['statusClass'] }}">{{ $tab['statusText'] }}</td>
                        <td>{{ $request->user->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($request->date)->format('Y/m/d') }}</td>
                        <td>{{ Str::limit($request->reason, 20) }}</td>
                        <td><a href="{{ route('attendance.show', $request->attendance_id) }}" class="link-detail">詳細</a></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">{{ $tab['emptyText'] }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endforeach
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var tabs = document.querySelectorAll('.request-tab');
    var panes = document.querySelectorAll('.request-pane');

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (item) { item.classList.remove('is-active'); });
            panes.forEach(function (pane) { pane.classList.remove('is-active'); });

            tab.classList.add('is-active');
            var pane = document.getElementById(tab.dataset.target);
            if (pane) {
                pane.classList.add('is-active');
            }
        });
    });
});
</script>
@endpush