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
        $filters = $request->validate([
            'status' => ['nullable', 'in:pending,confirmed,shipping,completed,cancelled'],
            'q' => ['nullable', 'string', 'max:30'],
        ]);

        // Begin with the user ID restriction so search can never leak other users' orders.
        $query = Order::query()->where('user_id', $request->user()->id);
        if (filled($filters['status'] ?? null)) {
            $query->where('status', $filters['status']);
        }
        if (filled($filters['q'] ?? null)) {
            $term = trim($filters['q']);
            if (preg_match('/^(?:DH0*)?([0-9]+)$/i', $term, $matches)) {
                $query->where('orders.id', (int) $matches[1]);
            } else {
                $query->whereRaw('1 = 0');
            }
        }
        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('shop.orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        $order->load(['items', 'statusHistories']);
        return view('shop.orders.show', compact('order'));
    }

    public function receipt(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        $order->load('items');
        return view('orders.receipt', compact('order'));
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
            $locked->statusHistories()->create([
                'actor_id' => $request->user()->id,
                'from_status' => 'pending',
                'to_status' => 'cancelled',
            ]);
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
