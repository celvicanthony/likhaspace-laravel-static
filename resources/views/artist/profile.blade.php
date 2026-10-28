@extends('layouts.app')

@section('title', 'Artist Profile | LikhaSpace')

@push('styles')
<style>
    .artist-profile-page { max-width: 960px; margin: 0 auto; }
    .artist-profile-page .form-control, .artist-profile-page .form-select { border-color: var(--studio-border); background-color: var(--surface-soft); color: var(--studio-text); }
    .artist-profile-page .form-control:focus, .artist-profile-page .form-select:focus { border-color: var(--studio-primary); }
</style>
@endpush

@section('content')
<div class="artist-profile-page">
    <header class="mb-4"><div class="home-kicker">Creator workspace</div><h1 class="h2 fw-bold mb-1">Artist profile</h1><p class="text-muted mb-0">Manage the public details collectors see about your studio.</p></header>
    @if (session('status'))<div class="alert bg-studio-card rounded-4" role="status">{{ session('status') }}</div>@endif
    @if ($errors->any())<div class="alert bg-studio-card rounded-4" role="alert">{{ $errors->first() }}</div>@endif
    <form method="POST" action="{{ route('artist.profile.update') }}" enctype="multipart/form-data">
        @csrf
        <section class="bg-studio-card rounded-4 p-3 p-md-4 mb-3">
            <h2 class="h5 fw-bold mb-3">Artist details</h2>
            <div class="row g-3">
                <div class="col-12"><label class="form-label" for="artist-avatar">Profile photo</label><input id="artist-avatar" name="avatar" type="file" class="form-control" accept="image/png,image/jpeg,image/webp"><div class="form-text text-muted">PNG, JPG, or WebP up to 5 MB.</div></div>
                <div class="col-12 col-md-6"><label class="form-label" for="artist-name">Artist or studio name</label><input id="artist-name" name="name" class="form-control" value="{{ old('name', $artist['name']) }}" required></div>
                <div class="col-12 col-md-6"><label class="form-label" for="artist-email">Contact email</label><input id="artist-email" name="email" type="email" class="form-control" value="{{ old('email', $artist['email']) }}" required></div>
                <div class="col-12 col-md-6"><label class="form-label" for="artist-country">Primary culture</label><select id="artist-country" name="country" class="form-select">@foreach (['Philippines', 'Greece', 'Scotland', 'Japan'] as $country)<option value="{{ $country }}" @selected(old('country', $artist['country']) === $country)>{{ $country }}</option>@endforeach</select></div>
                <div class="col-12 col-md-6"><label class="form-label" for="artist-studio">Studio name</label><input id="artist-studio" name="studio" class="form-control" value="{{ old('studio', $artist['studio']) }}" required></div>
                <div class="col-12"><label class="form-label" for="artist-bio">Artist bio</label><textarea id="artist-bio" name="bio" class="form-control" rows="4" maxlength="500" required>{{ old('bio', $artist['bio']) }}</textarea></div>
            </div>
        </section>
        <section class="bg-studio-card rounded-4 p-3 p-md-4 mb-3">
            <h2 class="h5 fw-bold mb-3">Change password</h2>
            <div class="row g-3">
                <div class="col-12 col-md-4"><label class="form-label" for="artist-current-password">Current password</label><input class="form-control" id="artist-current-password" name="current_password" type="password" autocomplete="current-password"></div>
                <div class="col-12 col-md-4"><label class="form-label" for="artist-new-password">New password</label><input class="form-control" id="artist-new-password" name="new_password" type="password" minlength="8" autocomplete="new-password"></div>
                <div class="col-12 col-md-4"><label class="form-label" for="artist-password-confirmation">Confirm new password</label><input class="form-control" id="artist-password-confirmation" name="new_password_confirmation" type="password" minlength="8" autocomplete="new-password"></div>
            </div>
            <div class="form-text text-muted">Leave the password fields blank to keep your current password.</div>
        </section>
        <section class="bg-studio-card rounded-4 p-3 p-md-4 mb-3">
            <h2 class="h5 fw-bold mb-3">Notification preferences</h2>
            <div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" id="artist-review-notice" name="notifications[artwork_updates]" value="1" @checked(old('notifications.artwork_updates', $artist['notifications']['artwork_updates'] ?? true))><label class="form-check-label" for="artist-review-notice">Artwork review updates</label></div>
            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="artist-sales-notice" name="notifications[sales_activity]" value="1" @checked(old('notifications.sales_activity', $artist['notifications']['sales_activity'] ?? true))><label class="form-check-label" for="artist-sales-notice">New order and sales activity</label></div>
        </section>
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3"><span class="text-muted small">Profile changes are saved to your LikhaSpace account.</span><button type="submit" class="btn btn-likha rounded-pill px-4"><i class="bi bi-check2 me-1"></i>Save profile</button></div>
    </form>
</div>
@endsection
