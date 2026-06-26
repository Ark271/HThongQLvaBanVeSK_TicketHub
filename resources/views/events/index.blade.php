@extends('layouts.app')

@section('content')
<div class="events-page py-5">
    <div class="container">

        <h2 class="mb-4 text-white fw-bold">
            Sự kiện đang mở bán
        </h2>

        @forelse($events as $event)
            <div class="event-card d-flex justify-content-between align-items-center mb-3 p-4 rounded">

                <div>
                    <div class="text-muted small">
                        {{ \Carbon\Carbon::parse($event->start_date)->format('d/m/Y') }}
                    </div>

                    <h5 class="mb-1 text-white">
                        {{ $event->title }}
                    </h5>

                    <div class="text-secondary small">
                        {{ $event->location }}
                    </div>
                </div>

                <a href="{{ route('events.show', $event->id) }}"
                   class="btn btn-primary">
                    Mua vé
                </a>

            </div>
        @empty
            <p class="text-muted">Chưa có sự kiện nào.</p>
        @endforelse

        @if ($events->hasPages())
            <div class="dashboard-pagination mt-4">
                {{ $events->links() }}
            </div>
        @endif

    </div>
</div>
@endsection
