<x-layouts.app :title="__('app.users.title')">
<div class="page-header">
    <div>
        <h1 class="h4 mb-1">{{ __('app.users.heading') }}</h1>
        <p class="text-secondary mb-0">{{ __('app.users.subtitle') }}</p>
    </div>
    @can('users.create')
        <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i>{{ __('app.users.register') }}
        </a>
    @endcan
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="text-center">#</th>
                    <th>{{ __('app.common.name') }}</th>
                    <th>{{ __('app.common.username') }}</th>
                    <th>{{ __('app.common.email') }}</th>
                    <th class="text-center">{{ __('app.common.role') }}</th>
                    <th class="text-center">{{ __('app.common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($users as $user)
                <tr>
                    <td class="text-center cell-code">{{ $user->id }}</td>
                    <td class="cell-truncate" title="{{ $user->name }}"><span class="fw-semibold">{{ $user->name }}</span></td>
                    <td class="cell-code">{{ $user->username }}</td>
                    <td class="cell-truncate" title="{{ $user->email }}">{{ $user->email }}</td>
                    <td class="text-center">
                        @if ($user->role?->slug === 'admin')
                            <span class="badge text-bg-primary">{{ $user->role?->name }}</span>
                        @elseif ($user->role?->slug === 'encargado')
                            <span class="badge text-bg-success">{{ $user->role?->name }}</span>
                        @else
                            <span class="badge text-bg-secondary">{{ $user->role?->name ?? __('app.common.no_role') }}</span>
                        @endif
                    </td>
                    <td class="text-center cell-actions">
                        <a href="{{ route('usuarios.show', $user) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('app.common.view_profile') }}">
                            <i class="bi bi-eye"></i>
                        </a>
                        @can('users.update')
                            <a href="{{ route('usuarios.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="{{ __('app.common.edit') }}">
                                <i class="bi bi-pencil"></i>
                            </a>
                        @endcan
                        @can('users.delete')
                            <button type="button" class="btn btn-sm btn-outline-danger" title="{{ __('app.common.delete') }}"
                                    data-delete-url="{{ route('usuarios.destroy', $user) }}"
                                    data-delete-name="{{ $user->name }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty-state">
                        <i class="bi bi-people"></i>
                        {{ __('app.users.empty') }}
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if ($users->hasPages())
        <div class="card-footer bg-white">
            {{ $users->links() }}
        </div>
    @endif
</div>
</x-layouts.app>
