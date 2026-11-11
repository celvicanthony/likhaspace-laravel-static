@extends('layouts.app')

@section('title', ($profile['studio'] ?? $artist->name) . ' | LikhaSpace')

@push('styles')
<style>
    .artist-page { max-width: 1180px; margin: 0 auto; color: var(--studio-text); }
    .artist-page h1, .artist-page h2, .artist-page h3 { color: var(--studio-text); }
    .artist-page .text-muted, .artist-page .artist-bio { color: var(--studio-muted) !important; }
    .artist-page .home-kicker { color: var(--studio-link); }
    .artist-cover { display: block; width: 100%; max-height: 300px; aspect-ratio: 4 / 1; object-fit: cover; border-radius: 22px; background: var(--surface-strong); }
    .artist-intro { display: flex; align-items: flex-end; gap: 16px; margin: -42px 20px 24px; position: relative; }
    .artist-avatar { display: grid; width: 104px; height: 104px; flex: 0 0 104px; overflow: hidden; place-items: center; border: 4px solid var(--surface); border-radius: 50%; background: var(--surface-strong); color: var(--studio-link); font-size: 2rem; }
    .artist-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .artist-intro-copy { min-width: 0; flex: 1; padding: 14px 18px; border: 1px solid var(--studio-border); border-radius: 18px; background: var(--studio-card); box-shadow: var(--shadow); }
    .artist-bio { max-width: 760px; color: var(--muted); line-height: 1.7; }
    .artist-art-card { height: 100%; overflow: hidden; border: 1px solid var(--line); border-radius: 17px; background: var(--surface); color: var(--studio-text); box-shadow: var(--shadow); }
    .artist-art-image { display: grid; min-height: 190px; place-items: center; overflow: hidden; background: var(--surface-strong); color: var(--studio-link); font-size: 3rem; }
    .artist-art-image img { width: 100%; height: 190px; object-fit: cover; }
    .artist-art-body { padding: 14px; }
    .artist-art-title { color: var(--ink); font-size: .96rem; font-weight: 700; }
    @media (max-width: 575.98px) { .artist-cover { aspect-ratio: 2 / 1; } .artist-intro { align-items: flex-start; flex-direction: column; gap: 10px; margin: -32px 12px 20px; } .artist-intro-copy { width: 100%; padding: 14px; } .artist-avatar { width: 84px; height: 84px; flex-basis: 84px; } }
</style>
@endpush

@section('content')
<div class="artist-page">
    <a class="small text-decoration-none" href="{{ route('explore') }}"><i class="bi bi-arrow-left me-1"></i>Explore artwork</a>

    @if (!empty($profile['cover_image']))
        <img class="artist-cover mt-3" src="{{ asset('storage/' . ltrim($profile['cover_image'], '/')) }}" alt="">
    @else
        <div class="artist-cover mt-3" aria-hidden="true"></div>
    @endif

    <header class="artist-intro">
        <span class="artist-avatar" aria-hidden="true">
            @if (!empty($profile['avatar']))
                <img src="{{ asset('storage/' . ltrim($profile['avatar'], '/')) }}" alt="">
            @else
                <i class="bi bi-person-fill"></i>
            @endif
        </span>
        <div class="artist-intro-copy">
            <div class="home-kicker">{{ $profile['country'] ?? 'Independent artist' }}</div>
            <h1 class="h2 fw-bold mb-1">{{ $profile['studio'] ?? $artist->name }}</h1>
            @if (($profile['studio'] ?? $artist->name) !== $artist->name)
                <p class="text-muted mb-0">{{ $artist->name }}</p>
            @endif
            @if ($artistReviewCount > 0)
                <div class="d-flex align-items-center gap-2 mt-2">
                    <span class="text-warning" aria-label="Rated {{ number_format((float) $artistRating, 1) }} out of 5">
                        @for ($star = 1; $star <= 5; $star++)
                            <i class="bi {{ $star <= round((float) $artistRating) ? 'bi-star-fill' : 'bi-star' }}" aria-hidden="true"></i>
                        @endfor
                    </span>
                    <span class="small text-muted">{{ number_format((float) $artistRating, 1) }}/5 · {{ $artistReviewCount }} verified review{{ $artistReviewCount === 1 ? '' : 's' }}</span>
                </div>
            @endif
        </div>
    </header>

    @if (!empty($profile['bio']))
        <section class="bg-studio-card rounded-4 p-3 p-md-4 mb-4" aria-labelledby="artist-about-heading">
            <h2 class="h5 fw-bold" id="artist-about-heading">About the artist</h2>
            <p class="artist-bio mb-0">{{ $profile['bio'] }}</p>
            @if (!empty($profile['portfolio_links']))
                <div class="d-flex flex-wrap gap-3 mt-3">
                    @foreach ($profile['portfolio_links'] as $link)
                        <a href="{{ $link }}" target="_blank" rel="noopener noreferrer">Portfolio <i class="bi bi-box-arrow-up-right"></i></a>
                    @endforeach
                </div>
            @endif
        </section>
    @endif

    <section aria-labelledby="artist-artworks-heading">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
            <div>
                <div class="home-kicker">Collected and created</div>
                <h2 class="h4 fw-bold mb-0" id="artist-artworks-heading">Artworks</h2>
            </div>
            <span class="text-muted small">{{ $artworks->total() + $demoArtworks->count() }} available</span>
        </div>

        @if ($artworks->isEmpty() && $demoArtworks->isEmpty())
            <div class="bg-studio-card rounded-4 p-4 text-center">
                <p class="text-muted mb-0">This artist has no approved artworks yet.</p>
            </div>
        @else
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3">
                @foreach ($demoArtworks as $artwork)
                    <div class="col">
                        <article class="artist-art-card">
                            <a class="artist-art-image" href="{{ route('product.show', ['id' => $artwork['id']]) }}" aria-label="View {{ $artwork['title'] }}">
                                <img src="https://images.unsplash.com/{{ $artwork['image'] }}?q=85&w=800&auto=format&fit=crop" alt="">
                            </a>
                            <div class="artist-art-body">
                                <div class="artist-art-title">{{ $artwork['title'] }}</div>
                                <div class="d-flex justify-content-between align-items-center gap-2 mt-2">
                                    <span class="text-muted small">{{ $artwork['country'] }}</span>
                                    <span class="fw-semibold">₱{{ number_format($artwork['price'], 2) }}</span>
                                </div>
                                <div class="small text-muted mt-2">0 sold · No reviews yet</div>
                            </div>
                        </article>
                    </div>
                @endforeach

                @foreach ($artworks as $artwork)
                    <div class="col">
                        <article class="artist-art-card">
                            <a class="artist-art-image" href="{{ route('artwork.show', $artwork->id) }}" aria-label="View {{ $artwork->title }}">
                                @if ($artwork->image_path)
                                    <img src="{{ asset('storage/' . $artwork->image_path) }}" alt="">
                                @else
                                    <i class="bi bi-image" aria-hidden="true"></i>
                                @endif
                            </a>
                            <div class="artist-art-body">
                                <div class="artist-art-title">{{ $artwork->title }}</div>
                                <div class="d-flex justify-content-between align-items-center gap-2 mt-2">
                                    <span class="text-muted small">{{ $artwork->country }}</span>
                                    <span class="fw-semibold">₱{{ number_format((float) $artwork->price, 2) }}</span>
                                </div>
                                @if ($artwork->reviews_count > 0)
                                    <div class="small text-warning mt-2">
                                        @for ($star = 1; $star <= 5; $star++)
                                            <i class="bi {{ $star <= round((float) $artwork->reviews_avg_rating) ? 'bi-star-fill' : 'bi-star' }}" aria-hidden="true"></i>
                                        @endfor
                                        <span class="text-muted">{{ number_format((float) $artwork->reviews_avg_rating, 1) }} · {{ $artwork->reviews_count }} review{{ $artwork->reviews_count === 1 ? '' : 's' }}</span>
                                    </div>
                                @endif
                                <div class="small text-muted mt-1">{{ $artwork->paid_sales_count ?? 0 }} sold</div>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">{{ $artworks->links() }}</div>
        @endif
    </section>
</div>
@endsection
