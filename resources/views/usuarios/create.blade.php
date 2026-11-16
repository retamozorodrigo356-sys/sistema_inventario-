<x-layouts.app :title="__('app.users.create_title')">
<div class="page-header">
    <div>
        <h1 class="h4 mb-1">{{ __('app.users.create_heading') }}</h1>
        <p class="text-secondary mb-0">{{ __('app.users.create_subtitle') }}</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('usuarios.store') }}" method="POST">
            @include('usuarios._form')
        </form>
    </div>
</div>
</x-layouts.app>
