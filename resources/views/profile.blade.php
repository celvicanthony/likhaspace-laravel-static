@extends('layouts.app')

@section('title', 'My Profile | LikhaSpace')

@push('styles')
<style>
    .studio-page { max-width: 960px; color: var(--studio-text); }
    .studio-page h1, .studio-page h2 { color: var(--studio-text); }
    .studio-page .form-control, .studio-page .form-select { border-color: var(--studio-border); background-color: var(--surface-soft); color: var(--studio-text); }
    .profile-avatar { display: grid; width: 88px; height: 88px; place-items: center; overflow: hidden; border: 2px solid var(--studio-accent); border-radius: 50%; background: var(--surface-soft); color: var(--studio-link); font-size: 1.8rem; }
    .profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .studio-page .form-check-input:checked { border-color: var(--studio-primary); background-color: var(--studio-primary); }
</style>
@endpush

@section('content')
<div class="studio-page mx-auto">
    <header class="mb-4">
        <div class="home-kicker">Account settings</div>
        <h1 class="h3 fw-bold mb-1">My profile</h1>
        <p class="text-muted mb-0">Update your account credentials, personal details, and delivery preferences.</p>
    </header>

    @if (session('status'))<div class="alert bg-studio-card rounded-4" role="status">{{ session('status') }}</div>@endif
    @if ($errors->any())<div class="alert bg-studio-card rounded-4" role="alert">{{ $errors->first() }}</div>@endif

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        <section class="bg-studio-card rounded-4 p-3 p-md-4 mb-3">
            <h2 class="h6 fw-bold mb-3">Personal details</h2>
            <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
                <div class="profile-avatar" id="avatar-preview" aria-label="Avatar preview">
                    @if (!empty($profile['avatar']))
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($profile['avatar']) }}" alt="Profile photo">
                    @else
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    @endif
                </div>
                <div class="flex-grow-1">
                    <label class="form-label small" for="avatar">Profile photo</label>
                    <input class="form-control" type="file" id="avatar" name="avatar" accept="image/png,image/jpeg,image/webp">
                    <div class="form-text text-muted">PNG, JPG, or WebP up to 5 MB.</div>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-12 col-md-6"><label class="form-label" for="name">Full name</label><input class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" autocomplete="name" required></div>
                <div class="col-12 col-md-6"><label class="form-label" for="email">Email address</label><input class="form-control" id="email" name="email" type="email" value="{{ old('email', $user->email) }}" autocomplete="email" required></div>
                <div class="col-12"><label class="form-label" for="bio">Bio</label><textarea class="form-control" id="bio" name="bio" rows="3" maxlength="400">{{ old('bio', $profile['bio'] ?? '') }}</textarea></div>
                <div class="col-12 col-md-6"><label class="form-label" for="contact">Contact number</label><input class="form-control" id="contact" name="contact" type="tel" value="{{ old('contact', $profile['contact'] ?? '') }}" autocomplete="tel"></div>
            </div>
        </section>

        <section class="bg-studio-card rounded-4 p-3 p-md-4 mb-3">
            <h2 class="h6 fw-bold mb-3">Shipping address</h2>
            <div class="row g-3">
                <div class="col-12"><label class="form-label" for="address">Street address</label><input class="form-control" id="address" name="address" value="{{ old('address', $profile['address'] ?? '') }}" autocomplete="street-address"></div>
                <div class="col-12 col-md-5"><label class="form-label" for="city">City</label><input class="form-control" id="city" name="city" value="{{ old('city', $profile['city'] ?? '') }}" autocomplete="address-level2"></div>
                <div class="col-12 col-md-4"><label class="form-label" for="province">Province</label><input class="form-control" id="province" name="province" value="{{ old('province', $profile['province'] ?? '') }}" autocomplete="address-level1"></div>
                <div class="col-12 col-md-3"><label class="form-label" for="postal">Postal code</label><input class="form-control" id="postal" name="postal" inputmode="numeric" value="{{ old('postal', $profile['postal'] ?? '') }}" autocomplete="postal-code"></div>
            </div>
        </section>

        <section class="bg-studio-card rounded-4 p-3 p-md-4 mb-3">
            <h2 class="h6 fw-bold mb-3">Change password</h2>
            <div class="row g-3">
                <div class="col-12 col-md-4"><label class="form-label" for="current-password">Current password</label><input class="form-control" id="current-password" name="current_password" type="password" autocomplete="current-password"></div>
                <div class="col-12 col-md-4"><label class="form-label" for="new-password">New password</label><input class="form-control" id="new-password" name="new_password" type="password" minlength="8" autocomplete="new-password"></div>
                <div class="col-12 col-md-4"><label class="form-label" for="confirm-password">Confirm new password</label><input class="form-control" id="confirm-password" name="new_password_confirmation" type="password" minlength="8" autocomplete="new-password"></div>
            </div>
            <div class="form-text text-muted">Leave the password fields blank to keep your current password.</div>
        </section>

        <section class="bg-studio-card rounded-4 p-3 p-md-4 mb-3">
            <h2 class="h6 fw-bold mb-3">Notifications</h2>
            <div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" id="email-updates" name="notifications[artwork_updates]" value="1" @checked(old('notifications.artwork_updates', $profile['notifications']['artwork_updates'] ?? true))><label class="form-check-label" for="email-updates">Artwork and order updates</label></div>
            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="newsletters" name="notifications[newsletters]" value="1" @checked(old('notifications.newsletters', $profile['notifications']['newsletters'] ?? false))><label class="form-check-label" for="newsletters">Studio news and announcements</label></div>
        </section>

        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
            <p class="text-muted small mb-0">Profile changes are saved to your LikhaSpace account.</p>
            <button class="btn btn-violet rounded-pill px-4" type="submit"><i class="bi bi-check2 me-1"></i>Save changes</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('avatar')?.addEventListener('change', (event) => {
        const file = event.currentTarget.files?.[0];
        if (!file) return;
        const image = new Image();
        image.alt = 'Selected profile photo preview';
        image.src = URL.createObjectURL(file);
        document.getElementById('avatar-preview').replaceChildren(image);
    });
</script>
@endpush
