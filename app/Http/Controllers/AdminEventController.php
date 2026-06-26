<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;


class AdminEventController extends Controller
{
    //HIỂN THỊ DANH SÁCH SỰ KIỆN
    public function index()
    {
        $events = Event::with('creator')
        ->orderBy('start_datetime', 'desc')
        ->paginate(10);

        return view('admin.events.index', compact('events'));
    }

    // XEM CHI TIẾT DANH SÁCH SỰ KIỆN
    public function show(Event $event)
    {
        $event->load('tickets');
        return view('admin.events.show', compact('event'));
    }

    // SỬA SỰ KIỆN
    public function edit(string $id)
    {
        $event = Event::with('tickets')->findOrFail($id);

        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, string $id)
    {
        $event = Event::with('tickets')->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'location' => 'required|string|max:255',
            'max_participants' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',

            'ticket_normal_price' => 'required|numeric|min:0',
            'ticket_normal_qty' => 'required|integer|min:0',
            'ticket_vip_price' => 'required|numeric|min:0',
            'ticket_vip_qty' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($request, $event, $validated) {
            $imagePath = $event->image;

            if ($request->hasFile('image')) {
                if ($event->image) {
                    Storage::disk('public')->delete($event->image);
                }

                $imagePath = $request->file('image')
                    ->store('events', 'public');
            }

            $event->update([
                'title' => $validated['title'],
                'start_datetime' => $validated['start_datetime'],
                'end_datetime' => $validated['end_datetime'],
                'location' => $validated['location'],
                'max_participants' => $validated['max_participants'],
                'description' => $validated['description'] ?? null,
                'image' => $imagePath,
            ]);

            $event->tickets()->updateOrCreate(
                ['type' => 'normal'],
                [
                    'price' => $validated['ticket_normal_price'],
                    'quantity' => $validated['ticket_normal_qty'],
                ]
            );

            $event->tickets()->updateOrCreate(
                ['type' => 'vip'],
                [
                    'price' => $validated['ticket_vip_price'],
                    'quantity' => $validated['ticket_vip_qty'],
                ]
            );
        });

        return redirect()
            ->route('admin.events.show', $event->id)
            ->with('success', 'Cập nhật sự kiện thành công.');
    }

    //FORM THÊM SỰ KIỆN
    public function create()
    {
        return view('admin.events.create');
    }

    //LƯU SỰ KIỆN
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'location' => 'required|string|max:255',
            'max_participants' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',

            'ticket_normal_price' => 'required|integer|min:0',
            'ticket_normal_qty'   => 'required|integer|min:1',

            'ticket_vip_price' => 'required|integer|min:0',
            'ticket_vip_qty'   => 'required|integer|min:1',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')
                ->store('events', 'public');
        }

        $event = Event::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'start_datetime' => Carbon::parse($request->start_datetime),
            'end_datetime' => Carbon::parse($request->end_datetime),
            'location' => $request->location,
            'max_participants' => $request->max_participants,
            'description' => $request->description,
            'image' => $imagePath,
        ]);

        // Vé thường
        Ticket::create([
            'event_id' => $event->id,
            'type'     => 'normal',
            'price'    => $request->ticket_normal_price,
            'quantity' => $request->ticket_normal_qty,
        ]);

        // Vé VIP
        Ticket::create([
            'event_id' => $event->id,
            'type'     => 'vip',
            'price'    => $request->ticket_vip_price,
            'quantity' => $request->ticket_vip_qty,
        ]);

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Tạo sự kiện thành công');
    }

    //XÓA SỰ KIỆN
    public function destroy(Event $event)
    {
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }
        $event->delete();
        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Đã xóa sự kiện');
    }

}
