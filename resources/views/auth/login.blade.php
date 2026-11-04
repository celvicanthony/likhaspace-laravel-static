@extends('layouts.app')

@section('title', 'Sign in | LikhaSpace')

@section('content')
<div class="mx-auto" style="max-width: 520px">
    <section class="bg-studio-card rounded-4 shadow-sm p-4 p-md-5">
        <div class="home-kicker">Welcome back</div>
        <h1 class="h3 fw-bold mt-2 mb-4">Sign in to LikhaSpace</h1>

        @if (session('status'))
            <div class="alert alert-success" role="status">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">{{ __('Email') }}</label>
                <input id="email" class="form-control @error('email') is-invalid @enderror"
                    type="email" name="email" value="{{ old('email') }}" required autofocus
                    autocomplete="username">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">{{ __('Password') }}</label>
                <input id="password" class="form-control @error('password') is-invalid @enderror"
                    type="password" name="password" required autocomplete="current-password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-check mb-4">
                <input id="remember_me" class="form-check-input" type="checkbox" name="remember" value="1">
                <label for="remember_me" class="form-check-label">{{ __('Remember me') }}</label>
            </div>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
                @endif
                <button class="btn btn-likha rounded-pill px-4" type="submit">{{ __('Log in') }}</button>
            </div>
        </form>

        <p class="text-muted small mt-4 mb-0">
            New to LikhaSpace? <a href="{{ route('register') }}">Create an account</a>
        </p>
    </section>
</div>
@endsection
