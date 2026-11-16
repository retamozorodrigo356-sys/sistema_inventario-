<x-layouts.app :title="__('app.movements.title')">
<div class="page-header">
    <div>
        <h1 class="h4 mb-1">{{ __('app.movements.heading') }}</h1>
        <p class="text-secondary mb-0">{{ __('app.movements.subtitle') }}</p>
    </div>
</div>

{{-- FILTROS --}}
<form action="{{ route('movimientos.index') }}" method="GET" class="filter-bar mb-3">
    <div class="row g-2">
        <div class="col-12 col-md-3">
            <select name="type" class="form-select">
                <option value="">{{ __('app.movements.all_types') }}</option>
                <option value="entrada" @selected(request('type') === 'entrada')>{{ __('app.entries.title') }}</option>
                <option value="salida" @selected(request('type') === 'salida')>{{ __('app.exits.title') }}</option>
            </select>
        </div>
        <div class="col-12 col-md-3">
            <input type="text" name="search" class="form-control" placeholder="{{ __('app.movements.search_placeholder') }}"
                   value="{{ request('search') }}">
        </div>
        <div class="col-6 col-md-2">
            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="col-6 col-md-2">
            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
        <div class="col-12 col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-outline-primary flex-fill">
                <i class="bi bi-funnel me-1"></i>{{ __('app.common.filter') }}
            </button>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="text-center">#</th>
                    <th>{{ __('app.common.type') }}</th>
                    <th>{{ __('app.common.product') }}</th>
                    <th class="text-center">{{ __('app.common.quantity') }}</th>
                    <th class="text-center">{{ __('app.common.date') }}</th>
                    <th>{{ __('app.common.observation') }}</th>
                    <th>{{ __('app.common.username') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($movements as $movement)
                <tr>
                    <td class="text-center cell-code">{{ $movement->id }}</td>
                    <td>
                        @if ($movement->isEntrada())
                            <span class="badge text-bg-success"><i class="bi bi-arrow-down-circle me-1"></i>{{ __('app.entries.title_one') }}</span>
                        @else
                            <span class="badge text-bg-warning text-dark"><i class="bi bi-arrow-up-circle me-1"></i>{{ __('app.exits.title_one') }}</span>
                        @endif
                    </td>
                    <td class="cell-truncate" title="{{ $movement->product?->name }}">
                        <span class="fw-semibold">{{ $movement->product?->name ?? '—' }}</span>
                        <span class="cell-sub">{{ $movement->product?->code }}</span>
                    </td>
                    <td class="text-center cell-num">{{ $movement->quantity }}</td>
                    <td class="text-center cell-num">{{ $movement->date->format('d/m/Y') }}</td>
                    <td>{{ $movement->observation ?? '—' }}</td>
                    <td>{{ $movement->user?->name ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty-state">
                        <i class="bi bi-arrow-left-right"></i>
                        {{ __('app.movements.empty') }}
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if ($movements->hasPages())
        <div class="card-footer bg-white">
            {{ $movements->links() }}
        </div>
    @endif
</div>
</x-layouts.app>
