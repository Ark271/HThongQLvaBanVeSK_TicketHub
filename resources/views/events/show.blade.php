@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">

        {{-- BÊN TRÁI: THÔNG TIN --}}
        <div class="col-md-7">
            <h2>{{ $event->title }}</h2>

            @if ($event->image)
                <img src="{{ asset('storage/' . $event->image) }}"
                    alt="{{ $event->title }}"
                    class="event-thumbnail">
            @endif
            <p>
                 {{ \Carbon\Carbon::parse($event->start_datetime)->format('d/m/Y H:i') }} - {{ \Carbon\Carbon::parse($event->end_datetime)->format('d/m/Y H:i') }}
            </p>
            <p> {{ $event->location }}</p>

            <p>{{ $event->description }}</p>
        </div>

        {{-- BÊN PHẢI: CHỌN VÉ --}}
        <div class="col-md-5">
            @if (session('error'))
                <div class="alert alert-danger auto-dismiss-alert">
                    {{ session('error') }}
                </div>
            @endif


            @if (\Carbon\Carbon::now()->lte(\Carbon\Carbon::parse($event->end_datetime)))

            <form action="{{ route('vnpay.payment') }}" method="POST">
                @csrf

                <input type="hidden" name="event_id" value="{{ $event->id }}">

                <input class="form-control mb-2" name="name" placeholder="Họ tên" required>
                <input class="form-control mb-2" name="email" placeholder="Email" required>
                <input class="form-control mb-3" name="phone" placeholder="Số điện thoại" required>

                @foreach($event->tickets as $ticket)
                    <div class="border p-3 mb-2">
                        <strong>{{ strtoupper($ticket->type) }}</strong><br>
                        Giá: {{ number_format($ticket->price) }}đ <br>
                        Còn lại: {{ $ticket->quantity }}

                        @if ($ticket->quantity > 0)
                            <input type="text"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                name="tickets[{{ $ticket->id }}]"
                                value="{{ old('tickets.' . $ticket->id, 0) }}"
                                class="form-control mt-2 ticket-qty"
                                data-price="{{ $ticket->price }}"
                                max="{{ $ticket->quantity }}"
                                placeholder="Nhập số lượng vé">
                        @else
                            <input type="text"
                                value="0"
                                class="form-control mt-2"
                                disabled>

                            <small class="text-danger">
                                Loại vé này đã hết.
                            </small>
                        @endif
                    </div>
                @endforeach

                <div class="alert alert-info mt-3">
                    Tổng tiền:
                    <strong id="total-price">0đ</strong>
                </div>

                <input type="hidden" name="total_price" id="total-price-input" value="0">

                <button type="submit" name="redirect" class="btn btn-success">
                    Thanh toán VNPAY
                </button>
            </form>
            @else
                <div class="alert alert-danger">
                    <strong>Sự kiện đã kết thúc.</strong>

                    <div class="mt-1">
                        Thời gian kết thúc:
                        {{ \Carbon\Carbon::parse($event->end_datetime)->format('d/m/Y H:i') }}
                    </div>

                    <div>
                        Bạn không thể đặt vé cho sự kiện này.
                    </div>
                </div>
            @endif
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const qtyInputs = document.querySelectorAll('.ticket-qty');
                const totalPriceElement = document.getElementById('total-price');

                function formatCurrency(value) {
                    return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
                }

                function updateTotalPrice() {
                    let total = 0;

                    qtyInputs.forEach(input => {
                        let quantity = parseInt(input.value) || 0;
                        let price = parseFloat(input.dataset.price) || 0;
                        let max = parseInt(input.getAttribute('max')) || 0;

                        if (quantity < 0) {
                            quantity = 0;
                            input.value = 0;
                        }

                        if (quantity > max) {
                            quantity = max;
                            input.value = max;
                        }

                        total += quantity * price;
                    });

                    totalPriceElement.textContent = formatCurrency(total);
                }

                qtyInputs.forEach(input => {
                    input.addEventListener('input', updateTotalPrice);
                });

                updateTotalPrice();
            });
        </script>


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
