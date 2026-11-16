<x-layouts.app :title="__('app.reports.title')">
<div class="page-header no-print">
    <div>
        <h1 class="h4 mb-1">{{ __('app.reports.heading') }}</h1>
        <p class="text-secondary mb-0">{{ __('app.reports.subtitle') }}</p>
    </div>
    <button type="button" class="btn btn-outline-dark" onclick="window.print()">
        <i class="bi bi-printer me-1"></i>{{ __('app.common.print_report') }}
    </button>
</div>

{{-- INDICADORES --}}
<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="stat-label">{{ __('app.reports.products') }}</div>
                <div class="stat-value">{{ $summary['products'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="stat-label">{{ __('app.reports.stock_units') }}</div>
                <div class="stat-value">{{ $summary['stock_total'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="stat-label">{{ __('app.reports.inventory_value') }}</div>
                <div class="stat-value">${{ number_format($summary['value_total'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="stat-label">{{ __('app.dashboard.low_stock_products') }}</div>
                <div class="stat-value text-danger">{{ $lowStock->count() }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- STOCK POR CATEGORÍA --}}
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white"><strong>{{ __('app.reports.stock_by_category') }}</strong></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('app.common.category') }}</th>
                            <th class="text-center">{{ __('app.reports.products') }}</th>
                            <th class="text-center">{{ __('app.reports.units') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($byCategory as $category)
                        <tr>
                            <td>{{ $category->name }}</td>
                            <td class="text-center cell-num">{{ $category->products_count }}</td>
                            <td class="text-center cell-num">{{ $category->stock_by_category ?? 0 }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MOVIMIENTOS --}}
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white"><strong>{{ __('app.reports.movement_summary') }}</strong></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('app.common.type') }}</th>
                            <th class="text-center">{{ __('app.reports.operations') }}</th>
                            <th class="text-center">{{ __('app.reports.units') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="badge text-bg-success">{{ __('app.entries.title') }}</span></td>
                            <td class="text-center cell-num">{{ $summary['entries_count'] }}</td>
                            <td class="text-center cell-num">{{ $summary['entries_units'] }}</td>
                        </tr>
                        <tr>
                            <td><span class="badge text-bg-warning text-dark">{{ __('app.exits.title') }}</span></td>
                            <td class="text-center cell-num">{{ $summary['exits_count'] }}</td>
                            <td class="text-center cell-num">{{ $summary['exits_units'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- INVENTARIO COMPLETO --}}
<div class="card border-0 shadow-sm mt-3">
    <div class="card-header bg-white d-flex justify-content-between">
        <strong>{{ __('app.reports.general_inventory') }}</strong>
        <span class="text-secondary small">{{ __('app.reports.total_products', ['count' => $products->count()]) }}</span>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('app.common.code') }}</th>
                    <th>{{ __('app.common.product') }}</th>
                    <th>{{ __('app.common.category') }}</th>
                    <th class="text-end">{{ __('app.common.price') }}</th>
                    <th class="text-center">{{ __('app.common.stock') }}</th>
                    <th class="text-center">{{ __('app.common.minimum') }}</th>
                    <th class="text-end">{{ __('app.common.value') }}</th>
                    <th class="text-center">{{ __('app.common.status') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($products as $product)
                <tr>
                    <td class="cell-code">{{ $product->code }}</td>
                    <td class="cell-truncate" title="{{ $product->name }}">
                        {{ $product->name }}
                        @if ($product->hasLowStock())
                            <span class="badge text-bg-danger">{{ __('app.common.low_stock') }}</span>
                        @endif
                    </td>
                    <td class="cell-truncate" title="{{ $product->category?->name }}">{{ $product->category?->name ?? '—' }}</td>
                    <td class="text-end cell-num">${{ number_format($product->price, 2) }}</td>
                    <td class="text-center cell-num">{{ $product->stock }}</td>
                    <td class="text-center cell-num">{{ $product->stock_min }}</td>
                    <td class="text-end cell-num">${{ number_format($product->price * $product->stock, 2) }}</td>
                    <td class="text-center">
                        @if ($product->status === 'activo')
                            <span class="badge text-bg-success">{{ __('app.common.active') }}</span>
                        @else
                            <span class="badge text-bg-secondary">{{ __('app.common.inactive') }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="empty-state">{{ __('app.reports.empty_products') }}</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- STOCK BAJO --}}
<div class="card border-0 shadow-sm mt-3">
    <div class="card-header bg-white"><strong class="text-danger"><i class="bi bi-exclamation-triangle me-2"></i>{{ __('app.reports.low_stock_title') }}</strong></div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('app.common.code') }}</th>
                    <th>{{ __('app.common.product') }}</th>
                    <th>{{ __('app.common.category') }}</th>
                    <th class="text-center">{{ __('app.products.stock_current') }}</th>
                    <th class="text-center">{{ __('app.products.stock_min') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($lowStock as $product)
                <tr>
                    <td class="cell-code">{{ $product->code }}</td>
                    <td class="cell-truncate" title="{{ $product->name }}">{{ $product->name }}</td>
                    <td class="cell-truncate" title="{{ $product->category?->name }}">{{ $product->category?->name ?? '—' }}</td>
                    <td class="text-center cell-num"><span class="badge text-bg-danger">{{ $product->stock }}</span></td>
                    <td class="text-center cell-num">{{ $product->stock_min }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="empty-state">{{ __('app.reports.empty_low_stock') }}</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-layouts.app>
