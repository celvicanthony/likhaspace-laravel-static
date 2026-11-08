@extends('layouts.app')

@section('title', 'Edit Artwork | LikhaSpace')

@section('content')
<div class="mx-auto" style="max-width: 960px">
    <header class="mb-4">
        <div class="home-kicker">Creator workspace</div>
        <h1 class="h2 fw-bold mb-1">Edit artwork</h1>
        <p class="text-muted mb-0">Save your changes to send this artwork back for admin review.</p>
    </header>

    @if ($errors->any())
        <div class="alert alert-danger rounded-4" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="bg-studio-card rounded-4 p-3 p-md-4">
        <form method="POST" action="{{ route('artist.inventory.update', $artwork) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label" for="artwork-title">Artwork title</label>
                    <input id="artwork-title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $artwork->title) }}" maxlength="120" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label" for="artwork-country">Culture</label>
                    <select id="artwork-country" name="country" class="form-select @error('country') is-invalid @enderror" required>
                        @foreach (['Philippines', 'Greece', 'Scotland', 'Japan'] as $country)
                            <option value="{{ $country }}" @selected(old('country', $artwork->country) === $country)>{{ $country }}</option>
                        @endforeach
                    </select>
                    @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label" for="artwork-price">Price (PHP)</label>
                    <input id="artwork-price" name="price" type="number" min="1" max="1000000" step="0.01" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $artwork->price) }}" required>
                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label" for="artwork-description">Description</label>
                    <textarea id="artwork-description" name="description" rows="4" maxlength="2000" class="form-control @error('description') is-invalid @enderror">{{ old('description', $artwork->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-6">
                    @if ($artwork->image_path)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . ltrim($artwork->image_path, '/')) }}" alt="Current artwork image" class="rounded-3" style="max-width: 100%; max-height: 220px; object-fit: contain">
                        </div>
                    @endif
                    <label class="form-label" for="artwork-file">Replace artwork image (optional)</label>
                    <input id="artwork-file" name="artwork_file" type="file" accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp" class="form-control @error('artwork_file') is-invalid @enderror">
                    @error('artwork_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text text-muted">PNG, JPG, or WebP up to 5 MB. Leave blank to keep the current image.</div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-4">
                <button class="btn btn-likha rounded-pill px-4" type="submit">Save and resubmit for review</button>
                <a class="btn btn-outline-secondary rounded-pill px-4" href="{{ route('artist.inventory') }}">Cancel</a>
            </div>
        </form>
    </section>
</div>
@endsection