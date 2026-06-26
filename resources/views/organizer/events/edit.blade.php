@extends('organizer.layout')

@section('content')
<div class="container">
    <a href="{{ route('organizer.events.show', $event->id) }}"
       class="btn btn-secondary mb-3">
        Quay lại
    </a>

    <h2 class="mb-4">Sửa sự kiện</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ route('organizer.events.update', $event->id) }}"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Tên sự kiện</label>
            <input type="text"
                   name="title"
                   class="form-control"
                   value="{{ old('title', $event->title) }}"
                   required>
        </div>

        <div class="row">
            <div class="col-md-6">
                <label class="form-label">Bắt đầu</label>
                <input type="datetime-local"
                       name="start_datetime"
                       class="form-control"
                       value="{{ old('start_datetime', $event->start_datetime->format('Y-m-d\TH:i')) }}"
                       required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Kết thúc</label>
                <input type="datetime-local"
                       name="end_datetime"
                       class="form-control"
                       value="{{ old('end_datetime', $event->end_datetime->format('Y-m-d\TH:i')) }}"
                       required>
            </div>
        </div>

        <div class="mt-3">
            <label class="form-label">Địa điểm</label>
            <input type="text"
                   name="location"
                   class="form-control"
                   value="{{ old('location', $event->location) }}"
                   required>
        </div>

        <div class="mt-3">
            <label class="form-label">Số người tối đa</label>
            <input type="number"
                   name="max_participants"
                   class="form-control"
                   min="1"
                   value="{{ old('max_participants', $event->max_participants) }}"
                   required>
        </div>

        <div class="mt-3">
            <label class="form-label">Mô tả</label>
            <textarea name="description"
                      class="form-control"
                      rows="4">{{ old('description', $event->description) }}</textarea>
        </div>

        <div class="mt-3">
            <label class="form-label">Hình ảnh mới</label>

            @if ($event->image)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $event->image) }}"
                         alt="{{ $event->title }}"
                         style="width: 240px; height: 150px; object-fit: cover;"
                         class="rounded">
                </div>
            @endif

            <input type="file"
                   name="image"
                   class="form-control"
                   accept="image/jpeg,image/png,image/jpg,image/webp">

            <small class="text-muted">
                Để trống nếu muốn giữ ảnh hiện tại.
            </small>
        </div>

        @php
            $normalTicket = $event->tickets->firstWhere('type', 'normal');
            $vipTicket = $event->tickets->firstWhere('type', 'vip');
        @endphp

        <hr>

        <h5>Vé thường</h5>

        <div class="row">
            <div class="col-md-6">
                <label class="form-label">Giá vé</label>
                <input type="number"
                       name="ticket_normal_price"
                       class="form-control"
                       min="0"
                       value="{{ old('ticket_normal_price', $normalTicket?->price ?? 0) }}"
                       required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Số lượng còn lại</label>
                <input type="number"
                       name="ticket_normal_qty"
                       class="form-control"
                       min="0"
                       value="{{ old('ticket_normal_qty', $normalTicket?->quantity ?? 0) }}"
                       required>
            </div>
        </div>

        <h5 class="mt-3">Vé VIP</h5>

        <div class="row">
            <div class="col-md-6">
                <label class="form-label">Giá vé</label>
                <input type="number"
                       name="ticket_vip_price"
                       class="form-control"
                       min="0"
                       value="{{ old('ticket_vip_price', $vipTicket?->price ?? 0) }}"
                       required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Số lượng còn lại</label>
                <input type="number"
                       name="ticket_vip_qty"
                       class="form-control"
                       min="0"
                       value="{{ old('ticket_vip_qty', $vipTicket?->quantity ?? 0) }}"
                       required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-4">
            Cập nhật sự kiện
        </button>
    </form>
</div>
@endsection
