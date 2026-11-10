<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $data = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
            'sort' => ['nullable', 'in:latest,price_asc,price_desc'],
        ]);

        $query = Product::query()->with('category')->where('is_active', true);
        if (! empty($data['q'])) {
            $query->where(function ($q) use ($data): void {
                $q->where('name', 'like', '%' . $data['q'] . '%')
                    ->orWhere('description', 'like', '%' . $data['q'] . '%');
            });
        }
        if (! empty($data['category'])) {
            $query->where('category_id', $data['category']);
        }

        match ($data['sort'] ?? 'latest') {
            'price_asc' => $query->orderBy('price')->orderBy('id'),
            'price_desc' => $query->orderByDesc('price')->orderByDesc('id'),
            default => $query->latest(),
        };

        return view('shop.products.index', [
            'products' => $query->paginate(9)->withQueryString(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);
        $product->load('category');
        $related = Product::where('category_id', $product->category_id)
            ->where('is_active', true)->whereKeyNot($product->id)->limit(4)->get();

        return view('shop.products.show', compact('product', 'related'));
    }
}
