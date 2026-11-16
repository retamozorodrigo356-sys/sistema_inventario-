<x-layouts.app :title="__('app.products.edit_title')">
<div class="page-header">
    <div>
        <h1 class="h4 mb-1">{{ __('app.products.edit_heading') }}</h1>
        <p class="text-secondary mb-0">{!! __('app.products.edit_subtitle', ['name' => e($product->name)]) !!}</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('products.update', $product) }}" method="POST">
            @method('PUT')
            @include('products._form')
        </form>
    </div>
</div>
</x-layouts.app>
