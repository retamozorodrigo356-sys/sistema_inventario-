{{-- Selector de idioma para las pantallas de acceso --}}
<div class="d-flex justify-content-center gap-2 mb-3">
    @foreach (config('app.available_locales') as $code => $label)
        <a href="{{ route('locale.switch', $code) }}"
           class="btn btn-sm {{ app()->getLocale() === $code ? 'btn-light text-dark' : 'btn-outline-light' }}"
           title="{{ $label }}">
            {{ strtoupper($code) }}
        </a>
    @endforeach
</div>
