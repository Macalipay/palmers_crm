<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <link rel="icon" href="{{ asset('images/logo/logo.png') }}">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Responsive Bootstrap 4 Admin &amp; Dashboard Template">
	<meta name="author" content="Bootlab">

	<title>PALMER</title>
	<link href="{{ asset('docs/css/modern.css')}}" rel="stylesheet">
    <link href="{{ asset('css/customLogin.css') }}" rel="stylesheet">

	<style>
		body {
			opacity: 0;
		}
	</style>
	<script src="{{ asset('docs/js/settings.js')}}"></script>
</head>

<body class="theme-blue">

    <main class="py-4">
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="login-container">
                <div class="container login">
                    <div class="form-section">
                        <div class="row">
                            <div class="col-md-8 slider-0">
                                <div class="form-carousel">
                                    <div id="carouselExampleSlidesOnly" class="carousel slide" data-ride="carousel" data-interval="2000">
                                    <div class="carousel-inner">
                                        <div class="carousel-item active" style="background:url('{{ asset('images/slider-1.jpg')}}');">
                                        </div>
                                        <div class="carousel-item" style="background:url('{{ asset('images/slider-2.jpg')}}');">
                                        </div>
                                        <div class="carousel-item" style="background:url('{{ asset('images/slider-3.jpg')}}');">
                                        </div>
                                        <div class="carousel-item" style="background:url('{{ asset('images/banner_4.jpg')}}');">
                                        </div>
                                        <div class="carousel-item" style="background:url('{{ asset('images/banner_5.jpg')}}');">
                                        </div>
                                        <div class="carousel-item" style="background:url('{{ asset('images/banner_6.jpg')}}');">
                                        </div>
                                        <div class="carousel-item" style="background:url('{{ asset('images/slider-7.jpg')}}');">
                                        </div>
                                        <div class="carousel-item" style="background:url('{{ asset('images/slider-8.jpg')}}');">
                                        </div>
                                        <div class="carousel-item" style="background:url('{{ asset('images/slider-9.jpg')}}');">
                                        </div>
                                        <div class="carousel-item" style="background:url('{{ asset('images/slider-10.jpg')}}');">
                                        </div>
                                        <div class="carousel-item" style="background:url('{{ asset('images/slider-11.jpg')}}');">
                                        </div>
                                        <div class="carousel-item" style="background:url('{{ asset('images/slider-12.jpg')}}');">
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 form-0">
                                <p class="company-logo">
                                    <img src="{{ asset('images/ms-logo.png')}}" class="img-fluid" alt="">
                                </p>
                                <p class="section-title">
                                    <span>Welcome to PALMER!</span>
                                </p>
                                <div class="form-1">
                                    <p class="form-label">Email</p>
                                    <div>
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    </div>
                                </div>
                                <div class="form-1">
                                    <p class="form-label">Password</p>
                                    <div>
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    </div>
                                </div>
                                <div class="form-1">
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}">
                                        <p class="form-label forgot-password">Forgot password?</p>
                                        </a>
                                    @endif
                                </div>
                                <div class="form-1 submit-btn">
                                    <p class="form-btn">
                                        <button type="submit" class="btn btn-primary">
                                            Sign In
                                        </button>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </main>
	<script src="{{ asset('docs/js/app.js')}}"></script>

</body>

</html>

{{-- @extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}
