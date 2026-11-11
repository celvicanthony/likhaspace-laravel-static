@extends('layouts.app')

@section('title', 'Create an account | LikhaSpace')

@section('content')
<div class="mx-auto" style="max-width: 560px">
    <section class="bg-studio-card rounded-4 shadow-sm p-4 p-md-5">
        <div class="home-kicker">Join our community</div>
        <h1 class="h3 fw-bold mt-2 mb-4">Create your LikhaSpace account</h1>

        <form method="POST" action="{{ route('register.submit') }}">
            @csrf

            <div class="row g-3 mb-3">
                <div class="col-12 col-sm-6">
                    <label for="first_name" class="form-label">{{ __('First Name') }}</label>
                    <input id="first_name" class="form-control @error('first_name') is-invalid @enderror"
                        type="text" name="first_name" value="{{ old('first_name') }}" required autofocus
                        autocomplete="given-name" maxlength="80">
                    @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-sm-6">
                    <label for="middle_name" class="form-label">{{ __('Middle Name') }}</label>
                    <input id="middle_name" class="form-control @error('middle_name') is-invalid @enderror"
                        type="text" name="middle_name" value="{{ old('middle_name') }}"
                        autocomplete="additional-name" maxlength="80">
                    @error('middle_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-sm-8">
                    <label for="last_name" class="form-label">{{ __('Surname') }}</label>
                    <input id="last_name" class="form-control @error('last_name') is-invalid @enderror"
                        type="text" name="last_name" value="{{ old('last_name') }}" required
                        autocomplete="family-name" maxlength="80">
                    @error('last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-sm-4">
                    <label for="name_extension" class="form-label">{{ __('Extension') }} <span class="text-muted">(optional)</span></label>
                    <input id="name_extension" class="form-control @error('name_extension') is-invalid @enderror"
                        type="text" name="name_extension" value="{{ old('name_extension') }}"
                        placeholder="Jr., III" autocomplete="honorific-suffix" maxlength="20">
                    @error('name_extension')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">{{ __('Email') }}</label>
                <input id="email" class="form-control @error('email') is-invalid @enderror"
                    type="email" name="email" value="{{ old('email') }}" required
                    autocomplete="username">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">{{ __('Account type') }}</label>
                <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                    <option value="client" @selected(old('role', 'client') === 'client')>Client</option>
                    <option value="artist" @selected(old('role') === 'artist')>Artist</option>
                </select>
                @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">{{ __('Password') }}</label>
                <input id="password" class="form-control @error('password') is-invalid @enderror"
                    type="password" name="password" required autocomplete="new-password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                <input id="password_confirmation" class="form-control" type="password"
                    name="password_confirmation" required autocomplete="new-password">
            </div>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <a href="{{ route('login') }}">{{ __('Already registered?') }}</a>
                <button class="btn btn-likha rounded-pill px-4" type="submit">{{ __('Register') }}</button>
            </div>
        </form>
    </section>
</div>
@endsection
