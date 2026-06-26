@extends('admin.layout')

@section('content')
    <h2>Dashboard</h2>

    <div class="row mt-4">
        <form method="GET" action="{{ route('admin.dashboard') }}" class="row g-3 mt-3 mb-3">
            <div class="col-md-3">
                <label class="form-label">Từ ngày</label>
                <input type="date" name="date_from" value="{{ $dateFrom ?? '' }}" class="form-control">
            </div>

            <div class="col-md-3">
                <label class="form-label">Đến ngày</label>
                <input type="date" name="date_to" value="{{ $dateTo ?? '' }}" class="form-control">
            </div>

            <div class="col-md-3 d-flex align-items-end gap-2">
                <button class="btn btn-primary">Lọc ngày</button>

                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    Reset
                </a>
            </div>
        </form>

        <div class="col-md-4">
            <div class="alert alert-primary">
                Tổng số sự kiện: {{ $totalEvents ?? 0 }}
            </div>
        </div>

        <div class="col-md-4">
            <div class="alert alert-success">
                Tổng người tham gia: {{ $totalParticipants ?? 0 }}
            </div>
        </div>

        <div class="col-md-4">
            <div class="alert alert-warning">
                Tổng doanh thu: {{ number_format($totalRevenue ?? 0) }}đ
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <strong>Danh sách người đặt vé</strong>

                <form method="GET" action="{{ route('admin.dashboard') }}" class="d-flex gap-2" style="min-width: 520px;">

                    <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control" placeholder="Tìm tên, email, SĐT, sự kiện" style="width:280px;">

                    <button class="btn btn-primary" class="btn btn-primary px-3">
                        Tìm
                    </button>

                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary px-3">
                        Xóa
                    </a>

                    <a href="{{ route('admin.dashboard.exportExcel', request()->query()) }}" class="btn btn-success px-3">
                        Xuất Excel
                    </a>
                </form>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">

                @php
                    function sortLink($label, $column, $sort, $direction) {
                        $newDirection = ($sort === $column && $direction === 'asc') ? 'desc' : 'asc';
                        $icon = $sort === $column ? ($direction === 'asc' ? ' ↑' : ' ↓') : '';

                        return '<a class="text-dark text-decoration-none" href="' .
                            request()->fullUrlWithQuery([
                                'sort' => $column,
                                'direction' => $newDirection,
                                'page' => 1
                            ]) . '">' . $label . $icon . '</a>';
                    }
                @endphp

                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th> {!! sortLink('STT', 'id', $sort, $direction) !!} </th>

                            <th>{!! sortLink('Tên', 'customer_name', $sort, $direction) !!}</th>

                            <th>{!! sortLink('Email', 'customer_email', $sort, $direction) !!}</th>

                            <th>Số điện thoại</th>

                            <th>{!! sortLink('Sự kiện', 'event_title', $sort, $direction) !!}</th>

                            <th>{!! sortLink('Loại vé', 'ticket_type', $sort, $direction) !!}</th>

                            <th>{!! sortLink('Số lượng', 'quantity', $sort, $direction) !!}</th>

                            <th>{!! sortLink('Tổng tiền', 'total_price', $sort, $direction) !!}</th>

                            <th> {!! sortLink('Thời điểm đặt vé', 'created_at', $sort, $direction) !!} </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($orderItems as $item)
                            <tr>
                                <td class="text-center fw-bold">
                                    {{ $orderItems->firstItem() + $loop->index }}
                                </td>
                                <td>{{ $item->order->customer_name ?? 'Không rõ' }}</td>
                                <td>{{ $item->order->customer_email ?? 'Không rõ' }}</td>
                                <td>{{ $item->order->customer_phone ?? 'Không rõ' }}</td>
                                <td>{{ $item->event->title ?? 'Không rõ' }}</td>
                                <td>{{ strtoupper($item->ticket->type ?? 'N/A') }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->price * $item->quantity) }}đ</td>
                                <td>
                                    {{ $item->order->created_at
                                        ? $item->order->created_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i')
                                        : 'Không rõ'
                                    }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">
                                    Chưa có người đặt vé
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($orderItems->hasPages())
                <div class="dashboard-pagination mt-3">
                    {{ $orderItems->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
