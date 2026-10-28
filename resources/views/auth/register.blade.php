@extends('layouts.app')

@section('title', 'Create account | LikhaSpace')

@section('content')
<div class="mx-auto" style="max-width: 560px">
    <section class="bg-studio-card rounded-4 shadow-sm p-4 p-md-5">
        <div class="home-kicker">Join the marketplace</div>
        <h1 class="h3 fw-bold mt-2">Create your account</h1>
        <p class="text-muted mb-4">Join as a collector or create an independent artist studio.</p>

        @if ($errors->any())
            <div class="alert bg-studio-card rounded-4" role="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('register.submit') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="name">Name</label>
                <input class="form-control" id="name" name="name" value="{{ old('name') }}" autocomplete="name" maxlength="120" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label" for="email">Email address</label>
                <input class="form-control" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="role">Account type</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="client" @selected(old('role', 'client') === 'client')>Client / collector</option>
                    <option value="artist" @selected(old('role') === 'artist')>Artist / creator</option>
                </select>
                <div class="form-text text-muted">Admin access cannot be requested through public registration.</div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="password">Password</label>
                <input class="form-control" id="password" name="password" type="password" autocomplete="new-password" minlength="8" required>
            </div>
            <div class="mb-4">
                <label class="form-label" for="password_confirmation">Confirm password</label>
                <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required>
            </div>
            <button class="btn btn-likha rounded-pill w-100 py-2" type="submit">Create account</button>
        </form>

        <p class="text-muted text-center small mt-4 mb-0">Already registered? <a href="{{ route('login') }}">Sign in</a></p>
    </section>
</div>
@endsection
