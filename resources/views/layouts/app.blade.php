<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>TICKETHUB MANAGER</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/events.css') }}">

</head>
<body class="layout-root">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg shadow-sm">
    <div class="container">

        <!-- LOGO -->
        <a class="navbar-brand" href="{{ route('home') }}">
            TICKETHUB MANAGER
        </a>

        <button class="navbar-toggler bg-light" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto align-items-lg-center">

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">Trang chủ</a>
                </li>

                <li class="nav-item">
                    @auth
                        <a class="nav-link" href="{{ route('events.index') }}">Mua vé</a>
                    @else
                        <a class="nav-link" href="{{ route('login') }}">Mua vé</a>
                    @endauth
                </li>

                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            {{ Auth::user()->name }}
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile') }}">
                                    Tài khoản
                                </a>
                            </li>

                            <li><hr class="dropdown-divider"></li>
                            @if (Auth::user()->role === 'admin')
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        Trang quản trị
                                    </a>
                                </li>
                            @elseif (Auth::user()->role === 'organizer')
                                <li>
                                    <a class="dropdown-item" href="{{ route('organizer.dashboard') }}">
                                        Trang đơn vị tổ chức
                                    </a>
                                </li>
                            @endif

                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item text-danger">
                                        Đăng xuất
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="btn btn-login ms-3" href="{{ route('login') }}">
                            Đăng nhập
                        </a>
                    </li>
                @endauth

            </ul>
        </div>
    </div>
</nav>

<!-- NỘI DUNG TRANG -->
<main class="layout-content hero-section">
    @yield('content')
</main>

<!--FOOTER-->
<footer class="site-footer">
    <div class="container">
        <div class="row gy-4">

            <div class="col-md-4">
                <h5 class="footer-title">TICKETHUB MANAGER</h5>
                <p class="footer-text">
                    Nền tảng quản lý & đăng ký sự kiện trực tuyến
                    nhanh chóng – tiện lợi – an toàn.
                </p>
            </div>

            <div class="col-md-4">
                <h5 class="footer-title">Liên kết</h5>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}">Trang chủ</a></li>
                    <li><a href="{{ route('events.index') }}">Mua vé</a></li>
                    @auth
                        <li><a href="{{ route('profile') }}">Tài khoản</a></li>
                    @else
                        <li><a href="{{ route('login') }}">Đăng nhập</a></li>
                    @endauth
                </ul>
            </div>

            <div class="col-md-4">
                <h5 class="footer-title">Liên hệ</h5>
                <p class="footer-text mb-1">📧 Email: support@eventmanager.com</p>
                <p class="footer-text mb-1">📞 Hotline: 0123 456 789</p>
                <p class="footer-text">📍 Việt Nam</p>
            </div>

        </div>

        <hr class="footer-divider">

        <div class="text-center footer-copy">
            © {{ date('Y') }} EVENT MANAGER. All rights reserved.
        </div>
    </div>
</footer>

</body>
</html>
