@extends('layouts.app')

@section('title', '管理者ログイン')
@section('body-class', 'login-page-body')
@section('main-class', 'main-center')

@section('content')
<div class="auth-center">
    <div class="login-card">
    <h1 class="page-title">管理者ログイン</h1>

    <form action="{{ route('admin.login.store') }}" method="post" novalidate>
        @csrf

        <p class="auth-field-label">メールアドレス</p>
        <div class="form-group flex-column mb-20">
            <input type="email" name="email" value="{{ old('email') }}" placeholder="メールアドレス" required autofocus class="auth-input">
            @error('email')
                <p class="error-message text-left mt-5 mb-0">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <p class="auth-field-label">パスワード</p>
        <div class="form-group flex-column mb-20">
            <input type="password" name="password" placeholder="パスワード" required class="auth-input">
            @error('password')
                <p class="error-message text-left mt-5 mb-0">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <button type="submit" class="submit-btn auth-button mt-10">管理者ログインする</button>
    </form>
    </div>
</div>
@endsection