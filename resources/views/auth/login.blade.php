@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('auth_header', '') {{-- Remove o cabeçalho padrão para personalizar --}}

@section('auth_body')
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                {{-- Adiciona a imagem do logo aqui --}}
                <img src="{{ asset('img/logo.png') }}" alt="Logo Criatório Coroné" class="img-fluid mb-2" style="max-width: 150px; display: block; margin: 0 auto;">
                <a href="{{ url('/') }}" class="h1"><b>Criatório</b>Coroné</a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">{{ __('Sign in to start your session') }}</p>

                <form action="{{ route('login') }}" method="post">
                    @csrf

                    {{-- Email field --}}
                    <div class="input-group mb-3">
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" placeholder="{{ __('Email') }}" autofocus>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    {{-- Password field --}}
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                               placeholder="{{ __('Password') }}">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    {{-- Remember Me checkbox --}}
                    <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label for="remember">
                                    {{ __('Remember Me') }}
                                </label>
                            </div>
                        </div>
                        {{-- Login button --}}
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">{{ __('Sign In') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('auth_footer')
    {{-- Password reset link --}}
    @if (Route::has('password.request'))
        <p class="my-0">
            <a href="{{ route('password.request') }}">
                {{ __('I forgot my password') }}
            </a>
        </p>
    @endif

    {{-- Register link --}}
    @if (Route::has('register'))
        <p class="my-0">
            <a href="{{ route('register') }}">
                {{ __('Register a new membership') }}
            </a>
        </p>
    @endif
@stop
