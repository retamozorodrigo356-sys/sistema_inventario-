<x-layouts.app :title="__('app.categories.edit_title')">
<div class="page-header">
    <div>
        <h1 class="h4 mb-1">{{ __('app.categories.edit_heading') }}</h1>
        <p class="text-secondary mb-0">{!! __('app.categories.edit_subtitle', ['name' => e($category->name)]) !!}</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('categories.update', $category) }}" method="POST">
            @method('PUT')
            @include('categories._form')
        </form>
    </div>
</div>
</x-layouts.app>
