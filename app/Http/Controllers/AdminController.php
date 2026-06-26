<?php

namespace App\Http\Controllers;

use App\Exports\DashboardOrdersExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;

class AdminController extends Controller
{
    public function dashboard()
    {
        $dateFrom = request('date_from');
        $dateTo = request('date_to');
        $search = request('search');
        $sort = request('sort', 'created_at');
        $direction = request('direction', 'desc');

        $totalEvents = Event::count();

        $totalParticipants = Order::where('status', 'paid')
            ->distinct('user_id')
            ->count('user_id');

        $totalRevenueQuery = Order::where('status', 'paid');

        if ($dateFrom) {
            $totalRevenueQuery->whereDate('paid_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $totalRevenueQuery->whereDate('paid_at', '<=', $dateTo);
        }

        $totalRevenue = $totalRevenueQuery->sum('total_amount');

        $orderItems = OrderItem::selectRaw(
                'order_items.*, (price * quantity) as total_price'
            )
            ->with([
                'order.user',
                'event',
                'ticket'
            ])
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
            ->when($search, function ($query) use ($search) {
                $query->orWhereHas('event', function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                });
            })
            ->when($sort === 'total_price', function ($query) use ($direction) {
                $query->orderByRaw('(price * quantity) ' . $direction);
            })
            ->when($sort === 'created_at', function ($query) use ($direction) {
                $query->orderBy('order_items.created_at', $direction);
            })
            ->when($sort === 'id', function ($query) use ($direction) {
                $query->orderBy('order_items.id', $direction);
            })
            ->when($sort === 'quantity', function ($query) use ($direction) {
                $query->orderBy('order_items.quantity', $direction);
            })
            ->paginate(10)
            ->appends(request()->query());

        return view('admin.dashboard', compact(
            'totalEvents',
            'totalParticipants',
            'totalRevenue',
            'orderItems',
            'dateFrom',
            'dateTo',
            'search',
            'sort',
            'direction'
        ));
    }

    public function exportExcel()
    {
        return Excel::download(new DashboardOrdersExport, 'dashboard_orders.xlsx');
    }
}
