<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Movement;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Panel principal con el resumen del inventario (RF-09, RF-12).
     */
    public function __invoke(): View
    {
        $totals = [
            'products' => Product::count(),
            'categories' => Category::count(),
            'low_stock' => Product::whereColumn('stock', '<=', 'stock_min')->count(),
            'entries' => Movement::ofType(Movement::ENTRADA)->count(),
            'exits' => Movement::ofType(Movement::SALIDA)->count(),
        ];

        $entryUnits = (int) Movement::ofType(Movement::ENTRADA)->sum('quantity');
        $exitUnits = (int) Movement::ofType(Movement::SALIDA)->sum('quantity');

        $lowStockProducts = Product::with('category')
            ->whereColumn('stock', '<=', 'stock_min')
            ->orderBy('stock')
            ->limit(5)
            ->get();

        $recentMovements = Movement::with(['product', 'user'])
            ->latest('id')
            ->limit(6)
            ->get();

        return view('dashboard', compact(
            'totals',
            'entryUnits',
            'exitUnits',
            'lowStockProducts',
            'recentMovements'
        ));
    }
}
