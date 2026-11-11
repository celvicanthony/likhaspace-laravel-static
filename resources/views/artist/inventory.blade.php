
@extends('layouts.app')

@section('title', 'My Artwork | LikhaSpace')

@push('styles')
<style>
    .artist-inventory-panel {
        border: 1px solid var(--studio-border);
        border-radius: 20px;
        background: var(--studio-card);
        box-shadow: var(--shadow);
    }

    .artist-inventory-table {
        --bs-table-bg: transparent;
        --bs-table-color: var(--studio-text);
        --bs-table-hover-bg: var(--surface-soft);
        --bs-table-hover-color: var(--studio-text);
        margin-bottom: 0;
    }

    .artist-inventory-table th {
        background: var(--surface-soft);
        color: var(--studio-muted);
        font-size: .7rem;
        white-space: nowrap;
    }

    .artist-inventory-table td,
    .artist-inventory-table th {
        border-color: var(--studio-border);
        vertical-align: middle;
    }

    .artist-inventory-status {
        display: inline-block;
        padding: 5px 9px;
        border: 1px solid var(--studio-border);
        border-radius: 999px;
        background: var(--surface-strong);
        color: var(--studio-link);
        font-size: .72rem;
        font-weight: 700;
    }

    .artist-upload-label {
        display: block;
        padding: 8px 12px;
        border: 1px dashed var(--studio-border);
        border-radius: 12px;
        background: var(--surface-soft);
        color: var(--muted);
        font-size: .8rem;
    }

    .artwork-thumbnail {
        width: 55px;
        height: 55px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid var(--studio-border);
    }

    .artwork-description {
        max-width: 300px;
        white-space: normal;
        font-size: .8rem;
        color: var(--studio-muted);
    }
</style>
@endpush

@section('content')
<div class="mx-auto" style="max-width: 1280px">

    <header class="mb-4">
        <div class="home-kicker">Creator workspace</div>
        <h1 class="h2 fw-bold mb-1">My artwork</h1>
        <p class="text-muted mb-0">
            Manage your catalog and submit new pieces for platform review.
        </p>
    </header>

    @if (session('status'))
        <div class="alert bg-studio-card rounded-4" role="status">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger rounded-4" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- SUBMIT ARTWORK --}}
    <section class="artist-inventory-panel p-3 p-md-4 mb-4">

        <h2 class="h5 fw-bold mb-1">Submit artwork</h2>

        <p class="text-muted small mb-3">
            New work is private until the platform administrator approves it.
            PNG, JPG, and WebP files up to 5 MB are accepted.
        </p>

        <form
            method="POST"
            action="{{ route('artist.inventory.submit') }}"
            enctype="multipart/form-data"
        >
            @csrf

            <div class="row g-3">

                {{-- ARTWORK TITLE --}}
                <div class="col-12 col-md-6">
                    <label class="form-label" for="artwork-title">
                        Artwork title
                    </label>

                    <input
                        id="artwork-title"
                        name="title"
                        type="text"
                        class="form-control"
                        value="{{ old('title') }}"
                        maxlength="120"
                        required
                    >
                </div>

                {{-- COUNTRY / CULTURE --}}
                <div class="col-12 col-md-3">
                    <label class="form-label" for="artwork-country">
                        Culture
                    </label>

                    <select
                        id="artwork-country"
                        name="country"
                        class="form-select"
                        required
                    >
                        @foreach (['Philippines', 'Greece', 'Scotland', 'Japan'] as $country)
                            <option
                                value="{{ $country }}"
                                @selected(old('country', 'Philippines') === $country)
                            >
                                {{ $country }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- PRICE --}}
                <div class="col-12 col-md-3">
                    <label class="form-label" for="artwork-price">
                        Price (PHP)
                    </label>

                    <input
                        id="artwork-price"
                        name="price"
                        type="number"
                        min="1"
                        max="1000000"
                        step="0.01"
                        class="form-control"
                        value="{{ old('price') }}"
                        required
                    >
                </div>

                {{-- DESCRIPTION --}}
                <div class="col-12">
                    <label class="form-label" for="artwork-description">
                        Artwork description
                    </label>

                    <textarea
                        id="artwork-description"
                        name="description"
                        class="form-control"
                        rows="4"
                        maxlength="2000"
                        placeholder="Describe your artwork, its inspiration, meaning, or creative process..."
                        required
                    >{{ old('description') }}</textarea>

                    <small class="text-muted">
                        Tell viewers what makes your artwork special.
                    </small>
                </div>

                {{-- ARTWORK IMAGE --}}
                <div class="col-12 col-md-8">
                    <label class="artist-upload-label" for="artwork-file">

                        <span class="d-block fw-semibold text-body mb-1">
                            Artwork file
                        </span>

                        <input
                            id="artwork-file"
                            name="artwork_file"
                            type="file"
                            class="form-control"
                            accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp"
                            required
                        >

                    </label>
                </div>

                {{-- SUBMIT BUTTON --}}
                <div class="col-12 col-md-4 d-flex align-items-end">
                    <button
                        type="submit"
                        class="btn btn-likha rounded-4 w-100 py-2"
                    >
                        <i class="bi bi-cloud-arrow-up me-1"></i>
                        Submit for approval
                    </button>
                </div>

            </div>
        </form>
    </section>


    {{-- ARTIST DATABASE INVENTORY --}}
    <section class="artist-inventory-panel overflow-hidden">

        <div class="p-3 p-md-4">
            <h2 class="h5 fw-bold mb-1">Your listings</h2>

            <p class="text-muted small mb-0">
                These are artworks submitted through your artist account.
                You can edit or delete your own submissions.
            </p>
        </div>

        <div class="table-responsive">

            <table class="table table-hover artist-inventory-table align-middle">

                <thead>
                    <tr>
                        <th class="ps-3 ps-md-4">Artwork</th>
                        <th>Culture</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Manage</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($artworks as $artwork)

                        <tr>

                            {{-- ARTWORK IMAGE AND TITLE --}}
                            <td class="ps-3 ps-md-4">

                                <div class="d-flex align-items-center gap-3">

                                    @if ($artwork->image_path)
                                        <img
                                            src="{{ asset('storage/' . $artwork->image_path) }}"
                                            alt="{{ $artwork->title }}"
                                            class="artwork-thumbnail"
                                        >
                                    @else
                                        <div class="artwork-thumbnail d-flex align-items-center justify-content-center bg-light">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    @endif

                                    <div>
                                        <div class="fw-semibold">
                                            {{ $artwork->title }}
                                        </div>

                                        <div class="text-muted small">{{ $artwork->public_id }}</div>

                                        <div class="artwork-description">
                                            {{ \Illuminate\Support\Str::limit($artwork->description, 65) }}
                                        </div>
                                    </div>

                                </div>

                            </td>

                            {{-- CULTURE --}}
                            <td>
                                {{ $artwork->country }}
                            </td>

                            {{-- PRICE --}}
                            <td class="text-nowrap">
                                ₱{{ number_format((float) $artwork->price, 2) }}
                            </td>

                            {{-- APPROVAL STATUS --}}
                            <td>
                                <span class="artist-inventory-status">
                                    {{ ucfirst($artwork->status) }}
                                </span>
                            </td>

                            {{-- EDIT AND DELETE --}}
                            <td>

                                <div class="d-flex gap-2">

                                    <a
                                        href="{{ route('artist.inventory.edit', $artwork->id) }}"
                                        class="btn btn-sm btn-outline-primary rounded-pill"
                                    >
                                        <i class="bi bi-pencil-square"></i>
                                        Edit
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('artist.inventory.destroy', $artwork->id) }}"
                                        onsubmit="return confirm('Are you sure you want to delete this artwork?');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-danger rounded-pill"
                                        >
                                            <i class="bi bi-trash"></i>
                                            Delete
                                        </button>
                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="text-center py-5">

                                <i class="bi bi-images fs-2 text-muted"></i>

                                <p class="fw-semibold mt-2 mb-1">
                                    No artwork submissions yet.
                                </p>

                                <p class="text-muted small mb-0">
                                    Upload your first artwork using the form above.
                                </p>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>
    </section>

</div>
@endsection
