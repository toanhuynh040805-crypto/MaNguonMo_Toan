<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        if ($cart === []) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Giỏ hàng đang trống.']);
        }
        $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');
        $items = collect($cart)->map(fn ($quantity, $id) => $products->has((int) $id)
            ? ['product' => $products->get((int) $id), 'quantity' => (int) $quantity,
                'subtotal' => $products->get((int) $id)->price * $quantity]
            : null)->filter()->values();

        if ($items->count() !== count($cart) || $items->contains(fn ($item) => ! $item['product']->is_active || $item['quantity'] > $item['product']->stock)) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Giỏ hàng có sản phẩm hết hàng hoặc ngừng bán. Hãy kiểm tra lại.']);
        }

        return view('shop.checkout.create', ['items' => $items, 'total' => $items->sum('subtotal')]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'recipient_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'regex:/^0[0-9]{9,10}$/'],
            'address' => ['required', 'string', 'max:500'],
            'note' => ['nullable', 'string', 'max:1000'],
        ], ['phone.regex' => 'Vui lòng nhập số điện thoại Việt Nam hợp lệ.']);

        $cart = $request->session()->get('cart', []);
        if ($cart === []) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Giỏ hàng đang trống.']);
        }

        $order = DB::transaction(function () use ($cart, $data, $request): Order {
            $products = Product::whereIn('id', array_keys($cart))
                ->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            if ($products->count() !== count($cart)) {
                throw ValidationException::withMessages(['cart' => 'Có sản phẩm không còn tồn tại.']);
            }

            $total = 0;
            foreach ($cart as $id => $quantity) {
                $product = $products->get((int) $id);
                if (! $product || ! $product->is_active || ! is_int($quantity) || $quantity < 1 || $quantity > 99 || $product->stock < $quantity) {
                    throw ValidationException::withMessages(['cart' => 'Giỏ hàng đã thay đổi hoặc tồn kho không đủ.']);
                }
                $total += $product->price * $quantity;
            }

            $order = Order::create([
                'user_id' => $request->user()->id,
                'recipient_name' => $data['recipient_name'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'note' => $data['note'] ?? null,
                'status' => 'pending',
                'total_amount' => $total,
            ]);

            foreach ($cart as $id => $quantity) {
                $product = $products->get((int) $id);
                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $quantity,
                    'subtotal' => $product->price * $quantity,
                ]);
                $product->decrement('stock', $quantity);
            }

            return $order;
        }, 3);

        $request->session()->forget('cart');
        return redirect()->route('orders.show', $order)->with('success', 'Đặt hàng thành công! Thanh toán khi nhận hàng (COD).');
    }
}
