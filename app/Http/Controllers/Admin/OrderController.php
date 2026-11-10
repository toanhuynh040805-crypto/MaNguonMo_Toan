<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $data = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:pending,confirmed,shipping,completed,cancelled'],
        ]);

        $orders = Order::with('user')
            ->when($data['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($data['q'] ?? null, function ($query, $term): void {
                $query->where(function ($sub) use ($term): void {
                    // Accept both raw numeric IDs and the displayed order code (e.g. DH000012).
                    if (ctype_digit($term)) {
                        $sub->orWhere('id', (int) $term);
                    } elseif (preg_match('/^DH0*([0-9]+)$/i', $term, $matches)) {
                        $sub->orWhere('id', (int) $matches[1]);
                    }
                    $sub->orWhere('recipient_name', 'like', '%' . $term . '%')
                        ->orWhereHas('user', fn ($u) => $u->where('email', 'like', '%' . $term . '%'));
                });
            })
            ->latest()->paginate(10)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load('user', 'items');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:confirmed,shipping,completed,cancelled']]);

        $updated = DB::transaction(function () use ($order, $data): bool {
            $locked = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if (! in_array($data['status'], Order::TRANSITIONS[$locked->status] ?? [], true)) {
                return false;
            }
            $locked->update(['status' => $data['status']]);
            if ($data['status'] === 'cancelled') {
                foreach ($locked->items as $item) {
                    if ($item->product_id !== null) {
                        Product::whereKey($item->product_id)->increment('stock', $item->quantity);
                    }
                }
            }
            return true;
        }, 3);

        return back()->with($updated ? 'success' : 'error', $updated ? 'Đã cập nhật trạng thái đơn hàng.' : 'Không thể chuyển sang trạng thái này.');
    }
}
