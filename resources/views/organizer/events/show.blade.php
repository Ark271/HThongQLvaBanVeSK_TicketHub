@extends('organizer.layout')

@section('content')
<div class="container">
    <div class="d-flex gap-2 mb-3">
        <a href="{{ route('organizer.events.index') }}"
        class="btn btn-secondary">
            Quay lại
        </a>

        <a href="{{ route('organizer.events.edit', $event->id) }}"
        class="btn btn-warning">
            Sửa sự kiện
        </a>
    </div>

    @if (session('success'))
        <div id="flash-message" class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <h3>{{ $event->title }}</h3>

    @if ($event->image)
        <div class="mb-4">
            <img src="{{ asset('storage/' . $event->image) }}"
                alt="{{ $event->title }}"
                class="event-thumbnail">
        </div>
    @else
        <div class="alert alert-secondary">
            Sự kiện chưa có hình ảnh.
        </div>
    @endif

    <p>
        <b>Thời gian:</b>
        {{ $event->start_datetime->format('d/m/Y H:i') }}
        →
        {{ $event->end_datetime->format('d/m/Y H:i') }}
    </p>

    <p><b>Địa điểm:</b> {{ $event->location }}</p>
    <p><b>Số người tối đa:</b> {{ $event->max_participants }}</p>

    <p><b>Mô tả:</b></p>
    <p>{{ $event->description }}</p>

    <hr>

    <h5>Vé</h5>
    <ul>
        @foreach($event->tickets as $ticket)
            <li>
                {{ strtoupper($ticket->type) }} –
                {{ number_format($ticket->price) }}đ –
                SL: {{ $ticket->quantity }}
            </li>
        @endforeach
    </ul>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const flashMessage = document.getElementById('flash-message');

        if (!flashMessage) {
            return;
        }

        setTimeout(function () {
            flashMessage.style.transition = 'opacity 0.5s ease';
            flashMessage.style.opacity = '0';

            setTimeout(function () {
                flashMessage.remove();
            }, 500);
        }, 3000);
    });
</script>
@endsection
