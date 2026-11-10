<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = $request->user()->orders()->latest()->paginate(10);
        return view('shop.orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        $order->load('items');
        return view('shop.orders.show', compact('order'));
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $cancelled = DB::transaction(function () use ($order, $request): bool {
            $locked = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if ($locked->user_id !== $request->user()->id || $locked->status !== 'pending') {
                return false;
            }
            $locked->update(['status' => 'cancelled']);
            foreach ($locked->items as $item) {
                if ($item->product_id !== null) {
                    \App\Models\Product::whereKey($item->product_id)->increment('stock', $item->quantity);
                }
            }
            return true;
        }, 3);

        return redirect()->route('orders.show', $order)
            ->with($cancelled ? 'success' : 'error', $cancelled ? 'Đã hủy đơn hàng và hoàn lại tồn kho.' : 'Chỉ hủy được đơn đang chờ xác nhận.');
    }
}
