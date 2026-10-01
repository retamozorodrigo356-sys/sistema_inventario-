<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Movement;
use App\Models\Product;
use Illuminate\View\View;

/**
 * Reportes e información resumida del inventario.
 */
class ReportController extends Controller
{
    public function index(): View
    {
        $products = Product::with('category')->orderBy('name')->get();

        // Productos con stock bajo (RF-09).
        $lowStock = $products
            ->filter(fn (Product $product) => $product->hasLowStock())
            ->values();

        $entries = Movement::ofType(Movement::ENTRADA);
        $exits = Movement::ofType(Movement::SALIDA);

        $summary = [
            'products' => $products->count(),
            'categories' => Category::count(),
            'stock_total' => (int) $products->sum('stock'),
            'value_total' => (float) $products->sum(fn (Product $p) => $p->price * $p->stock),
            'entries_count' => (clone $entries)->count(),
            'entries_units' => (int) (clone $entries)->sum('quantity'),
            'exits_count' => (clone $exits)->count(),
            'exits_units' => (int) (clone $exits)->sum('quantity'),
        ];

        $byCategory = Category::withCount('products')
            ->withSum('products as stock_by_category', 'stock')
            ->orderBy('name')
            ->get();

        return view('reportes.index', compact('products', 'lowStock', 'summary', 'byCategory'));
    }
}
