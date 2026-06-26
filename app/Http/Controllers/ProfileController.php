<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'info');
        $user = auth()->user();

        $participants = collect();

        $orders = Order::with([
                'items.event',
                'items.ticket'
            ])
            ->where('user_id', $user->id)
            ->where('status', 'paid')
            ->latest()
            ->get();

        return view('profile.index', compact(
            'tab',
            'user',
            'participants',
            'orders'
        ));
    }

    public function cancelOrder(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            return redirect()
                ->route('profile', ['tab' => 'events'])
                ->with('error', 'Bạn không có quyền hủy đơn đặt vé này.');
        }

        if ($order->status !== 'paid') {
            return redirect()
                ->route('profile', ['tab' => 'events'])
                ->with('error', 'Đơn đặt vé này đã được hủy hoặc không thể hủy.');
        }

        $order->load([
            'items.event',
            'items.ticket'
        ]);

        $firstItem = $order->items->first();
        $event = $firstItem?->event;

        if (!$event) {
            return redirect()
                ->route('profile', ['tab' => 'events'])
                ->with('error', 'Không tìm thấy thông tin sự kiện.');
        }

        $eventStart = Carbon::parse($event->start_datetime);

        if (Carbon::now()->greaterThanOrEqualTo($eventStart)) {
            return redirect()
                ->route('profile', ['tab' => 'events'])
                ->with('error', 'Không thể hủy vé vì sự kiện đã bắt đầu.');
        }

        try {
            DB::transaction(function () use ($order) {
                foreach ($order->items as $item) {
                    $ticket = Ticket::lockForUpdate()
                        ->findOrFail($item->ticket_id);

                    $ticket->increment('quantity', $item->quantity);
                }

                $order->update([
                    'status' => 'cancelled',
                ]);
            });

            return redirect()
                ->route('profile', ['tab' => 'events'])
                ->with(
                    'success',
                    'Hủy đặt vé thành công. Số lượng vé đã được khôi phục.'
                );
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()
                ->route('profile', ['tab' => 'events'])
                ->with(
                    'error',
                    'Hủy đặt vé thất bại. Vui lòng thử lại sau.'
                );
        }
    }

    public function updateInformation(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
            'name.max' => 'Họ tên không được vượt quá 255 ký tự.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'email.unique' => 'Địa chỉ email này đã được sử dụng.',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        return redirect()
            ->route('profile', ['tab' => 'info'])
            ->with('success', 'Cập nhật thông tin cá nhân thành công.');
    }
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();
        $user->password = bcrypt($request->password);
        $user->save();

        return redirect()
            ->route('profile', ['tab' => 'password'])
            ->with('success', 'Đổi mật khẩu thành công');
    }

    public function destroy()
    {
        $user = Auth::user();

        Auth::logout();
        $user->delete();

        return redirect('/')
            ->with('success', 'Tài khoản đã bị xóa');
    }
}
