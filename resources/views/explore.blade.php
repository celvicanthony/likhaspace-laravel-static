@extends('layouts.app')

@section('content')
<style>
    .explore-page {
        min-height: calc(100vh - 5rem);
        margin: -1.5rem;
        padding: 1.5rem;
        background-color: #171521;
        color: #f8f9fa;
    }

    .bg-studio-card {
        background-color: #27243a;
    }

    .text-cyan {
        color: #6ee7f9 !important;
    }

    .btn-violet {
        background-color: #6c5ce7;
        color: #fff;
    }

    .btn-violet:hover,
    .btn-violet:focus-visible {
        background-color: #5b4cc4;
        color: #fff;
    }

    .explore-page .form-control::placeholder {
        color: #adb5bd;
    }
</style>
<div class="explore-page">
<div class="container py-4">
    <!-- Hero Banner / Title -->
    <div class="text-center mb-5">
        <h1 class="fw-bold text-white display-5 mb-2">Explore Digital Obra</h1>
        <p class="text-muted fs-6">Discover, license, and download authentic digital art from local creators.</p>
    </div>

    <!-- Search Bar & Category Filters -->
    <div class="row justify-content-center mb-5">
        <div class="col-lg-10">
            <!-- Search Bar -->
            <form action="{{ route('explore') }}" method="GET" class="mb-4">
                <div class="input-group input-group-lg shadow-sm">
                    <span class="input-group-text bg-studio-card border-secondary border-opacity-25 text-muted px-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                        </svg>
                    </span>
                    <input type="search" name="q" value="{{ request('q') }}" class="form-control bg-studio-card text-white border-secondary border-opacity-25 fs-6" placeholder="Search by artwork title, artist, or tags (e.g., Cyberpunk, Manila)..." aria-label="Search artworks">
                    <button class="btn btn-violet px-4 fw-bold" type="submit">Search</button>
                </div>
            </form>

            <!-- Category Filter Tags -->
            <div class="d-flex flex-wrap gap-2 justify-content-center align-items-center">
                <span class="text-muted me-2 small fw-semibold">Categories:</span>
                <button type="button" class="btn btn-sm btn-violet rounded-pill px-3 shadow-sm category-filter" data-category="all" aria-pressed="true">All Obra</button>
                <button type="button" class="btn btn-sm btn-outline-secondary text-white rounded-pill px-3 category-filter" data-category="philippines" aria-pressed="false">Philippines</button>
                <button type="button" class="btn btn-sm btn-outline-secondary text-white rounded-pill px-3 category-filter" data-category="greece" aria-pressed="false">Greece</button>
                <button type="button" class="btn btn-sm btn-outline-secondary text-white rounded-pill px-3 category-filter" data-category="japan" aria-pressed="false">Japan</button>
                <button type="button" class="btn btn-sm btn-outline-secondary text-white rounded-pill px-3 category-filter" data-category="3d-assets" aria-pressed="false">3D Assets</button>
                <button type="button" class="btn btn-sm btn-outline-secondary text-white rounded-pill px-3 category-filter" data-category="vector-art" aria-pressed="false">Vector Art</button>
            </div>
        </div>
    </div>

    <!-- Artwork Grid Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h5 text-white fw-bold m-0">Featured Marketplace Items</h2>
        <span class="text-muted small">Showing <span id="artwork-count">3</span> artworks</span>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
        
        <!-- Card 1 (Philippines) -->
        <div class="col artwork-card" data-category="philippines" data-search="cyberpunk manila sunset juankanlaon philippines">
            <div class="card h-100 bg-studio-card text-white border border-secondary border-opacity-25 rounded-4 shadow-sm overflow-hidden">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=600&auto=format&fit=crop" class="card-img-top" alt="Cyberpunk Manila" style="height: 240px; object-fit: cover;">
                    <span class="badge bg-dark bg-opacity-75 text-cyan border border-info border-opacity-25 position-absolute top-0 start-0 m-3 rounded-pill">Philippines</span>
                    <button type="button" class="btn btn-dark btn-sm rounded-circle position-absolute top-0 end-0 m-3 p-2 border-0 bg-opacity-75 favorite-button" data-artwork-name="Cyberpunk Manila Sunset" aria-label="Add Cyberpunk Manila Sunset to favorites" aria-pressed="false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart-fill" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"/>
                        </svg>
                    </button>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="card-title fw-bold text-white mb-1">Cyberpunk Manila Sunset</h5>
                        <p class="text-muted small mb-3">By <strong class="text-white">@JuanKanlaon</strong></p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-25">
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Price</small>
                            <span class="fs-5 fw-bold text-cyan">₱1,250.00</span>
                        </div>
                        <a href="{{ route('product.show', 1) }}" class="btn btn-violet btn-sm rounded-pill px-3 fw-semibold">View Obra</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2 (Greece) -->
        <div class="col artwork-card" data-category="greece" data-search="echoes mount olympus aathena art greece">
            <div class="card h-100 bg-studio-card text-white border border-secondary border-opacity-25 rounded-4 shadow-sm overflow-hidden">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?q=80&w=600&auto=format&fit=crop" class="card-img-top" alt="Olympus Myth" style="height: 240px; object-fit: cover;">
                    <span class="badge bg-dark bg-opacity-75 text-cyan border border-info border-opacity-25 position-absolute top-0 start-0 m-3 rounded-pill">Greece</span>
                    <button type="button" class="btn btn-dark btn-sm rounded-circle position-absolute top-0 end-0 m-3 p-2 border-0 bg-opacity-75 favorite-button" data-artwork-name="Echoes of Mount Olympus" aria-label="Add Echoes of Mount Olympus to favorites" aria-pressed="false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart-fill" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"/>
                        </svg>
                    </button>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="card-title fw-bold text-white mb-1">Echoes of Mount Olympus</h5>
                        <p class="text-muted small mb-3">By <strong class="text-white">@AAthena_Art</strong></p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-25">
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Price</small>
                            <span class="fs-5 fw-bold text-cyan">₱2,100.00</span>
                        </div>
                        <a href="{{ route('product.show', 2) }}" class="btn btn-violet btn-sm rounded-pill px-3 fw-semibold">View Obra</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3 (Japan) -->
        <div class="col artwork-card" data-category="japan" data-search="neo kyoto cherry blossom kaitorenders japan">
            <div class="card h-100 bg-studio-card text-white border border-secondary border-opacity-25 rounded-4 shadow-sm overflow-hidden">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1578632767115-351597cf2477?q=80&w=600&auto=format&fit=crop" class="card-img-top" alt="Kyoto Cherry Blossom" style="height: 240px; object-fit: cover;">
                    <span class="badge bg-dark bg-opacity-75 text-cyan border border-info border-opacity-25 position-absolute top-0 start-0 m-3 rounded-pill">Japan</span>
                    <button type="button" class="btn btn-dark btn-sm rounded-circle position-absolute top-0 end-0 m-3 p-2 border-0 bg-opacity-75 favorite-button" data-artwork-name="Neo-Kyoto Cherry Blossom" aria-label="Add Neo-Kyoto Cherry Blossom to favorites" aria-pressed="false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart-fill" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"/>
                        </svg>
                    </button>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="card-title fw-bold text-white mb-1">Neo-Kyoto Cherry Blossom</h5>
                        <p class="text-muted small mb-3">By <strong class="text-white">@KaitoRenders</strong></p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-25">
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Price</small>
                            <span class="fs-5 fw-bold text-cyan">₱1,850.00</span>
                        </div>
                        <a href="{{ route('product.show', 3) }}" class="btn btn-violet btn-sm rounded-pill px-3 fw-semibold">View Obra</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <p id="empty-results" class="text-muted text-center mt-4 d-none" role="status">No artworks match your search.</p>
</div>
</div>
<script>
    const artworkCards = document.querySelectorAll('.artwork-card');
    const searchInput = document.querySelector('input[name="q"]');
    const categoryFilters = document.querySelectorAll('.category-filter');
    const artworkCount = document.getElementById('artwork-count');
    const emptyResults = document.getElementById('empty-results');
    let activeCategory = 'all';

    function filterArtworks() {
        const query = searchInput.value.trim().toLowerCase();
        let visibleCount = 0;

        artworkCards.forEach((card) => {
            const matchesCategory = activeCategory === 'all' || card.dataset.category === activeCategory;
            const matchesSearch = card.dataset.search.includes(query);
            const isVisible = matchesCategory && matchesSearch;

            card.classList.toggle('d-none', !isVisible);
            visibleCount += isVisible ? 1 : 0;
        });

        artworkCount.textContent = visibleCount;
        emptyResults.classList.toggle('d-none', visibleCount > 0);
    }

    categoryFilters.forEach((button) => {
        button.addEventListener('click', () => {
            activeCategory = button.dataset.category;

            categoryFilters.forEach((filter) => {
                const isActive = filter === button;

                filter.classList.toggle('btn-violet', isActive);
                filter.classList.toggle('btn-outline-secondary', !isActive);
                filter.setAttribute('aria-pressed', isActive);
            });

            filterArtworks();
        });
    });

    searchInput.addEventListener('input', filterArtworks);

    document.querySelectorAll('.favorite-button').forEach((button) => {
        button.addEventListener('click', () => {
            const isFavorite = button.getAttribute('aria-pressed') !== 'true';

            button.setAttribute('aria-pressed', isFavorite);
            button.classList.toggle('text-danger', isFavorite);
            button.setAttribute('aria-label', `${isFavorite ? 'Remove' : 'Add'} ${button.dataset.artworkName} ${isFavorite ? 'from' : 'to'} favorites`);
        });
    });

    filterArtworks();
</script>
@endsection
