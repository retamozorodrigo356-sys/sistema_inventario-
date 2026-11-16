<x-layouts.app :title="__('app.products.title')">
<div class="page-header">
    <div>
        <h1 class="h4 mb-1">{{ __('app.products.heading') }}</h1>
        <p class="text-secondary mb-0">{{ __('app.products.subtitle') }}</p>
    </div>
    @can('products.create')
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>{{ __('app.products.register') }}
        </a>
    @endcan
</div>

{{-- BUSCADOR Y FILTROS --}}
<form action="{{ route('products.index') }}" method="GET" class="filter-bar mb-3">
    <div class="row g-2">
        <div class="col-12 col-md-4">
            <input type="text" name="search" class="form-control" placeholder="{{ __('app.products.search_placeholder') }}"
                   value="{{ request('search') }}">
        </div>
        <div class="col-6 col-md-3">
            <select name="category_id" class="form-select">
                <option value="">{{ __('app.products.all_categories') }}</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <select name="status" class="form-select">
                <option value="">{{ __('app.products.all_statuses') }}</option>
                <option value="activo" @selected(request('status') === 'activo')>{{ __('app.common.active') }}</option>
                <option value="inactivo" @selected(request('status') === 'inactivo')>{{ __('app.common.inactive') }}</option>
            </select>
        </div>
        <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-outline-primary flex-fill">
                <i class="bi bi-search me-1"></i>{{ __('app.common.search') }}
            </button>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">{{ __('app.common.clear') }}</a>
        </div>
    </div>
</form>

{{-- TABLA DE PRODUCTOS --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('app.common.code') }}</th>
                    <th>{{ __('app.common.name') }}</th>
                    <th>{{ __('app.common.category') }}</th>
                    <th class="text-end">{{ __('app.common.price') }}</th>
                    <th class="text-center">{{ __('app.common.stock') }}</th>
                    <th class="text-center">{{ __('app.common.minimum') }}</th>
                    <th class="text-center">{{ __('app.common.status') }}</th>
                    <th class="text-center">{{ __('app.common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($products as $product)
                <tr>
                    <td class="cell-code">{{ $product->code }}</td>
                    <td>
                        {{ $product->name }}
                        @if ($product->hasLowStock())
                            <span class="badge text-bg-danger ms-1">{{ __('app.common.low_stock') }}</span>
                        @endif
                    </td>
                    <td class="cell-truncate" title="{{ $product->category?->name }}">{{ $product->category?->name ?? '—' }}</td>
                    <td class="text-end cell-num">${{ number_format($product->price, 2) }}</td>
                    <td class="text-center">
                        <span class="badge {{ $product->hasLowStock() ? 'text-bg-danger' : 'text-bg-success' }}">
                            {{ $product->stock }}
                        </span>
                    </td>
                    <td class="text-center cell-num">{{ $product->stock_min }}</td>
                    <td class="text-center">
                        @if ($product->status === 'activo')
                            <span class="badge text-bg-success">{{ __('app.common.active') }}</span>
                        @else
                            <span class="badge text-bg-secondary">{{ __('app.common.inactive') }}</span>
                        @endif
                    </td>
                    <td class="text-center cell-actions">
                        @can('products.update')
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary" title="{{ __('app.common.edit') }}">
                                <i class="bi bi-pencil"></i>
                            </a>
                        @endcan
                        @can('products.delete')
                            <button type="button" class="btn btn-sm btn-outline-danger" title="{{ __('app.common.delete') }}"
                                    data-delete-url="{{ route('products.destroy', $product) }}"
                                    data-delete-name="{{ $product->name }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        @endcan
                        @cannot('products.update')
                            @cannot('products.delete')
                                <span class="text-secondary small">{{ __('app.common.read_only') }}</span>
                            @endcannot
                        @endcannot
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="empty-state">
                        <i class="bi bi-box-seam"></i>
                        {{ __('app.products.empty') }}
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if ($products->hasPages())
        <div class="card-footer bg-white">
            {{ $products->links() }}
        </div>
    @endif
</div>
</x-layouts.app>
