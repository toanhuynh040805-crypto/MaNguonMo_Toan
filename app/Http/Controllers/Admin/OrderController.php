<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->filters($request);
        $orders = $this->filteredOrders($filters)->latest()->paginate(10)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function export(Request $request): StreamedResponse
    {
        $filters = $this->filters($request);
        $orders = $this->filteredOrders($filters);
        $filename = 'openshop_don_hang_' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($orders): void {
            $stream = fopen('php://output', 'wb');
            if ($stream === false) {
                return;
            }
            // BOM allows Excel to display Vietnamese characters correctly.
            fwrite($stream, "\xEF\xBB\xBF");
            fputcsv($stream, ['Mã đơn', 'Ngày đặt', 'Khách hàng', 'Email', 'Người nhận', 'Số điện thoại', 'Địa chỉ', 'Tổng tiền (VND)', 'Trạng thái'], ',', '"', '');
            foreach ($orders->lazyByIdDesc(200) as $order) {
                fputcsv($stream, [
                    $order->code,
                    $order->created_at->format('d/m/Y H:i'),
                    $this->safeCell($order->user?->name ?? ''),
                    $this->safeCell($order->user?->email ?? ''),
                    $this->safeCell($order->recipient_name),
                    $this->safeCell($order->phone),
                    $this->safeCell($order->address),
                    $order->total_amount,
                    $order->status_label,
                ], ',', '"', '');
            }
            fclose($stream);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
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

    /** Shared filtering makes exported rows match the list shown on screen. */
    private function filters(Request $request): array
    {
        return $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:pending,confirmed,shipping,completed,cancelled'],
        ]);
    }

    private function filteredOrders(array $filters): Builder
    {
        return Order::query()->with('user')
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['q'] ?? null, function (Builder $query, string $term): void {
                $query->where(function (Builder $sub) use ($term): void {
                    if (ctype_digit($term)) {
                        $sub->orWhere('id', (int) $term);
                    } elseif (preg_match('/^DH0*([0-9]+)$/i', $term, $matches)) {
                        $sub->orWhere('id', (int) $matches[1]);
                    }
                    $sub->orWhere('recipient_name', 'like', '%' . $term . '%')
                        ->orWhereHas('user', fn (Builder $user) => $user->where('email', 'like', '%' . $term . '%'));
                });
            });
    }

    /** Prevent spreadsheet formulas hidden in user-controlled fields. */
    private function safeCell(string $value): string
    {
        return preg_match('/^[=+@\-]/u', ltrim($value)) || preg_match('/^[\t\r\n]/u', $value)
            ? "'" . $value : $value;
    }
}
