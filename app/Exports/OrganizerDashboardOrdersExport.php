<?php

namespace App\Exports;

use App\Models\OrderItem;
use Maatwebsite\Excel\Concerns\FromArray;

class OrganizerDashboardOrdersExport implements FromArray
{
    public function array(): array
    {
        $organizerId = auth()->id();

        $rows = [[
            'STT',
            'Tên',
            'Email',
            'Số điện thoại',
            'Sự kiện',
            'Loại vé',
            'Số lượng',
            'Tổng tiền',
            'Thời điểm đặt vé',
        ]];

        $orderItems = OrderItem::with(['order.user', 'event', 'ticket'])
            ->whereHas('order', fn ($q) => $q->where('status', 'paid'))
            ->whereHas('event', fn ($q) => $q->where('user_id', $organizerId))
            ->latest()
            ->get();

        $totalRevenue = 0;

        foreach ($orderItems as $index => $item) {
            $lineTotal = $item->price * $item->quantity;
            $totalRevenue += $lineTotal;

            $rows[] = [
                $index + 1,
                $item->order->customer_name ?? 'Không rõ',
                $item->order->customer_email ?? 'Không rõ',
                $item->order->customer_phone ?? 'Không rõ',
                $item->event->title ?? 'Không rõ',
                strtoupper($item->ticket->type ?? 'N/A'),
                $item->quantity,
                $lineTotal,
                optional($item->order->created_at)->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i'),
            ];
        }

        $rows[] = [];
        $rows[] = ['', '', '', '', '', '', 'Tổng doanh thu:', $totalRevenue];

        return $rows;
    }
}
