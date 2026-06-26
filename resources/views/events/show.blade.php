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
                 {{ \Carbon\Carbon::parse($event->start_datetime)->format('d/m/Y H:i') }}
            </p>
            <p> {{ $event->location }}</p>

            <p>{{ $event->description }}</p>
        </div>

        {{-- BÊN PHẢI: CHỌN VÉ --}}
        <div class="col-md-5">
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

                        <input
                            type="text"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            name="tickets[{{ $ticket->id }}]"
                            min="0"
                            max="{{ $ticket->quantity }}"
                            value="0"
                            class="form-control mt-2 ticket-qty"
                            data-price="{{ $ticket->price }}"
                            placeholder="Nhập số lượng vé"
                        >
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
@endsection
