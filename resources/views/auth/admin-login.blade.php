@extends('layouts.app')

@section('title', 'Administrative access | LikhaSpace')

@section('content')
<div class="mx-auto" style="max-width: 520px">
    <section class="bg-studio-card rounded-4 shadow-sm p-4 p-md-5">
        <div class="home-kicker">Restricted portal</div>
        <h1 class="h3 fw-bold mt-2">Administrative access</h1>
        <p class="text-muted mb-4">Sign in with an active platform administrator account.</p>

        @if ($errors->any())
            <div class="alert bg-studio-card rounded-4" role="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="admin-email">Administrator email</label>
                <input class="form-control" id="admin-email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
            </div>
            <div class="mb-4">
                <label class="form-label" for="admin-password">Password</label>
                <input class="form-control" id="admin-password" name="password" type="password" autocomplete="current-password" required>
            </div>
            <button class="btn btn-likha rounded-pill w-100 py-2" type="submit">Continue to admin dashboard</button>
        </form>
    </section>
</div>
@endsection
