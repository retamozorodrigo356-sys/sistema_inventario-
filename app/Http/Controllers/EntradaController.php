<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMovementRequest;
use App\Models\Movement;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Registro de entradas de productos al inventario (RF-05, RF-07).
 */
class EntradaController extends Controller
{
    public function index(): View
    {
        $movements = Movement::with(['product', 'user'])
            ->ofType(Movement::ENTRADA)
            ->latest('id')
            ->paginate(10);

        return view('entradas.index', compact('movements'));
    }

    public function create(): View
    {
        return view('entradas.create', [
            'products' => Product::where('status', 'activo')->orderBy('name')->get(),
        ]);
    }

    /**
     * Registra la entrada y aumenta el stock automáticamente.
     */
    public function store(StoreMovementRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $product = Product::lockForUpdate()->findOrFail($data['product_id']);

            Movement::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'type' => Movement::ENTRADA,
                'quantity' => $data['quantity'],
                'date' => $data['date'],
                'observation' => $data['observation'] ?? null,
            ]);

            $product->increment('stock', $data['quantity']);
        });

        return redirect()->route('entradas.index')->with('success', 'Entrada registrada y stock actualizado correctamente.');
    }
}
