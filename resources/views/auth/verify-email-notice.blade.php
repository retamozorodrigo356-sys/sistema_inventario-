<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.auth.verify_notice_title') }} · {{ __('app.brand') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="login-page">

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-12 col-sm-9 col-md-6 col-lg-5 col-xl-4">

            <div class="text-center text-white mb-4">
                <div class="login-logo mx-auto"><i class="bi bi-box-seam-fill"></i></div>
                <h1 class="h4 mt-3 mb-1">{{ __('app.brand_web') }}</h1>
                <p class="mb-0 opacity-75">{{ __('app.auth.verify_notice_subtitle') }}</p>
            </div>

            @include('partials.locale-switcher')

            <div class="card border-0 shadow-lg">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3"><i class="bi bi-envelope-exclamation me-2"></i>{{ __('app.auth.verify_notice_heading') }}</h2>

                    @if (session('success'))
                        <div class="alert alert-success py-2">{{ session('success') }}</div>
                    @endif

                    <p class="small text-secondary mb-2">
                        {{ __('app.auth.verify_notice_hello') }} <strong>{{ auth()->user()->name }}</strong>:
                        {{ __('app.auth.verify_notice_text') }} <strong>{{ auth()->user()->email }}</strong>.
                    </p>

                    <p class="small text-secondary">{{ __('app.auth.verify_notice_help') }}</p>

                    <form action="{{ route('verification.resend') }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-arrow-clockwise me-2"></i>{{ __('app.auth.resend') }}
                        </button>
                    </form>

                    <form action="{{ route('logout') }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary w-100 py-2">
                            <i class="bi bi-box-arrow-left me-2"></i>{{ __('app.nav.logout') }}
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
