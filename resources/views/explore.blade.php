@extends('layouts.app')

@section('title', 'Explore Artworks | LikhaSpace')

@push('styles')
<style>
    .explore-heading { margin-bottom: 24px; }
    .explore-heading h1 { margin-bottom: 4px; font-size: clamp(1.7rem, 3vw, 2.35rem); font-weight: 700; letter-spacing: -.04em; }
    .explore-heading p { margin: 0; color: var(--muted); }
    .explore-toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: 12px; margin-bottom: 22px; }
    .art-search { flex: 1 1 250px; max-width: 360px; }
    .filter-pills { display: flex; flex: 1 1 360px; flex-wrap: wrap; gap: 8px; }
    .filter-pill { padding: 7px 16px; border: 1px solid var(--line); border-radius: 999px; background: var(--surface); color: var(--muted); font-size: .82rem; font-weight: 600; }
    .filter-pill:hover, .filter-pill.active { border-color: var(--purple); background: var(--surface-strong); color: var(--purple); }
    .sort-select { width: auto; min-width: 128px; border-radius: 999px; }
    .art-card { height: 100%; overflow: hidden; border: 1px solid var(--line); border-radius: 17px; background: var(--surface); box-shadow: var(--shadow); transition: transform .2s ease, box-shadow .2s ease; }
    .art-card:hover { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(32, 23, 69, .16); }
    .art-image { position: relative; padding: 12px 12px 0; }
    .art-image img { width: 100%; height: clamp(145px, 16vw, 190px); border-radius: 13px; object-fit: cover; }
    .art-country { position: absolute; top: 21px; left: 21px; padding: 6px 10px; border-radius: 999px; background: rgba(31, 25, 54, .72); color: #fff; font-size: .7rem; }
    .favorite-button { position: absolute; top: 20px; right: 20px; display: grid; width: 34px; height: 34px; place-items: center; border: 0; border-radius: 50%; background: #fff; color: #7a718f; }
    .favorite-button.is-favorite { color: #e84b79; }
    .art-card .card-body { padding: 13px 16px 16px; }
    .art-name { margin: 0; color: var(--ink); font-size: .95rem; font-weight: 700; }
    .art-artist { margin: 3px 0 12px; color: var(--muted); font-size: .8rem; }
    .art-price { color: var(--ink); font-size: .9rem; font-weight: 700; white-space: nowrap; }
    .art-link { color: var(--purple); font-size: .8rem; font-weight: 700; text-decoration: none; white-space: nowrap; }
    .art-link:hover { color: var(--purple-hover); }
    .empty-results { display: none; padding: 48px 16px; border: 1px dashed var(--line); border-radius: 16px; color: var(--muted); text-align: center; }
    @media (max-width: 575.98px) {
        .explore-toolbar { gap: 10px; }
        .art-search { max-width: none; }
        .filter-pills { flex-basis: 100%; }
        .filter-pill { padding: 6px 12px; }
        .sort-select { margin-left: auto; }
    }
</style>
@endpush

@section('content')
<section class="explore-heading">
    <h1>Explore Artworks</h1>
    <p>Discover digital artwork from independent artists around the world.</p>
</section>

<section class="explore-toolbar" aria-label="Search and filter artworks">
    <div class="input-group art-search">
        <span class="input-group-text border-end-0 rounded-start-pill ps-3"><i class="bi bi-search"></i></span>
        <input id="artSearch" type="search" class="form-control border-start-0 rounded-end-pill" placeholder="Search artworks, artists, or styles" aria-label="Search artworks">
    </div>
    <div class="filter-pills" role="group" aria-label="Filter by location">
        <button type="button" class="filter-pill active" data-category="all">All</button>
        <button type="button" class="filter-pill" data-category="Philippines">Philippines</button>
        <button type="button" class="filter-pill" data-category="Japan">Japan</button>
        <button type="button" class="filter-pill" data-category="Greece">Greece</button>
    </div>
    <select class="form-select sort-select" aria-label="Sort artworks">
        <option>Newest</option>
        <option>Price: low to high</option>
        <option>Price: high to low</option>
    </select>
</section>

<div class="d-flex align-items-center justify-content-between mb-3">
    <h2 class="h6 fw-bold mb-0">Featured artworks</h2>
    <span id="artCount" class="small text-muted">Showing 6 artworks</span>
</div>

<section id="artworks" class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-3 g-lg-4" aria-label="Featured artworks">
    @php
        $artworks = [
            ['Cyberpunk Manila Sunset', 'Juan Kanlaon', 'Philippines', '₱1,250.00', 'photo-1618005182384-a83a8bd57fbe'],
            ['Echoes of Mount Olympus', 'AAthena Art', 'Greece', '₱2,100.00', 'photo-1568605117036-5fe5e7bab0b7'],
            ['Neo-Kyoto Cherry Blossom', 'Kaito Renders', 'Japan', '₱1,850.00', 'photo-1578632767115-351597cf2477'],
            ['Ibong Adarna Reimagined', 'Maria Makiling', 'Philippines', '₱3,500.00', 'photo-1579783902614-a3fb3927b675'],
            ['Futuristic Shinjuku Alley', 'Yuki Pixel', 'Japan', '₱1,500.00', 'photo-1607604276583-eef5d076aa5f'],
            ['Parthenon Under the Stars', 'Nikolaos 3D', 'Greece', '₱2,800.00', 'photo-1544816155-12df9643f363'],
        ];
    @endphp
    @foreach ($artworks as $index => $artwork)
        <div class="col artwork-item" data-category="{{ $artwork[2] }}" data-search="{{ strtolower(implode(' ', $artwork)) }}">
            <article class="card art-card">
                <div class="art-image">
                    <img src="https://images.unsplash.com/{{ $artwork[4] }}?q=80&w=700&auto=format&fit=crop" alt="{{ $artwork[0] }}" loading="lazy">
                    <span class="art-country">{{ $artwork[2] }}</span>
                    <button class="favorite-button" type="button" aria-label="Add {{ $artwork[0] }} to favorites" aria-pressed="false"><i class="bi bi-heart-fill"></i></button>
                </div>
                <div class="card-body">
                    <h3 class="art-name">{{ $artwork[0] }}</h3>
                    <p class="art-artist">By {{ $artwork[1] }}</p>
                    <div class="d-flex align-items-center justify-content-between border-top pt-3" style="border-color: var(--line) !important">
                        <span class="art-price">{{ $artwork[3] }}</span>
                        <a href="{{ route('product.show', $index + 1) }}" class="art-link">View details <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </article>
        </div>
    @endforeach
</section>
<div class="empty-results mt-3" id="emptyResults">No artworks match your search. Try another artist, style, or location.</div>
@endsection

@push('scripts')
<script>
    const searchField = document.getElementById('artSearch');
    const artworkItems = [...document.querySelectorAll('.artwork-item')];
    const categoryButtons = [...document.querySelectorAll('button[data-category]')];
    const emptyResults = document.getElementById('emptyResults');
    let selectedCategory = 'all';

    function filterArtworks() {
        const query = searchField.value.trim().toLowerCase();
        let visibleCount = 0;
        artworkItems.forEach((item) => {
            const matchesCategory = selectedCategory === 'all' || item.dataset.category === selectedCategory;
            const matchesSearch = item.dataset.search.includes(query);
            item.classList.toggle('d-none', !matchesCategory || !matchesSearch);
            if (matchesCategory && matchesSearch) visibleCount++;
        });
        document.getElementById('artCount').textContent = `Showing ${visibleCount} artwork${visibleCount === 1 ? '' : 's'}`;
        emptyResults.style.display = visibleCount ? 'none' : 'block';
    }

    searchField.addEventListener('input', filterArtworks);
    categoryButtons.forEach((button) => button.addEventListener('click', () => {
        selectedCategory = button.dataset.category;
        categoryButtons.forEach((filter) => filter.classList.toggle('active', filter === button));
        filterArtworks();
    }));
    document.querySelectorAll('.favorite-button').forEach((button) => button.addEventListener('click', () => {
        const isFavorite = button.getAttribute('aria-pressed') !== 'true';
        button.setAttribute('aria-pressed', String(isFavorite));
        button.classList.toggle('is-favorite', isFavorite);
        button.setAttribute('aria-label', `${isFavorite ? 'Remove' : 'Add'} ${button.closest('.art-card').querySelector('.art-name').textContent} ${isFavorite ? 'from' : 'to'} favorites`);
    }));
</script>
@endpush
