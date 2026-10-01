<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Historial de movimientos: entradas, salidas, producto,
 * cantidad, fecha y usuario (RF-10).
 */
class MovementController extends Controller
{
    public function index(Request $request): View
    {
        $movements = Movement::with(['product', 'user'])
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->input('type')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->input('search');
                $q->whereHas('product', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('date', '>=', $request->input('date_from')))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('date', '<=', $request->input('date_to')))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('movimientos.index', [
            'movements' => $movements,
            'products' => Product::orderBy('name')->get(),
        ]);
    }
}
