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
                <div class="col-12 col-md-6">
                    <label class="form-label" for="artist-avatar">Profile photo</label>
                    @if (!empty($artist['avatar']))
                        <div class="mb-2"><img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($artist['avatar']) }}" alt="Current artist profile photo" class="rounded-circle" style="width: 84px; height: 84px; object-fit: cover"></div>
                    @endif
                    <input id="artist-avatar" name="avatar" type="file" class="form-control @error('avatar') is-invalid @enderror" accept="image/png,image/jpeg,image/webp">
                    @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text text-muted">PNG, JPG, or WebP up to 5 MB.</div>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="artist-cover">Cover image</label>
                    @if (!empty($artist['cover_image']))
                        <div class="mb-2"><img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($artist['cover_image']) }}" alt="Current studio cover image" class="rounded-3" style="width: 100%; max-height: 120px; object-fit: cover"></div>
                    @endif
                    <input id="artist-cover" name="cover_image" type="file" class="form-control @error('cover_image') is-invalid @enderror" accept="image/png,image/jpeg,image/webp">
                    @error('cover_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text text-muted">PNG, JPG, or WebP up to 8 MB.</div>
                </div>
                <div class="col-12 col-md-6"><label class="form-label" for="artist-name">Artist or studio name</label><input id="artist-name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $artist['name']) }}" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-12 col-md-6"><label class="form-label" for="artist-email">Contact email</label><input id="artist-email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $artist['email']) }}" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-12 col-md-6"><label class="form-label" for="artist-contact">Contact number</label><input id="artist-contact" name="contact" type="tel" class="form-control @error('contact') is-invalid @enderror" value="{{ old('contact', $artist['contact'] ?? '') }}" autocomplete="tel">@error('contact')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-12 col-md-6"><label class="form-label" for="artist-country">Primary culture</label><select id="artist-country" name="country" class="form-select">@foreach (['Philippines', 'Greece', 'Scotland', 'Japan'] as $country)<option value="{{ $country }}" @selected(old('country', $artist['country']) === $country)>{{ $country }}</option>@endforeach</select></div>
                <div class="col-12 col-md-6"><label class="form-label" for="artist-studio">Studio name</label><input id="artist-studio" name="studio" class="form-control" value="{{ old('studio', $artist['studio']) }}" required></div>
                <div class="col-12"><label class="form-label" for="artist-bio">Artist bio</label><textarea id="artist-bio" name="bio" class="form-control" rows="4" maxlength="500" required>{{ old('bio', $artist['bio']) }}</textarea></div>
                <div class="col-12">
                    <label class="form-label" for="portfolio-link-0">Portfolio links</label>
                    <div id="portfolio-links" class="d-grid gap-2">
                        @foreach (old('portfolio_links', $artist['portfolio_links'] ?? ['']) as $index => $link)
                            <input id="portfolio-link-{{ $index }}" name="portfolio_links[]" type="url" class="form-control @error('portfolio_links.'.$index) is-invalid @enderror" value="{{ $link }}" placeholder="https://your-portfolio.example">
                            @error('portfolio_links.'.$index)<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @endforeach
                    </div>
                    <button class="btn btn-outline-likha btn-sm rounded-pill mt-2" id="add-portfolio-link" type="button">Add another link</button>
                    @error('portfolio_links')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
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

@push('scripts')
<script>
    document.getElementById('add-portfolio-link')?.addEventListener('click', () => {
        const container = document.getElementById('portfolio-links');
        if (container.querySelectorAll('input[name="portfolio_links[]"]').length >= 10) return;

        const input = document.createElement('input');
        input.type = 'url';
        input.name = 'portfolio_links[]';
        input.className = 'form-control';
        input.placeholder = 'https://your-portfolio.example';
        input.setAttribute('aria-label', 'Portfolio URL');
        container.append(input);
    });
</script>
@endpush
