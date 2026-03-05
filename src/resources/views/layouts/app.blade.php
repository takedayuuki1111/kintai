<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coachtech - @yield('title', '勤怠管理システム')</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/overrides.css') }}?v={{ time() }}">
</head>
<body class="@yield('body-class')">
    <header class="header">
        <div class="header-logo">
            <a href="{{ route('attendance.index') }}">
                <img src="{{ asset('img/logo.png') }}" alt="Coachtech" class="header-logo-img">
            </a>
        </div>
        
        @auth
        <nav class="header-nav">
            <ul>
                <li><a href="{{ route('attendance.index') }}">ホーム</a></li>
                <li><a href="{{ route('attendance.list') }}">日付一覧</a></li>
                <li><a href="{{ route('correction.list') }}">申請一覧</a></li>
                <li>
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button type="submit">ログアウト</button>
                    </form>
                </li>
            </ul>
        </nav>
        @endauth
    </header>

    <main class="main @yield('main-class')">
        <div class="site-inner">
            @yield('content')
        </div>
    </main>

    <footer class="footer">
        <small>Coachtech</small>
    </footer>

    @stack('scripts')
</body>
</html>