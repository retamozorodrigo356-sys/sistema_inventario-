<x-layouts.app :title="__('app.entries.title')">
<div class="page-header">
    <div>
        <h1 class="h4 mb-1">{{ __('app.entries.heading') }}</h1>
        <p class="text-secondary mb-0">{{ __('app.entries.subtitle') }}</p>
    </div>
    @can('entries.create')
        <a href="{{ route('entradas.create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg me-1"></i>{{ __('app.entries.register') }}
        </a>
    @endcan
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="text-center">#</th>
                    <th>{{ __('app.common.product') }}</th>
                    <th class="text-center">{{ __('app.common.quantity') }}</th>
                    <th class="text-center">{{ __('app.common.date') }}</th>
                    <th>{{ __('app.common.observation') }}</th>
                    <th>{{ __('app.common.username') }}</th>
                    <th class="text-center">{{ __('app.common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($movements as $movement)
                <tr>
                    <td class="text-center cell-code">{{ $movement->id }}</td>
                    <td class="cell-truncate" title="{{ $movement->product?->name }}">
                        <span class="fw-semibold">{{ $movement->product?->name ?? '—' }}</span>
                        <span class="cell-sub">{{ $movement->product?->code }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge text-bg-success cell-num">+{{ $movement->quantity }}</span>
                    </td>
                    <td class="text-center cell-num">{{ $movement->date->format('d/m/Y') }}</td>
                    <td>{{ $movement->observation ?? '—' }}</td>
                    <td>{{ $movement->user?->name ?? '—' }}</td>
                    <td class="text-center cell-actions">
                        @can('entries.update')
                            <a href="{{ route('entradas.edit', $movement) }}" class="btn btn-sm btn-outline-primary" title="{{ __('app.common.edit') }}">
                                <i class="bi bi-pencil"></i>
                            </a>
                        @endcan
                        @can('entries.delete')
                            <button type="button" class="btn btn-sm btn-outline-danger" title="{{ __('app.common.delete') }}"
                                    data-delete-url="{{ route('entradas.destroy', $movement) }}"
                                    data-delete-name="{{ __('app.entries.delete_name', ['id' => $movement->id, 'product' => $movement->product?->name ?? __('app.common.product')]) }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        @endcan
                        @cannot('entries.update')
                            @cannot('entries.delete')
                                <span class="text-secondary small">{{ __('app.common.read_only') }}</span>
                            @endcannot
                        @endcannot
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty-state">
                        <i class="bi bi-box-arrow-in-down"></i>
                        {{ __('app.entries.empty') }}
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
