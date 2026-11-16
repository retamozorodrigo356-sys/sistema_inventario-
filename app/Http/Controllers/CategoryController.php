<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Listado de categorías (RF-04).
     */
    public function index(): View
    {
        $categories = Category::withCount('products')->orderBy('name')->paginate(10);

        return view('categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('categories.create');
    }

    /**
     * Registra una categoría (RF-04).
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categorias,name'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        Category::create($data);

        return redirect()->route('categories.index')->with('success', __('app.messages.category_created'));
    }

    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Actualiza una categoría (RF-04).
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categorias,name,'.$category->id],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $category->update($data);

        return redirect()->route('categories.index')->with('success', __('app.messages.category_updated'));
    }

    /**
     * Elimina una categoría (RF-04).
     */
    public function destroy(Category $category): RedirectResponse
    {
        try {
            $category->delete();

            return redirect()->route('categories.index')->with('success', __('app.messages.category_deleted'));
        } catch (QueryException) {
            return redirect()
                ->route('categories.index')
                ->with('error', __('app.messages.category_has_products'));
        }
    }
}
