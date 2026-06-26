<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Ticket;

class TicketController extends Controller
{
    public function store(Request $request, Event $event)
    {
        Ticket::create([
            'event_id' => $event->id,
            'type'     => $request->type,
            'price'    => $request->price,
            'quantity' => $request->quantity,
        ]);

        return back()->with('success', 'Thêm vé thành công');
    }
}
