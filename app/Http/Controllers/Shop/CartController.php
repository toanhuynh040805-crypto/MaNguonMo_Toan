<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $cart = $request->session()->get('cart', []);
        $products = Product::with('category')->whereIn('id', array_keys($cart))->get()->keyBy('id');
        // Remove products that have been deleted by admin from an old session cart.
        $validCart = array_filter($cart, fn ($id) => $products->has((int) $id), ARRAY_FILTER_USE_KEY);
        if (count($validCart) !== count($cart)) {
            $request->session()->put('cart', $validCart);
            $cart = $validCart;
        }
        $items = collect($cart)->map(function ($quantity, $id) use ($products) {
            $product = $products->get((int) $id);
            return $product ? ['product' => $product, 'quantity' => (int) $quantity, 'subtotal' => $product->price * $quantity] : null;
        })->filter()->values();

        return view('shop.cart.index', [
            'items' => $items,
            'total' => $items->sum('subtotal'),
            'canCheckout' => $items->isNotEmpty() && $items->every(fn ($item) => $item['product']->is_active && $item['product']->stock >= $item['quantity']),
        ]);
    }

    public function add(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:99']]);
        if (! $product->is_active || $product->stock < 1) {
            throw ValidationException::withMessages(['cart' => 'Sản phẩm hiện không thể mua.']);
        }

        $cart = $request->session()->get('cart', []);
        $newQuantity = (int) ($cart[$product->id] ?? 0) + (int) $data['quantity'];
        if ($newQuantity > $product->stock || $newQuantity > 99) {
            throw ValidationException::withMessages(['cart' => 'Số lượng vượt quá tồn kho hoặc giới hạn 99 sản phẩm.']);
        }

        $cart[$product->id] = $newQuantity;
        $request->session()->put('cart', $cart);
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Đã thêm sản phẩm vào giỏ hàng.',
                'cart_count' => array_sum($cart),
            ]);
        }
        return redirect()->route('cart.index')->with('success', 'Đã thêm sản phẩm vào giỏ hàng.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:99']]);
        $cart = $request->session()->get('cart', []);
        if (! isset($cart[$product->id])) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Sản phẩm không có trong giỏ hàng.']);
        }
        if (! $product->is_active || $data['quantity'] > $product->stock) {
            return back()->withErrors(['cart' => 'Số lượng lớn hơn tồn kho hoặc sản phẩm đã ngừng bán.']);
        }

        $cart[$product->id] = (int) $data['quantity'];
        $request->session()->put('cart', $cart);
        return redirect()->route('cart.index')->with('success', 'Giỏ hàng đã cập nhật.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[$product->id]);
        $request->session()->put('cart', $cart);
        return redirect()->route('cart.index')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }
}
