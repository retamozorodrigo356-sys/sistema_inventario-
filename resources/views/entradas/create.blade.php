<x-layouts.app :title="__('app.entries.create_title')">
<div class="page-header">
    <div>
        <h1 class="h4 mb-1">{{ __('app.entries.create_heading') }}</h1>
        <p class="text-secondary mb-0">{{ __('app.entries.create_subtitle') }}</p>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-8 col-xl-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">                <form action="{{ route('entradas.store') }}" method="POST">
                    @include('entradas._form')
                </form>
            </div>
        </div>
    </div>
</div>
</x-layouts.app>
