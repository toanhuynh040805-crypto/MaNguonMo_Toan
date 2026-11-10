<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'categoryCount' => Category::count(),
            'productCount' => Product::count(),
            'orderCount' => Order::count(),
            'pendingCount' => Order::where('status', 'pending')->count(),
            'lowStockCount' => Product::whereBetween('stock', [1, 5])->count(),
            'outOfStockCount' => Product::where('stock', 0)->count(),
            'revenue' => Order::where('status', 'completed')->sum('total_amount'),
            'recentOrders' => Order::with('user')->latest()->limit(6)->get(),
        ]);
    }
}
