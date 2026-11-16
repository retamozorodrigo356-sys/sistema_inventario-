<?php

namespace App\Http\Controllers;

use App\Events\InventoryChanged;
use App\Http\Requests\StoreExitRequest;
use App\Http\Requests\StoreMovementRequest;
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
        $productName = '';

        $result = DB::transaction(function () use ($data, &$productName) {
            $product = Product::lockForUpdate()->findOrFail($data['product_id']);
            $productName = $product->name;

            // Doble validación dentro de la transacción (evita condiciones de carrera).
            if ($data['quantity'] > $product->stock) {
                return __('app.messages.exit_exceeds_stock', ['stock' => $product->stock, 'name' => $product->name]);
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

        InventoryChanged::notify('salida', __('app.messages.broadcast_exit_created', ['quantity' => $data['quantity'], 'product' => $productName]));

        return redirect()->route('salidas.index')->with('success', __('app.messages.exit_created'));
    }

    public function edit(Movement $movement): View
    {
        abort_unless(! $movement->isEntrada(), 404);

        return view('salidas.edit', [
            'movement' => $movement,
            'products' => Product::where('status', 'activo')->orderBy('name')->get(),
        ]);
    }

    /**
     * Actualiza la salida: revierte el descuento anterior y aplica el nuevo,
     * sin permitir superar el stock disponible (RF-08).
     */
    public function update(StoreMovementRequest $request, Movement $movement): RedirectResponse
    {
        abort_unless(! $movement->isEntrada(), 404);

        $data = $request->validated();
        $productName = '';

        $error = DB::transaction(function () use ($movement, $data, &$productName) {
            $product = Product::lockForUpdate()->findOrFail($data['product_id']);
            $productName = $product->name;
            $previous = Product::lockForUpdate()->findOrFail($movement->product_id);
            $sameProduct = $previous->id === $product->id;

            // Stock disponible = stock actual + lo que devolvía la salida original (si es el mismo producto).
            $available = $product->stock + ($sameProduct ? $movement->quantity : 0);

            if ($data['quantity'] > $available) {
                return __('app.messages.exit_update_exceeds_stock', ['quantity' => $data['quantity'], 'available' => $available, 'name' => $product->name]);
            }

            if ($sameProduct) {
                $product->stock = $product->stock + $movement->quantity - $data['quantity'];
                $product->save();
            } else {
                $previous->increment('stock', $movement->quantity);
                $product->decrement('stock', $data['quantity']);
            }

            $movement->update([
                'product_id' => $product->id,
                'quantity' => $data['quantity'],
                'date' => $data['date'],
                'observation' => $data['observation'] ?? null,
            ]);

            return null;
        });

        if ($error) {
            return redirect()->route('salidas.edit', $movement)->with('error', $error)->withInput();
        }

        InventoryChanged::notify('salida', __('app.messages.broadcast_exit_updated', ['quantity' => $data['quantity'], 'product' => $productName]));

        return redirect()->route('salidas.index')->with('success', __('app.messages.exit_updated'));
    }

    /**
     * Elimina la salida y devuelve la cantidad al stock (RF-07).
     */
    public function destroy(Movement $movement): RedirectResponse
    {
        abort_unless(! $movement->isEntrada(), 404);

        $productName = $movement->product?->name ?? __('app.common.product');
        $quantity = $movement->quantity;

        DB::transaction(function () use ($movement) {
            Product::whereKey($movement->product_id)->increment('stock', $movement->quantity);
            $movement->delete();
        });

        InventoryChanged::notify('salida', __('app.messages.broadcast_exit_deleted', ['quantity' => $quantity, 'product' => $productName]));

        return redirect()->route('salidas.index')->with('success', __('app.messages.exit_deleted'));
    }
}
