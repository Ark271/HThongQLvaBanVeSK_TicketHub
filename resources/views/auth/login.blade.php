@extends('layouts.app')

@section('content')

@if(session('success'))
    <div class="container">
        <div id="flash-message" class="alert alert-success text-center mt-3 mb-0">
            {{ session('success') }}
        </div>
    </div>
@endif

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">

            <div class="card shadow">
                <div class="card-header text-center fw-bold">
                    Đăng nhập
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror"
                                required
                                autofocus
                            >

                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                required
                            >

                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input
                                id="remember"
                                type="checkbox"
                                class="form-check-input"
                                name="remember"
                                {{ old('remember') ? 'checked' : '' }}
                            >
                            <label for="remember" class="form-check-label">Ghi nhớ đăng nhập</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Đăng nhập
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <span>Chưa có tài khoản?</span>
                        <a href="{{ route('register') }}" class="fw-bold text-decoration-none">
                            Đăng ký ngay
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const flashMessage = document.getElementById('flash-message');

        if (flashMessage) {
            setTimeout(() => {
                flashMessage.style.transition = 'all 0.6s ease';
                flashMessage.style.opacity = '0';
                flashMessage.style.transform = 'translateY(-10px)';

                setTimeout(() => {
                    flashMessage.remove();
                }, 600);
            }, 4000);
        }
    });
</script>

@endsection
