<x-layouts.app :title="auth()->id() === $user->id ? __('app.users.edit_self_title') : __('app.users.edit_title')">
<div class="page-header">
    <div>
        <h1 class="h4 mb-1">{{ auth()->id() === $user->id ? __('app.users.edit_self_heading') : __('app.users.edit_heading') }}</h1>
        <p class="text-secondary mb-0">{!! __('app.users.edit_subtitle', ['name' => e($user->name)]) !!}</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('usuarios.update', $user) }}" method="POST">
            @method('PUT')
            @include('usuarios._form')
        </form>
    </div>
</div>
</x-layouts.app>
