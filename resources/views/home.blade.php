@extends('layouts.app')

@section('content')
<section class="hero-section">
    <div class="container py-5">
        <div class="row align-items-center">

            <!-- TEXT -->
            <div class="col-md-6">
                <h1 class="fw-bold display-5">
                    THAM GIA<br>
                    sự kiện của chúng tôi<br>
                    tại đây
                </h1>

                <p class="text-muted my-4">
                    Hệ thống quản lý sự kiện của bạn
                </p>

                @auth
                    <a href="{{ route('events.index') }}" class="btn btn-warning btn-lg">
                        MUA VÉ NGAY
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-warning btn-lg">
                        MUA VÉ NGAY
                    </a>
                @endauth
            </div>

            <!-- HERO CAROUSEL -->
            <div class="col-md-6">
                <div id="heroCarousel"
                     class="carousel slide carousel-fade hero-carousel"
                     data-bs-ride="carousel"
                     data-bs-interval="3000">

                    <div class="carousel-inner">

                        <div class="carousel-item active">
                            <img src="{{ asset('images/banner1.jpg') }}"
                                 class="hero-img"
                                 alt="Banner 1">
                        </div>

                        <div class="carousel-item">
                            <img src="{{ asset('images/banner2.jpg') }}"
                                 class="hero-img"
                                 alt="Banner 2">
                        </div>

                        <div class="carousel-item">
                            <img src="{{ asset('images/banner3.jpg') }}"
                                 class="hero-img"
                                 alt="Banner 3">
                        </div>

                        @foreach ($featuredEvents as $event)
                            <div class="carousel-item">
                                <a href="{{ route('events.show', $event->id) }}"
                                class="featured-event-slide">

                                    <img src="{{ asset('storage/' . $event->image) }}"
                                        class="hero-img"
                                        alt="{{ $event->title }}">

                                </a>
                            </div>
                        @endforeach

                    </div>

                </div>
            </div>

        </div>
    </div>
</section>
@endsection
