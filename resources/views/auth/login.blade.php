@extends('layouts.app')

@section('title', 'Sign in | LikhaSpace')

@section('content')
<div class="mx-auto" style="max-width: 520px">
    <section class="bg-studio-card rounded-4 shadow-sm p-4 p-md-5">
        <div class="home-kicker">Welcome back</div>
        <h1 class="h3 fw-bold mt-2">Sign in to LikhaSpace</h1>
        <p class="text-muted mb-4">Continue exploring original art and your saved collections.</p>

        @if ($errors->any())
            <div class="alert bg-studio-card rounded-4" role="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="email">Email address</label>
                <input class="form-control" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label" for="password">Password</label>
                <input class="form-control" id="password" name="password" type="password" autocomplete="current-password" required>
            </div>
            <div class="form-check mb-4">
                <input class="form-check-input" id="remember" name="remember" type="checkbox" value="1">
                <label class="form-check-label" for="remember">Keep me signed in</label>
            </div>
            <button class="btn btn-likha rounded-pill w-100 py-2" type="submit">Sign in</button>
        </form>

        <p class="text-muted text-center small mt-4 mb-0">New to LikhaSpace? <a href="{{ route('register') }}">Create an account</a></p>
    </section>
</div>
@endsection
