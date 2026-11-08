@extends('layouts.app')

@section('title', 'Create an account | LikhaSpace')

@section('content')
<div class="mx-auto" style="max-width: 560px">
    <section class="bg-studio-card rounded-4 shadow-sm p-4 p-md-5">
        <div class="home-kicker">Join our community</div>
        <h1 class="h3 fw-bold mt-2 mb-4">Create your LikhaSpace account</h1>

        <form method="POST" action="{{ route('register.submit') }}">
            @csrf

            <div class="mb-3">
                <h2 class="h6 fw-bold">Personal details</h2>
                <p class="form-text text-muted">Your birthdate and gender are private and optional.</p>
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="first-name" class="form-label">First name</label>
                        <input id="first-name" class="form-control @error('first_name') is-invalid @enderror"
                            type="text" name="first_name" value="{{ old('first_name') }}" maxlength="60" required autofocus autocomplete="given-name">
                        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="middle-name" class="form-label">Middle name <span class="text-muted">(optional)</span></label>
                        <input id="middle-name" class="form-control @error('middle_name') is-invalid @enderror"
                            type="text" name="middle_name" value="{{ old('middle_name') }}" maxlength="60" autocomplete="additional-name">
                        @error('middle_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="last-name" class="form-label">Last name</label>
                        <input id="last-name" class="form-control @error('last_name') is-invalid @enderror"
                            type="text" name="last_name" value="{{ old('last_name') }}" maxlength="60" required autocomplete="family-name">
                        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="name-extension" class="form-label">Name extension <span class="text-muted">(optional)</span></label>
                        <input id="name-extension" class="form-control @error('name_extension') is-invalid @enderror"
                            type="text" name="name_extension" value="{{ old('name_extension') }}" placeholder="Jr., Sr., III">
                        @error('name_extension')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="display-name" class="form-label">Display name <span class="text-muted">(optional)</span></label>
                        <input id="display-name" class="form-control @error('display_name') is-invalid @enderror"
                            type="text" name="display_name" value="{{ old('display_name') }}" autocomplete="nickname">
                        <div class="form-text text-muted">Shown publicly instead of your full legal name.</div>
                        @error('display_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="birthdate" class="form-label">Birthdate <span class="text-muted">(optional)</span></label>
                        <input id="birthdate" class="form-control @error('birthdate') is-invalid @enderror"
                            type="date" name="birthdate" value="{{ old('birthdate') }}" max="{{ now()->subDay()->toDateString() }}" autocomplete="bday">
                        @error('birthdate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="gender" class="form-label">Gender <span class="text-muted">(optional)</span></label>
                        <select id="gender" class="form-select @error('gender') is-invalid @enderror" name="gender">
                            <option value="">Prefer not to share</option>
                            <option value="female" @selected(old('gender') === 'female')>Female</option>
                            <option value="male" @selected(old('gender') === 'male')>Male</option>
                            <option value="non_binary" @selected(old('gender') === 'non_binary')>Non-binary</option>
                            <option value="other" @selected(old('gender') === 'other')>Other</option>
                            <option value="prefer_not_to_say" @selected(old('gender') === 'prefer_not_to_say')>Prefer not to say</option>
                        </select>
                        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="contact" class="form-label">Contact number <span class="text-muted">(optional)</span></label>
                        <input id="contact" class="form-control @error('contact') is-invalid @enderror"
                            type="tel" name="contact" value="{{ old('contact') }}" autocomplete="tel">
                        @error('contact')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
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
