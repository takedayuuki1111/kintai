@extends('layouts.app')

@section('title', 'ログイン')
@section('body-class', 'login-page-body')
@section('main-class', 'main-center')

@section('content')
<div class="auth-center">
    <div class="login-card">
    <h1 class="page-title">ログイン</h1>

    <form action="{{ route('login') }}" method="post" novalidate>
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
        
        <button type="submit" class="submit-btn auth-button mt-10">ログイン</button>
    </form>

    <div class="auth-link-area">
        <p class="auth-link-text">アカウントをお持ちでない方はこちら</p>
        <a href="{{ route('register') }}" class="auth-link">会員登録</a>
    </div>
    </div>
</div>
@endsection