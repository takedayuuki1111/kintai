@extends('layouts.admin')

@section('title', 'スタッフ一覧')

@section('content')
<div class="container-centered py-40 admin-staff-page">
    <h1 class="page-title page-title-with-bar">スタッフ一覧</h1>

    <div class="content-card admin-staff-card">

        <table class="attendance-table">
            <thead>
                <tr>
                    <th>名前</th>
                    <th>メールアドレス</th>
                    <th>月次勤怠</th>
                </tr>
            </thead>
            <tbody>
                @forelse($staffs as $staff)
                    <tr>
                        <td>{{ $staff->name }}</td>
                        <td>{{ $staff->email }}</td>
                        <td>
                            <a href="{{ route('admin.staff.attendance', ['id' => $staff->id]) }}" class="link-detail">詳細</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-40 text-muted">スタッフが登録されていません</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection