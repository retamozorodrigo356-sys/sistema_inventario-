<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.auth.verify_request_title') }} · {{ __('app.brand') }}</title>
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
                <p class="mb-0 opacity-75">{{ __('app.auth.verify_request_subtitle') }}</p>
            </div>

            @include('partials.locale-switcher')

            <div class="card border-0 shadow-lg">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3"><i class="bi bi-envelope-check me-2"></i>{{ __('app.auth.verify_request_heading') }}</h2>

                    <p class="small text-secondary">{!! __('app.auth.verify_request_help') !!}</p>

                    @if (session('success'))
                        <div class="alert alert-success py-2">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
                    @endif

                    <form action="{{ route('verification.request.store') }}" method="POST" autocomplete="off">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('app.common.email') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" id="email" class="form-control"
                                       value="{{ old('email') }}" placeholder="{{ __('app.auth.email_placeholder') }}" required autofocus>
                            </div>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-send me-2"></i>{{ __('app.auth.send_link') }}
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="{{ route('login') }}" class="small">
                            <i class="bi bi-arrow-left me-1"></i>{{ __('app.auth.back_login') }}
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
