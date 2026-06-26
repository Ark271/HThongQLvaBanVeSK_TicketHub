@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">

        <!-- CỘT TRÁI -->
        <div class="col-md-3">
            <div class="list-group">
                <a href="{{ route('profile', ['tab' => 'info']) }}"
                   class="list-group-item {{ $tab === 'info' ? 'active' : '' }}">
                    Thông tin tài khoản
                </a>

                <a href="{{ route('profile', ['tab' => 'events']) }}"
                   class="list-group-item {{ $tab === 'events' ? 'active' : '' }}">
                    Sự kiện đã tham gia
                </a>

                <a href="{{ route('profile', ['tab' => 'password']) }}"
                   class="list-group-item {{ $tab === 'password' ? 'active' : '' }}">
                    Đổi mật khẩu
                </a>

                <a href="{{ route('profile', ['tab' => 'delete']) }}"
                   class="list-group-item text-danger {{ $tab === 'delete' ? 'active' : '' }}">
                    Xóa tài khoản
                </a>
            </div>
        </div>

        <!-- CỘT PHẢI -->
        <div class="col-md-9">
            @if (session('success'))
                <div class="alert alert-success auto-dismiss-alert" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger auto-dismiss-alert" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <!-- THÔNG TIN -->
            @if ($tab === 'info')
                <div class="card">
                    <div class="card-header fw-bold">
                        Thông tin cá nhân
                    </div>

                    <div class="card-body">
                        <form method="POST"
                            action="{{ route('profile.information.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">
                                    Họ tên
                                </label>

                                <input type="text"
                                    id="name"
                                    name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $user->name) }}"
                                    required>

                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold">
                                    Email
                                </label>

                                <input type="email"
                                    id="email"
                                    name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $user->email) }}"
                                    required>

                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">
                                Lưu thay đổi
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- SỰ KIỆN ĐÃ THAM GIA -->
            @if ($tab === 'events')
                <div class="card mt-3">
                    <div class="card-header fw-bold">Sự kiện đã tham gia</div>

                    <div class="card-body events-scroll-box">
                        @if ($orders->isEmpty())
                            <p class="text-muted">Bạn chưa tham gia sự kiện nào</p>
                        @else
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="60">STT</th>
                                        <th>Tên sự kiện</th>
                                        <th>Thời gian sự kiện</th>
                                        <th>Số lượng vé</th>
                                        <th>Tổng tiền</th>
                                        <th>Thời gian đặt vé</th>
                                        <th>Chi tiết</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($orders as $order)
                                        @php
                                            $firstItem = $order->items->first();
                                            $event = $firstItem?->event;
                                            $totalTickets = $order->items->sum('quantity');
                                        @endphp

                                        @if ($event)
                                            <tr>
                                                <td class="text-center fw-bold">
                                                    {{ $loop->iteration }}
                                                </td>

                                                <td>{{ $event->title }}</td>

                                                <td>
                                                    {{ \Carbon\Carbon::parse($event->start_datetime)->format('d/m/Y H:i') }}
                                                </td>

                                                <td>{{ $totalTickets }}</td>

                                                <td>{{ number_format($order->total_amount) }}đ</td>

                                                <td>{{ $order->created_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}</td>

                                                <td>
                                                    <button class="btn btn-sm btn-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#orderModal{{ $order->id }}">
                                                        Xem chi tiết
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>

                            @foreach ($orders as $order)
                                @php
                                    $firstItem = $order->items->first();
                                    $event = $firstItem?->event;
                                @endphp

                                @if ($event)
                                    <div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">

                                                <div class="modal-header">
                                                    <h5 class="modal-title">Chi tiết vé đã đặt</h5>

                                                    <button type="button"
                                                            class="btn-close"
                                                            data-bs-dismiss="modal">
                                                    </button>
                                                </div>

                                                <div class="modal-body">
                                                    <p>
                                                        <strong>Tên sự kiện:</strong>
                                                        {{ $event->title }}
                                                    </p>

                                                    @if ($event->image)
                                                        <img src="{{ asset('storage/' . $event->image) }}"
                                                            alt="{{ $event->title }}"
                                                            class="img-fluid rounded mb-4"
                                                            style="width: 100%; max-height: 450px; object-fit: cover;">
                                                    @endif

                                                    <p>
                                                        <strong>Thời gian:</strong>
                                                        {{ \Carbon\Carbon::parse($event->start_datetime)->format('d/m/Y H:i') }}
                                                    </p>

                                                    <p>
                                                        <strong>Địa điểm:</strong>
                                                        {{ $event->location }}
                                                    </p>

                                                    <p>
                                                        <strong>Mô tả:</strong>
                                                        {{ $event->description }}
                                                    </p>

                                                    <hr>

                                                    <h6 class="fw-bold mb-3">Chi tiết vé</h6>

                                                    <table class="table table-sm table-bordered align-middle">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Loại vé</th>
                                                                <th>Số lượng</th>
                                                                <th>Giá</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody>
                                                            @foreach ($order->items as $item)
                                                                @if ($item->quantity > 0)
                                                                    <tr>
                                                                        <td>{{ strtoupper($item->ticket->type) }}</td>
                                                                        <td>{{ $item->quantity }}</td>
                                                                        <td>{{ number_format($item->price) }}đ</td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                        </tbody>
                                                    </table>

                                                    <p class="mt-3">
                                                        <strong>Tổng tiền:</strong>
                                                        {{ number_format($order->total_amount) }}đ
                                                    </p>

                                                    <p>
                                                        <strong>Thời gian đặt vé:</strong>
                                                        {{ $order->created_at->format('d/m/Y H:i') }}
                                                    </p>
                                                </div>

                                                <div class="modal-footer justify-content-center gap-2">
                                                    <button type="button"
                                                            class="btn btn-secondary"
                                                            data-bs-dismiss="modal">
                                                        Đóng
                                                    </button>

                                                    @if (
                                                        $order->status === 'paid' &&
                                                        \Carbon\Carbon::now()->lt(
                                                            \Carbon\Carbon::parse($event->start_datetime)
                                                        )
                                                    )
                                                        <form method="POST"
                                                            action="{{ route('profile.orders.cancel', $order) }}"
                                                            class="m-0"
                                                            onsubmit="return confirm('Bạn có chắc muốn hủy toàn bộ vé trong đơn này không?');">
                                                            @csrf

                                                            <button type="submit" class="btn btn-danger">
                                                                Hủy đặt vé
                                                            </button>
                                                        </form>
                                                    @elseif ($order->status === 'cancelled')
                                                        <span class="text-muted">
                                                            Đơn đặt vé này đã được hủy
                                                        </span>
                                                    @else
                                                        <span class="text-danger">
                                                            Sự kiện đã bắt đầu, không thể hủy vé
                                                        </span>
                                                    @endif
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            @endif

            <!-- ĐỔI MẬT KHẨU -->
            @if ($tab === 'password')
                <div class="card mt-3">
                    <div class="card-header fw-bold">Đổi mật khẩu</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('profile.password') }}">
                            @csrf

                            <input type="password"
                                   name="current_password"
                                   class="form-control mb-2"
                                   placeholder="Mật khẩu hiện tại">

                            <input type="password"
                                   name="password"
                                   class="form-control mb-2"
                                   placeholder="Mật khẩu mới">

                            <input type="password"
                                   name="password_confirmation"
                                   class="form-control mb-2"
                                   placeholder="Nhập lại mật khẩu">

                            <button class="btn btn-primary">Cập nhật</button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- XÓA TÀI KHOẢN -->
            @if ($tab === 'delete')
                <div class="card mt-3 border-danger">
                    <div class="card-header text-danger fw-bold">Xóa tài khoản</div>

                    <div class="card-body">
                        <p class="text-danger">
                            Hành động này không thể hoàn tác.
                        </p>

                        <form method="POST" action="{{ route('profile.delete') }}">
                            @csrf
                            <button class="btn btn-danger">Xóa tài khoản</button>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const alerts = document.querySelectorAll('.auto-dismiss-alert');

        alerts.forEach(function (alert) {
            setTimeout(function () {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';

                setTimeout(function () {
                    alert.remove();
                }, 500);
            }, 3000);
        });
    });
</script>

@endsection
