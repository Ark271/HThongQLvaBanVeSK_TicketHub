<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function vnpay_payment(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'tickets' => 'required|array',
        ]);

        $order = DB::transaction(function () use ($request) {
            $totalAmount = 0;

            $order = Order::create([
                'user_id' => auth()->id(),
                'customer_name' => $request->name,
                'customer_email' => $request->email,
                'customer_phone' => $request->phone,
                'order_code' => 'ORD-' . time() . '-' . Str::random(5),
                'total_amount' => 0,
                'status' => 'pending',
                'payment_method' => 'vnpay',
            ]);

            foreach ($request->tickets as $ticketId => $qty) {
                $qty = (int) $qty;

                if ($qty <= 0) {
                    continue;
                }

                $ticket = Ticket::where('id', $ticketId)
                    ->where('event_id', $request->event_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($ticket->quantity < $qty) {
                    abort(400, 'Số lượng vé không đủ.');
                }

                $lineTotal = $ticket->price * $qty;
                $totalAmount += $lineTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'event_id' => $request->event_id,
                    'ticket_id' => $ticket->id,
                    'quantity' => $qty,
                    'price' => $ticket->price,
                ]);

                // Reserve vé ngay khi tạo order pending
                $ticket->decrement('quantity', $qty);
            }

            if ($totalAmount <= 0) {
                abort(400, 'Vui lòng chọn ít nhất 1 vé.');
            }

            $order->update([
                'total_amount' => $totalAmount,
            ]);

            return $order;
        });

        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = route('vnpay.return');

        $vnp_TmnCode = "V0M2F59B";
        $vnp_HashSecret = "GD1XUSMXTLO9WC6S5YNAVDNCAILJOYI5";

        $vnp_TxnRef = $order->id;
        $vnp_OrderInfo = "Thanh toan don hang" . $order->order_code;
        $vnp_OrderType = "billpayment";
        $vnp_Amount = (int) ($order->total_amount * 100);
        $vnp_Locale = "vn";
        $vnp_BankCode = "";
        $vnp_IpAddr = $request->ip();

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        ];

        if ($vnp_BankCode !== "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);

        $hashdata = "";
        $query = "";
        $i = 0;

        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }

            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);

        $paymentUrl = $vnp_Url . "?" . $query . "vnp_SecureHash=" . $vnpSecureHash;

        return redirect()->away($paymentUrl);
    }

    public function vnpay_return(Request $request)
    {
        $vnp_HashSecret = "GD1XUSMXTLO9WC6S5YNAVDNCAILJOYI5";

        $inputData = $request->except('vnp_SecureHash', 'vnp_SecureHashType');

        ksort($inputData);

        $hashdata = "";
        $i = 0;

        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);

        if ($secureHash !== $request->vnp_SecureHash) {
            return redirect()
                ->route('home')
                ->with('error', 'Sai chữ ký thanh toán VNPAY.');
        }

        $order = Order::with('items.ticket')->findOrFail($request->vnp_TxnRef);

        if ($request->input('vnp_ResponseCode') == '00') {
            $order->update([
                'status' => 'paid',
                'payment_method' => 'vnpay',
                'transaction_code' => $request->input('vnp_TransactionNo'),
                'paid_at' => now(),
            ]);

            return redirect()
                ->route('home')
                ->with('success', 'Thanh toán VNPAY thành công.');
        }

        DB::transaction(function () use ($order) {
            if ($order->status === 'pending') {
                foreach ($order->items as $item) {
                    $item->ticket->increment('quantity', $item->quantity);
                }

                $order->update([
                    'status' => 'failed',
                    'payment_method' => 'vnpay',
                ]);
            }
        });

        return redirect()
            ->route('home')
            ->with('error', 'Thanh toán VNPAY thất bại hoặc đã bị hủy.');
    }
}
