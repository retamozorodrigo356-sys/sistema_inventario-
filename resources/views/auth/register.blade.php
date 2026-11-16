<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.auth.register_title') }} · {{ __('app.brand') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="login-page">

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100 py-4">
        <div class="col-12 col-sm-9 col-md-6 col-lg-5 col-xl-4">

            <div class="text-center text-white mb-4">
                <div class="login-logo mx-auto"><i class="bi bi-box-seam-fill"></i></div>
                <h1 class="h4 mt-3 mb-1">{{ __('app.brand_web') }}</h1>
                <p class="mb-0 opacity-75">{{ __('app.auth.register_subtitle') }}</p>
            </div>

            @include('partials.locale-switcher')

            <div class="card border-0 shadow-lg">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3"><i class="bi bi-person-plus me-2"></i>{{ __('app.auth.register_heading') }}</h2>

                    <p class="small text-secondary">{!! __('app.auth.register_help') !!}</p>

                    @if ($errors->any())
                        <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
                    @endif

                    <form action="{{ route('register.store') }}" method="POST" autocomplete="off">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('app.auth.full_name') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="name" id="name" class="form-control"
                                       value="{{ old('name') }}" placeholder="{{ __('app.auth.full_name_placeholder') }}" maxlength="150" required autofocus>
                            </div>
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label">{{ __('app.common.username') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-at"></i></span>
                                <input type="text" name="username" id="username" class="form-control"
                                       value="{{ old('username') }}" placeholder="{{ __('app.auth.username_placeholder') }}" maxlength="50" required>
                            </div>
                            @error('username')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('app.common.email') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" id="email" class="form-control"
                                       value="{{ old('email') }}" placeholder="{{ __('app.auth.email_placeholder') }}" maxlength="150" required>
                            </div>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('app.common.password') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" id="password" class="form-control"
                                       placeholder="••••••••" minlength="6" required>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">{{ __('app.common.confirm_password') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       class="form-control" placeholder="••••••••" minlength="6" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-person-check me-2"></i>{{ __('app.auth.create_account') }}
                        </button>
                    </form>

                    <div class="text-center mt-3 small">
                        {{ __('app.auth.have_account') }}
                        <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i>{{ __('app.auth.login_button') }}
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
