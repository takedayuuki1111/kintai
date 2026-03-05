@extends('layouts.app')

@section('title', 'メール認証')
@section('body-class', 'login-page-body')
@section('main-class', 'main-center')

@section('content')
<div class="auth-center">
    <div class="content-card">
        <h1 class="page-title">メール認証が必要です</h1>
        <p>登録したメールアドレスに認証リンクを送信しました。確認してください。</p>
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-black mt-20">認証メールを再送する</button>
        </form>
    </div>
</div>
@endsection
