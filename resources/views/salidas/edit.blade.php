<x-layouts.app :title="__('app.exits.edit_title')">
<div class="page-header">
    <div>
        <h1 class="h4 mb-1">{{ __('app.exits.edit_heading', ['id' => $movement->id]) }}</h1>
        <p class="text-secondary mb-0">
            {!! __('app.exits.edit_subtitle') !!}
        </p>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-8 col-xl-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('salidas.update', $movement) }}" method="POST">
                    @method('PUT')
                    @include('salidas._form', ['submitLabel' => __('app.common.save_changes')])
                </form>
            </div>
        </div>
    </div>
</div>
</x-layouts.app>
