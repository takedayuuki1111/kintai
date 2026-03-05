@extends('layouts.app')

@section('title', '会員登録')
@section('body-class', 'login-page-body')
@section('main-class', 'main-center')

@section('content')
<div class="auth-center">
    <div class="login-card">
    <h1 class="page-title">会員登録</h1>

    <form action="{{ route('register') }}" method="post" novalidate>
        @csrf

        <p class="auth-field-label">名前</p>
        <div class="form-group flex-column mb-20">
            <input type="text" name="name" value="{{ old('name') }}" placeholder="名前" required autofocus class="auth-input">
            @error('name')
                <p class="error-message text-left mt-5 mb-0">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <p class="auth-field-label">メールアドレス</p>
        <div class="form-group flex-column mb-20">
            <input type="email" name="email" value="{{ old('email') }}" placeholder="メールアドレス" required class="auth-input">
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

        <p class="auth-field-label">パスワード確認</p>
        <div class="form-group flex-column mb-20">
            <input type="password" name="password_confirmation" placeholder="パスワード確認" required class="auth-input">
        </div>
        
        <button type="submit" class="submit-btn auth-button mt-10">登録する</button>
    </form>

    <div class="auth-link-area">
        <p class="auth-link-text">アカウントをお持ちの方はこちら</p>
        <a href="{{ route('login') }}" class="auth-link">ログイン</a>
    </div>
    </div>
</div>
@endsection