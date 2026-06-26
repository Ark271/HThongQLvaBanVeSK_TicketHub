@extends('admin.layout')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h3>Danh sách sự kiện của tôi</h3>

        <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
            + Thêm sự kiện
        </a>
    </div>

    @if(session('success'))
        <div id="flash-message" class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>STT</th>
                <th>Tên sự kiện</th>
                <th>Bắt đầu</th>
                <th>Kết thúc</th>
                <th>Hành động</th>
            </tr>
        </thead>

        <tbody>
        @forelse($events as $index => $event)
            <tr>
                <td>{{ $events->firstItem() + $index }}</td>
                <td>{{ $event->title }}</td>
                <td>{{ $event->start_datetime?->format('d/m/Y H:i') }}</td>
                <td>{{ $event->end_datetime?->format('d/m/Y H:i') }}</td>
                <td>
                    <a href="{{ route('admin.events.show', $event) }}" class="btn btn-sm btn-info">
                        Xem
                    </a>

                    <form action="{{ route('admin.events.destroy', $event) }}"
                          method="POST"
                          class="d-inline"
                          onsubmit="return confirm('Xóa sự kiện này?')">
                        @csrf
                        @method('DELETE')

                        <button class="btn btn-sm btn-danger">
                            Xóa
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">Bạn chưa tạo sự kiện nào</td>
            </tr>
        @endforelse
        </tbody>
    </table>
    @if ($events->hasPages())
        <div class="dashboard-pagination mt-3">
            {{ $events->links() }}
        </div>
    @endif
</div>
@endsection
