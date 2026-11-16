<x-layouts.app :title="__('app.entries.edit_title')">
<div class="page-header">
    <div>
        <h1 class="h4 mb-1">{{ __('app.entries.edit_heading', ['id' => $movement->id]) }}</h1>
        <p class="text-secondary mb-0">{{ __('app.entries.edit_subtitle') }}</p>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-8 col-xl-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('entradas.update', $movement) }}" method="POST">
                    @method('PUT')
                    @include('entradas._form', ['submitLabel' => __('app.common.save_changes')])
                </form>
            </div>
        </div>
    </div>
</div>
</x-layouts.app>
