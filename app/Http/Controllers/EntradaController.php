<?php

namespace App\Http\Controllers;

use App\Events\InventoryChanged;
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
        $productName = '';

        DB::transaction(function () use ($data, &$productName) {
            $product = Product::lockForUpdate()->findOrFail($data['product_id']);
            $productName = $product->name;

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

        InventoryChanged::notify('entrada', __('app.messages.broadcast_entry_created', ['quantity' => $data['quantity'], 'product' => $productName]));

        return redirect()->route('entradas.index')->with('success', __('app.messages.entry_created'));
    }

    public function edit(Movement $movement): View
    {
        abort_unless($movement->isEntrada(), 404);

        return view('entradas.edit', [
            'movement' => $movement,
            'products' => Product::where('status', 'activo')->orderBy('name')->get(),
        ]);
    }

    /**
     * Actualiza la entrada y ajusta el stock (RF-07) según el cambio realizado.
     */
    public function update(StoreMovementRequest $request, Movement $movement): RedirectResponse
    {
        abort_unless($movement->isEntrada(), 404);

        $data = $request->validated();
        $productName = '';

        $error = DB::transaction(function () use ($movement, $data, &$productName) {
            $product = Product::lockForUpdate()->findOrFail($data['product_id']);
            $productName = $product->name;
            $previous = Product::lockForUpdate()->findOrFail($movement->product_id);
            $sameProduct = $previous->id === $product->id;

            // Ajuste de stock: se revierte lo que aportó la entrada original y se aplica el nuevo valor.
            $revert = $movement->quantity;
            $apply = $data['quantity'];

            if (! $sameProduct && $previous->stock < $revert) {
                return __('app.messages.entry_change_product', ['name' => $previous->name, 'stock' => $previous->stock, 'quantity' => $revert]);
            }

            if ($sameProduct && ($product->stock - $revert + $apply) < 0) {
                return __('app.messages.entry_reduce_unavailable', ['name' => $product->name, 'quantity' => $revert]);
            }

            if ($sameProduct) {
                $product->stock = $product->stock - $revert + $apply;
                $product->save();
            } else {
                $previous->decrement('stock', $revert);
                $product->increment('stock', $apply);
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
            return redirect()->route('entradas.edit', $movement)->with('error', $error)->withInput();
        }

        InventoryChanged::notify('entrada', __('app.messages.broadcast_entry_updated', ['quantity' => $data['quantity'], 'product' => $productName]));

        return redirect()->route('entradas.index')->with('success', __('app.messages.entry_updated'));
    }

    /**
     * Elimina la entrada y revierte el aumento de stock (RF-07).
     */
    public function destroy(Movement $movement): RedirectResponse
    {
        abort_unless($movement->isEntrada(), 404);

        $productName = $movement->product?->name ?? __('app.common.product');
        $quantity = $movement->quantity;

        $error = DB::transaction(function () use ($movement) {
            $product = Product::lockForUpdate()->find($movement->product_id);

            if ($product && $product->stock < $movement->quantity) {
                return __('app.messages.entry_delete_unavailable', ['name' => $product->name, 'stock' => $product->stock, 'quantity' => $movement->quantity]);
            }

            $product?->decrement('stock', $movement->quantity);
            $movement->delete();

            return null;
        });

        if ($error) {
            return redirect()->route('entradas.index')->with('error', $error);
        }

        InventoryChanged::notify('entrada', __('app.messages.broadcast_entry_deleted', ['quantity' => $quantity, 'product' => $productName]));

        return redirect()->route('entradas.index')->with('success', __('app.messages.entry_deleted'));
    }
}
