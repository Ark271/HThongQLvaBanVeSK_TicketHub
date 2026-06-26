<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/events.css') }}">
</head>
<body>

<div class="d-flex" style="min-height:100vh">

    <!-- SIDEBAR -->
    <aside class="bg-dark text-white p-3" style="width:250px">
        <h4 class="mb-4">ADMIN</h4>

        <ul class="nav flex-column gap-2">

            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}"
                   class="nav-link text-white">
                    Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.events.index') }}"
                   class="nav-link text-white">
                    Sự kiện
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.users.index') }}"
                    class="nav-link text-white">
                    Người dùng
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('home') }}"
                   class="nav-link text-white">
                    Website
                </a>
            </li>

            <li class="nav-item mt-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-danger w-100">
                        Đăng xuất
                    </button>
                </form>
            </li>

        </ul>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-fill p-4 bg-light">
        @yield('content')
    </main>

</div>

</body>
</html>
