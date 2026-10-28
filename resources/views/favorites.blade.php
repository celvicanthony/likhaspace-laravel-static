@extends('layouts.app')

@section('title', 'My Favorites | LikhaSpace')

@push('styles')
<style>
    .favorite-card { height: 100%; overflow: hidden; border: 1px solid var(--studio-border); border-radius: 18px; background: var(--studio-card); box-shadow: var(--shadow); }
    .favorite-image { width: 100%; height: 190px; object-fit: cover; }
    .favorite-meta { color: var(--muted); font-size: .82rem; }
</style>
@endpush

@section('content')
<div class="mx-auto" style="max-width: 1200px">
    <header class="mb-4">
        <div class="home-kicker">Your saved collection</div>
        <h1 class="h2 fw-bold mb-1">My favorites</h1>
        <p class="text-muted mb-0">Keep track of artwork you may want to collect.</p>
    </header>

    @if (session('status'))
        <div class="alert bg-studio-card rounded-4" role="status">{{ session('status') }}</div>
    @endif

    @if (count($artworks))
        <section class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-3" aria-label="Favorite artworks">
            @foreach ($artworks as $id => $artwork)
                <article class="col">
                    <div class="favorite-card">
                        <img class="favorite-image" src="https://images.unsplash.com/{{ $artwork['image'] }}?q=80&w=800&auto=format&fit=crop" alt="{{ $artwork['title'] }}" loading="lazy">
                        <div class="p-3">
                            <span class="home-kicker">{{ $artwork['country'] }}</span>
                            <h2 class="h6 fw-bold mt-2 mb-1">{{ $artwork['title'] }}</h2>
                            <p class="favorite-meta mb-3">By {{ $artwork['artist'] }}</p>
                            <div class="d-flex align-items-center justify-content-between gap-2">
                                <span class="fw-bold">₱{{ number_format($artwork['price'], 2) }}</span>
                                <a href="{{ route('product.show', $id) }}" class="btn btn-sm btn-outline-likha rounded-pill">View artwork</a>
                            </div>
                            <form method="POST" action="{{ route('favorites.toggle', $id) }}" class="mt-3">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-likha rounded-pill w-100"><i class="bi bi-heartbreak me-1"></i>Remove from favorites</button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </section>
    @else
        <section class="bg-studio-card rounded-4 p-4 p-md-5 text-center">
            <i class="bi bi-heart display-5 text-primary"></i>
            <h2 class="h5 fw-bold mt-3">No favorites yet</h2>
            <p class="text-muted">Browse the catalog and save artworks here for later.</p>
            <a href="{{ route('explore') }}" class="btn btn-likha rounded-4">Explore artwork</a>
        </section>
    @endif
</div>
@endsection
