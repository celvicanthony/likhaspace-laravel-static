@extends('layouts.app')

@section('title', 'My Artwork | LikhaSpace')

@push('styles')
<style>
    .artist-inventory-panel { border: 1px solid var(--studio-border); border-radius: 20px; background: var(--studio-card); box-shadow: var(--shadow); }
    .artist-inventory-table { --bs-table-bg: transparent; --bs-table-color: var(--studio-text); --bs-table-hover-bg: var(--surface-soft); --bs-table-hover-color: var(--studio-text); margin-bottom: 0; }
    .artist-inventory-table th { background: var(--surface-soft); color: var(--studio-muted); font-size: .7rem; white-space: nowrap; }
    .artist-inventory-table td, .artist-inventory-table th { border-color: var(--studio-border); vertical-align: middle; }
    .artist-inventory-status { display: inline-block; padding: 5px 9px; border: 1px solid var(--studio-border); border-radius: 999px; background: var(--surface-strong); color: var(--studio-link); font-size: .72rem; font-weight: 700; }
    .artist-upload-label { display: block; padding: 8px 12px; border: 1px dashed var(--studio-border); border-radius: 12px; background: var(--surface-soft); color: var(--muted); font-size: .8rem; }
</style>
@endpush

@section('content')
<div class="mx-auto" style="max-width: 1280px">
    <header class="mb-4">
        <div class="home-kicker">Creator workspace</div>
        <h1 class="h2 fw-bold mb-1">My artwork</h1>
        <p class="text-muted mb-0">Manage your catalog and submit new pieces for platform review.</p>
    </header>

    @if (session('status'))<div class="alert bg-studio-card rounded-4" role="status">{{ session('status') }}</div>@endif
    @if ($errors->any())<div class="alert bg-studio-card rounded-4" role="alert">{{ $errors->first() }}</div>@endif

    <section class="artist-inventory-panel p-3 p-md-4 mb-4">
        <h2 class="h5 fw-bold mb-1">Submit artwork</h2>
        <p class="text-muted small mb-3">New work is private until the platform administrator approves it. PNG, JPG, and WebP files up to 5 MB are accepted.</p>
        <form method="POST" action="{{ route('artist.inventory.submit') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-12 col-md-6"><label class="form-label" for="artwork-title">Artwork title</label><input id="artwork-title" name="title" class="form-control" value="{{ old('title') }}" maxlength="120" required></div>
                <div class="col-12 col-md-3"><label class="form-label" for="artwork-country">Culture</label><select id="artwork-country" name="country" class="form-select" required>@foreach (['Philippines', 'Greece', 'Scotland', 'Japan'] as $country)<option value="{{ $country }}" @selected(old('country') === $country)>{{ $country }}</option>@endforeach</select></div>
                <div class="col-12 col-md-3"><label class="form-label" for="artwork-price">Price (PHP)</label><input id="artwork-price" name="price" type="number" min="1" max="1000000" step="0.01" class="form-control" value="{{ old('price') }}" required></div>
                <div class="col-12 col-md-8"><label class="artist-upload-label" for="artwork-file"><span class="d-block fw-semibold text-body mb-1">Artwork file</span><input id="artwork-file" name="artwork_file" type="file" class="form-control" accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp" required></label></div>
                <div class="col-12 col-md-4 d-flex align-items-end"><button type="submit" class="btn btn-likha rounded-4 w-100 py-2"><i class="bi bi-cloud-arrow-up me-1"></i>Submit for approval</button></div>
            </div>
        </form>
    </section>

    <section class="artist-inventory-panel overflow-hidden">
        <div class="p-3 p-md-4"><h2 class="h5 fw-bold mb-1">Your listings</h2><p class="text-muted small mb-0">Inventory is scoped to your artist workspace. Status changes are managed by the platform admin.</p></div>
        <div class="table-responsive">
            <table class="table table-hover artist-inventory-table align-middle">
                <thead><tr><th class="ps-3 ps-md-4">Artwork</th><th>Culture</th><th>Price</th><th>Status</th><th>Manage</th></tr></thead>
                <tbody>
                    @foreach ($artworks as $artwork)
                        <tr><td class="ps-3 ps-md-4 fw-semibold">{{ $artwork['title'] }}</td><td>{{ $artwork['country'] }}</td><td class="text-nowrap">₱{{ number_format($artwork['price'], 2) }}</td><td><span class="artist-inventory-status">{{ ucfirst($artwork['status']) }}</span></td><td><span class="small text-muted">Catalog managed by platform</span></td></tr>
                    @endforeach
                    @foreach ($submissions as $submission)
                        <tr><td class="ps-3 ps-md-4 fw-semibold">{{ $submission['title'] }}<small class="d-block text-muted">{{ $submission['file_name'] }}</small></td><td>{{ $submission['country'] }}</td><td class="text-nowrap">₱{{ number_format($submission['price'], 2) }}</td><td><span class="artist-inventory-status">{{ ucfirst($submission['status']) }}</span></td><td><form method="POST" action="{{ route('artist.inventory.remove', $submission['id']) }}" data-withdraw-form>@csrf<button type="submit" class="btn btn-sm btn-outline-likha rounded-pill">Withdraw</button></form></td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('[data-withdraw-form]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm('Withdraw this artwork submission?')) event.preventDefault();
        });
    });
</script>
@endpush
