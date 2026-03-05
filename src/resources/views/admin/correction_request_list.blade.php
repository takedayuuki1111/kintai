@extends('layouts.admin')

@section('title', '申請一覧')

@section('content')
<div class="container-centered py-40">
    <h1 class="page-title">申請一覧</h1>

    <div class="message-area">
        @if (session('message'))
            <p class="message-success">{{ session('message') }}</p>
        @endif
    </div>

    <div class="request-tabs" role="tablist" aria-label="申請一覧タブ">
        <button type="button" class="request-tab is-active" data-target="admin-pending-pane">承認待ち</button>
        <button type="button" class="request-tab" data-target="admin-approved-pane">承認済み</button>
    </div>

    <div id="admin-pending-pane" class="request-pane is-active">
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
                @forelse($pending_requests as $request)
                <tr>
                    <td class="status-pending">承認待ち</td>
                    <td>{{ $request->user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($request->date)->format('Y/m/d') }}</td>
                    <td>{{ Str::limit($request->reason, 20) }}</td>
                    <td>
                        <a href="{{ route('admin.correction.approve.show', $request->id) }}" class="link-detail">詳細</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">承認待ちの申請はありません。</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="admin-approved-pane" class="request-pane">
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
                @forelse($approved_requests as $request)
                <tr>
                    <td class="status-approved">承認済み</td>
                    <td>{{ $request->user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($request->date)->format('Y/m/d') }}</td>
                    <td>{{ Str::limit($request->reason, 20) }}</td>
                    <td>
                        <a href="{{ route('admin.correction.approve.show', $request->id) }}" class="link-detail">詳細</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">承認済みの申請はありません。</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
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