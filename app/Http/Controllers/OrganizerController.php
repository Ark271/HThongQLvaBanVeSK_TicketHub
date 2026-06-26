<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Exports\OrganizerDashboardOrdersExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;

class OrganizerController extends Controller
{
    public function dashboard()
    {
        $organizerId = auth()->id();

        $dateFrom = request('date_from');
        $dateTo = request('date_to');
        $search = request('search');
        $sort = request('sort', 'created_at');
        $direction = request('direction', 'desc');

        $totalEvents = Event::where('user_id', $organizerId)->count();

        $totalParticipants = Order::where('status', 'paid')
            ->whereHas('items.event', function ($query) use ($organizerId) {
                $query->where('user_id', $organizerId);
            })
            ->distinct('user_id')
            ->count('user_id');

        $totalRevenueQuery = OrderItem::whereHas('order', function ($query) use ($dateFrom, $dateTo) {
                $query->where('status', 'paid');

                if ($dateFrom) {
                    $query->whereDate('paid_at', '>=', $dateFrom);
                }

                if ($dateTo) {
                    $query->whereDate('paid_at', '<=', $dateTo);
                }
            })
            ->whereHas('event', function ($query) use ($organizerId) {
                $query->where('user_id', $organizerId);
            });

        $totalRevenue = $totalRevenueQuery->sum(DB::raw('price * quantity'));

        $orderItems = OrderItem::selectRaw('order_items.*, (price * quantity) as total_price')
            ->with(['order.user', 'event', 'ticket'])
            ->whereHas('order', function ($query) use ($dateFrom, $dateTo, $search) {
                $query->where('status', 'paid');

                if ($dateFrom) {
                    $query->whereDate('paid_at', '>=', $dateFrom);
                }

                if ($dateTo) {
                    $query->whereDate('paid_at', '<=', $dateTo);
                }

                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('customer_name', 'like', "%{$search}%")
                          ->orWhere('customer_email', 'like', "%{$search}%")
                          ->orWhere('customer_phone', 'like', "%{$search}%");
                    });
                }
            })
            ->whereHas('event', function ($query) use ($organizerId, $search) {
                $query->where('user_id', $organizerId);

                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%");
                    });
                }
            });

        if ($sort === 'total_price') {
            $orderItems->orderByRaw('(price * quantity) ' . $direction);
        } elseif ($sort === 'created_at') {
            $orderItems->orderBy('order_items.created_at', $direction);
        } elseif ($sort === 'id') {
            $orderItems->orderBy('order_items.id', $direction);
        } elseif ($sort === 'quantity') {
            $orderItems->orderBy('order_items.quantity', $direction);
        } else {
            $orderItems->orderBy('order_items.created_at', 'desc');
        }

        $orderItems = $orderItems
            ->paginate(10)
            ->appends(request()->query());

        return view('organizer.dashboard', compact(
            'dateFrom',
            'dateTo',
            'search',
            'sort',
            'direction',
            'totalEvents',
            'totalParticipants',
            'totalRevenue',
            'orderItems'
        ));
    }

    public function exportExcel()
    {
        return Excel::download(new OrganizerDashboardOrdersExport,'organizer_dashboard.xlsx');
    }
}
