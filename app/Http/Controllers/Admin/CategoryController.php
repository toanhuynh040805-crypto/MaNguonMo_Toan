<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $data = $request->validate(['q' => ['nullable', 'string', 'max:100']]);
        $categories = Category::withCount('products')
            ->when($data['q'] ?? null, fn ($q, $term) => $q->where('name', 'like', '%' . $term . '%'))
            ->latest()->paginate(10)->withQueryString();
        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.form', ['category' => new Category()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        Category::create($data);
        return redirect()->route('admin.categories.index')->with('success', 'Đã thêm danh mục.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.form', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $category->update($this->validatedData($request, $category));
        return redirect()->route('admin.categories.index')->with('success', 'Đã cập nhật danh mục.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Danh mục đang có sản phẩm nên không thể xóa.');
        }
        $category->delete();
        return back()->with('success', 'Đã xóa danh mục.');
    }

    private function validatedData(Request $request, ?Category $category = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1500'],
        ]);
        $data['slug'] = Str::slug($data['name']);
        if ($data['slug'] === '' || Category::where('slug', $data['slug'])
            ->when($category, fn ($q) => $q->whereKeyNot($category->id))->exists()) {
            throw ValidationException::withMessages(['name' => 'Tên danh mục không hợp lệ hoặc đã tồn tại.']);
        }
        return $data;
    }
}
