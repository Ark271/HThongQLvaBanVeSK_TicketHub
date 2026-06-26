<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang đơn vị tổ chức</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/events.css') }}">
</head>

<body>
<div class="d-flex">
    <div class="bg-dark text-white p-3" style="width: 240px; min-height: 100vh;">
        <h4>ORGANIZER</h4>

        <a href="{{ route('organizer.dashboard') }}" class="d-block text-white mt-4 text-decoration-none">
            Dashboard
        </a>

        <a href="{{ route('organizer.events.index') }}" class="d-block text-white mt-3 text-decoration-none">
            Quản lý sự kiện
        </a>

        <a href="{{ route('home') }}" class="d-block text-white mt-3 text-decoration-none">
            Website
        </a>

        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button class="btn btn-danger w-100">Đăng xuất</button>
        </form>
    </div>

    <div class="p-4 flex-grow-1">
        @yield('content')
    </div>
</div>
</body>
</html>
