{{-- Aviso de correo no verificado (no bloquea el acceso) --}}
@if (auth()->check() && auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
    <div class="alert alert-warning d-flex flex-wrap align-items-center gap-2" role="alert">
        <i class="bi bi-envelope-exclamation fs-5"></i>
        <span class="small mb-0">{!! __('app.auth.email_not_verified', ['email' => e(auth()->user()->email)]) !!}</span>
        <form action="{{ route('verification.resend') }}" method="POST" class="d-inline ms-auto mb-0">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-warning">
                <i class="bi bi-arrow-clockwise me-1"></i>{{ __('app.auth.resend') }}
            </button>
        </form>
    </div>
@endif
