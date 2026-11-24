<x-layouts.app :title="__('app.dashboard.title')">
<div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
    <div>
        <h1 class="h4 mb-1">{{ __('app.dashboard.heading') }}</h1>
        <p class="text-secondary mb-0">{{ __('app.dashboard.subtitle') }}</p>
    </div>

    {{-- Estado de la conexión WebSocket (Laravel Reverb) --}}
    <span class="live-indicator" id="live-indicator" data-state="offline">
        <span class="live-dot"></span>
        <span id="live-status" class="small fw-semibold text-secondary">{{ __('app.live.connecting') }}</span>
    </span>
</div>

<div id="dashboard-live" data-url="{{ route('dashboard') }}">

{{-- TARJETAS DEL DASHBOARD --}}
<div class="row g-3">
    <div class="col-12 col-sm-6 col-xl">
        <div class="card stat-card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-primary-subtle text-primary"><i class="bi bi-box-seam"></i></div>
                <div class="ms-3">
                    <div class="stat-label">{{ __('app.dashboard.total_products') }}</div>
                    <div class="stat-value">{{ $totals['products'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl">
        <div class="card stat-card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-info-subtle text-info"><i class="bi bi-tags"></i></div>
                <div class="ms-3">
                    <div class="stat-label">{{ __('app.dashboard.total_categories') }}</div>
                    <div class="stat-value">{{ $totals['categories'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl">
        <div class="card stat-card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-danger-subtle text-danger"><i class="bi bi-exclamation-triangle"></i></div>
                <div class="ms-3">
                    <div class="stat-label">{{ __('app.dashboard.low_stock_products') }}</div>
                    <div class="stat-value text-danger">{{ $totals['low_stock'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl">
        <div class="card stat-card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-success-subtle text-success"><i class="bi bi-box-arrow-in-down"></i></div>
                <div class="ms-3">
                    <div class="stat-label">{{ __('app.dashboard.total_entries') }}</div>
                    <div class="stat-value">{{ $totals['entries'] }}</div>
                    <small class="text-secondary">{{ $entryUnits }} {{ __('app.dashboard.units') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl">
        <div class="card stat-card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-warning-subtle text-warning-emphasis"><i class="bi bi-box-arrow-up"></i></div>
                <div class="ms-3">
                    <div class="stat-label">{{ __('app.dashboard.total_exits') }}</div>
                    <div class="stat-value">{{ $totals['exits'] }}</div>
                    <small class="text-secondary">{{ $exitUnits }} {{ __('app.dashboard.units') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- STOCK BAJO + MOVIMIENTOS RECIENTES --}}
<div class="row g-3 mt-1">
    <div class="col-12 col-xxl-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong><i class="bi bi-exclamation-triangle text-danger me-2"></i>{{ __('app.dashboard.low_stock_products') }}</strong>
                @can('products.view')
                    <a href="{{ route('products.index') }}" class="small">{{ __('app.dashboard.view_all') }}</a>
                @endcan
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('app.common.code') }}</th>
                            <th>{{ __('app.common.product') }}</th>
                            <th class="text-center">{{ __('app.common.stock') }}</th>
                            <th class="text-center">{{ __('app.common.minimum') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($lowStockProducts as $product)
                        <tr>
                    <td class="cell-code">{{ $product->code }}</td>
                    <td class="cell-truncate" title="{{ $product->name }}">{{ $product->name }}</td>
                    <td class="text-center cell-num"><span class="badge text-bg-danger">{{ $product->stock }}</span></td>
                    <td class="text-center cell-num">{{ $product->stock_min }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty-state">
                                <i class="bi bi-check-circle text-success"></i>
                                {{ __('app.dashboard.empty_low_stock') }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12 col-xxl-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong><i class="bi bi-arrow-left-right me-2"></i>{{ __('app.dashboard.recent_movements') }}</strong>
                @can('movements.view')
                    <a href="{{ route('movimientos.index') }}" class="small">{{ __('app.dashboard.view_history') }}</a>
                @endcan
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('app.common.type') }}</th>
                            <th>{{ __('app.common.product') }}</th>
                            <th class="text-center">{{ __('app.common.quantity') }}</th>
                            <th class="text-center">{{ __('app.common.date') }}</th>
                            <th>{{ __('app.common.username') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($recentMovements as $movement)
                        <tr>
                            <td>
                                @if ($movement->isEntrada())
                                    <span class="badge text-bg-success">{{ __('app.entries.title_one') }}</span>
                                @else
                                    <span class="badge text-bg-warning">{{ __('app.exits.title_one') }}</span>
                                @endif
                            </td>
                            <td class="cell-truncate" title="{{ $movement->product?->name }}">{{ $movement->product?->name ?? '—' }}</td>
                            <td class="text-center cell-num">{{ $movement->quantity }}</td>
                            <td class="text-center cell-num">{{ $movement->date->format('d/m/Y') }}</td>
                            <td>{{ $movement->user?->name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state">
                                <i class="bi bi-arrow-left-right"></i>
                                {{ __('app.dashboard.empty_movements') }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</div>

{{-- Avisos de cambios recibidos en tiempo real --}}
<div class="toast-container position-fixed bottom-0 end-0 p-3" id="live-toasts" style="z-index: 1080;"></div>

{{-- Cliente WebSocket (Laravel Echo + Laravel Reverb) --}}
@push('scripts')
<script src="https://js.pusher.com/8.6.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@2.6.3/dist/echo.iife.js"></script>
<script>
    if (window.Echo && typeof window.Echo === 'object' && window.Echo.default) {
        window.Echo = window.Echo.default;
    }

    window.liveLabels = {
        online: @json(__('app.live.online')),
        offline: @json(__('app.live.offline')),
        pending: @json(__('app.live.pending')),
        notConfigured: @json(__('app.live.not_configured')),
        refreshError: @json(__('app.live.refresh_error')),
        close: @json(__('app.common.close')),
    };

    window.reverbConfig = {
        key: @json(config('broadcasting.connections.reverb.key')),
        host: @json(config('broadcasting.connections.reverb.options.host', 'localhost')),
        port: @json((int) config('broadcasting.connections.reverb.options.port', 8080)),
        scheme: @json(config('broadcasting.connections.reverb.options.scheme', 'http')),
    };
</script>
<script src="{{ asset('js/inventory-live.js') }}?v={{ time() }}"></script>
@endpush
</x-layouts.app>
