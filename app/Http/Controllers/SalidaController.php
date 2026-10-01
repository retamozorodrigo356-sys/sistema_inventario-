<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExitRequest;
use App\Models\Movement;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Registro de salidas de productos del inventario (RF-06, RF-07, RF-08).
 */
class SalidaController extends Controller
{
    public function index(): View
    {
        $movements = Movement::with(['product', 'user'])
            ->ofType(Movement::SALIDA)
            ->latest('id')
            ->paginate(10);

        return view('salidas.index', compact('movements'));
    }

    public function create(): View
    {
        return view('salidas.create', [
            'products' => Product::where('status', 'activo')->orderBy('name')->get(),
        ]);
    }

    /**
     * Registra la salida y disminuye el stock automáticamente.
     * No permite salidas superiores al stock disponible (RF-08).
     */
    public function store(StoreExitRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $result = DB::transaction(function () use ($data) {
            $product = Product::lockForUpdate()->findOrFail($data['product_id']);

            // Doble validación dentro de la transacción (evita condiciones de carrera).
            if ($data['quantity'] > $product->stock) {
                return "La cantidad solicitada supera el stock disponible ({$product->stock}) del producto {$product->name}.";
            }

            Movement::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'type' => Movement::SALIDA,
                'quantity' => $data['quantity'],
                'date' => $data['date'],
                'observation' => $data['observation'] ?? null,
            ]);

            $product->decrement('stock', $data['quantity']);

            return null;
        });

        if ($result) {
            return redirect()->route('salidas.create')->with('error', $result)->withInput();
        }

        return redirect()->route('salidas.index')->with('success', 'Salida registrada y stock actualizado correctamente.');
    }
}
