<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */


    public function index()
    {
        // $events = Event::latest()->take(3)->get(); // hiển thị demo
        // return view('home', compact('events'));

        $featuredEvents = Event::query()
            ->select(
                'events.*',
                DB::raw('COALESCE(SUM(order_items.quantity), 0) as sold_tickets')
            )
            ->leftJoin('order_items', 'events.id', '=', 'order_items.event_id')
            ->leftJoin('orders', function ($join) {
                $join->on('order_items.order_id', '=', 'orders.id')
                    ->where('orders.status', '=', 'paid');
            })
            ->whereNotNull('events.image')
            ->where('events.image', '!=', '')
            ->groupBy(
                'events.id',
                'events.user_id',
                'events.title',
                'events.start_datetime',
                'events.end_datetime',
                'events.location',
                'events.max_participants',
                'events.description',
                'events.image',
                'events.created_at',
                'events.updated_at'
            )
            ->orderByDesc('sold_tickets')
            ->limit(3)
            ->get();

        return view('home', compact('featuredEvents'));
    }
}
