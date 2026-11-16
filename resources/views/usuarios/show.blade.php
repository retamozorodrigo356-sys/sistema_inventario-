<x-layouts.app :title="__('app.users.profile_title')">
<div class="page-header">
    <div>
        <h1 class="h4 mb-1">{{ __('app.users.profile_heading') }}</h1>
        <p class="text-secondary mb-0">{!! __('app.users.profile_subtitle', ['name' => e($user->name)]) !!}</p>
    </div>
    <a href="{{ auth()->user()->hasPermission('users.view') ? route('usuarios.index') : route('dashboard') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>{{ __('app.common.back') }}
    </a>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <span class="avatar fs-4"><i class="bi bi-person-fill"></i></span>
                    <div>
                        <h2 class="h5 mb-1">{{ $user->name }}</h2>
                        @if ($user->role?->slug === 'admin')
                            <span class="badge text-bg-primary">{{ $user->role?->name ?? __('app.common.no_role') }}</span>
                        @elseif ($user->role?->slug === 'encargado')
                            <span class="badge text-bg-success">{{ $user->role?->name ?? __('app.common.no_role') }}</span>
                        @else
                            <span class="badge text-bg-secondary">{{ $user->role?->name ?? __('app.common.no_role') }}</span>
                        @endif
                    </div>
                </div>

                <dl class="row mb-0">
                    <dt class="col-5 text-secondary fw-normal">{{ __('app.common.username') }}</dt>
                    <dd class="col-7">{{ $user->username }}</dd>

                    <dt class="col-5 text-secondary fw-normal">{{ __('app.common.email') }}</dt>
                    <dd class="col-7">{{ $user->email }}</dd>

                    <dt class="col-5 text-secondary fw-normal">{{ __('app.users.email_verified') }}</dt>
                    <dd class="col-7 mb-0">
                        @if ($user->hasVerifiedEmail())
                            <span class="badge text-bg-success"><i class="bi bi-check-circle me-1"></i>{{ __('app.users.verified') }}</span>
                        @else
                            <span class="badge text-bg-warning text-dark"><i class="bi bi-exclamation-circle me-1"></i>{{ __('app.users.unverified') }}</span>
                        @endif
                    </dd>
                </dl>
            </div>
            @if (auth()->id() === $user->id || auth()->user()->hasPermission('users.update'))
                <div class="card-footer bg-white d-flex gap-2 flex-wrap">
                    <a href="{{ route('usuarios.edit', $user) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i>{{ __('app.common.edit') }}
                    </a>
                    <a href="{{ route('usuarios.password', $user) }}" class="btn btn-outline-primary">
                        <i class="bi bi-key me-1"></i>{{ __('app.users.change_password') }}
                    </a>
                    @if (auth()->user()->hasPermission('users.view'))
                        <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">{{ __('app.common.back') }}</a>
                    @endif
                    @if (auth()->id() === $user->id)
                        <button type="button" class="btn btn-outline-danger ms-lg-auto"
                                data-delete-url="{{ route('usuarios.destroy', $user) }}"
                                data-delete-name="{{ __('app.users.self_delete_name', ['name' => $user->name]) }}">
                            <i class="bi bi-trash me-1"></i>{{ __('app.users.self_delete') }}
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="col-12 col-lg-7">
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <div class="card stat-card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2">
                            <span class="stat-icon"><i class="bi bi-arrow-left-right"></i></span>
                            <span class="stat-label">{{ __('app.users.movements') }}</span>
                        </div>
                        <div class="stat-value mt-2">{{ $totalMovements }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card stat-card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2">
                            <span class="stat-icon"><i class="bi bi-box-arrow-in-down"></i></span>
                            <span class="stat-label">{{ __('app.users.entries') }}</span>
                        </div>
                        <div class="stat-value mt-2">{{ $entradas }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card stat-card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2">
                            <span class="stat-icon"><i class="bi bi-box-arrow-up"></i></span>
                            <span class="stat-label">{{ __('app.users.exits') }}</span>
                        </div>
                        <div class="stat-value mt-2">{{ $salidas }}</div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <strong>{{ __('app.users.recent_movements') }}</strong>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>{{ __('app.common.type') }}</th>
                                    <th>{{ __('app.common.product') }}</th>
                                    <th class="text-center">{{ __('app.common.quantity') }}</th>
                                    <th class="text-center">{{ __('app.common.date') }}</th>
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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="empty-state">
                                        <i class="bi bi-arrow-left-right"></i>
                                        {{ __('app.users.empty_movements') }}
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
</div>
</x-layouts.app>
