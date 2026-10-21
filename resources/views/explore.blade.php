@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Hero Banner / Storefront Title -->
    <div class="text-center mb-5">
        <h1 class="fw-bold text-white display-5 mb-2">Explore Digital Obra</h1>
        <p class="text-muted fs-6">Discover, license, and download authentic digital art from local creators.</p>
    </div>

    <!-- Search Bar & Category Filter Section -->
    <div class="row justify-content-center mb-5">
        <div class="col-lg-10">
            <!-- Top Search Bar -->
            <form action="#" method="GET" class="mb-4" onsubmit="event.preventDefault(); alert('[SIMULATED ACTION] Search submitted!');">
                <div class="input-group input-group-lg shadow-sm">
                    <span class="input-group-text bg-studio-card border-secondary border-opacity-25 text-muted px-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                        </svg>
                    </span>
                    <input type="text" class="form-control bg-studio-card text-white border-secondary border-opacity-25 fs-6" placeholder="Search by artwork title, artist, or tags (e.g., Cyberpunk, Manila)...">
                    <button class="btn btn-violet px-4 fw-bold" type="submit">Search</button>
                </div>
            </form>

            <!-- Category Filter Tags -->
            <div class="d-flex flex-wrap gap-2 justify-content-center align-items-center">
                <span class="text-muted me-2 small fw-semibold">Categories:</span>
                <button type="button" class="btn btn-sm btn-violet rounded-pill px-3 shadow-sm">All Obra</button>
                <button type="button" class="btn btn-sm btn-outline-secondary text-white rounded-pill px-3">Philippines</button>
                <button type="button" class="btn btn-sm btn-outline-secondary text-white rounded-pill px-3">Greece</button>
                <button type="button" class="btn btn-sm btn-outline-secondary text-white rounded-pill px-3">Japan</button>
                <button type="button" class="btn btn-sm btn-outline-secondary text-white rounded-pill px-3">3D Assets</button>
                <button type="button" class="btn btn-sm btn-outline-secondary text-white rounded-pill px-3">Vector Art</button>
            </div>
        </div>
    </div>

    <!-- Artwork Grid Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h5 text-white fw-bold m-0">Featured Marketplace Items</h2>
        <span class="text-muted small">Showing 6 artworks</span>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">

        <!-- Artwork Card 1 (Philippines) -->
        <div class="col">
            <div class="card h-100 bg-studio-card text-white border border-secondary border-opacity-25 rounded-4 shadow-sm overflow-hidden">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=600&auto=format&fit=crop" class="card-img-top" alt="Cyberpunk Manila" style="height: 240px; object-fit: cover;">
                    <span class="badge bg-dark bg-opacity-75 text-cyan border border-info border-opacity-25 position-absolute top-0 start-0 m-3 rounded-pill">Philippines</span>
                    <!-- Heart Icon (Interactive Favorite Button) -->
                    <button class="btn btn-dark btn-sm rounded-circle position-absolute top-0 end-0 m-3 p-2 border-0 bg-opacity-75" onclick="this.classList.toggle('text-danger'); alert('[SIMULATED ACTION] Added to Favorites!');" title="Favorite">
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
                        <a href="{{ route('client.show', 1) }}" class="btn btn-violet btn-sm rounded-pill px-3 fw-semibold">View Obra</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Artwork Card 2 (Greece) -->
        <div class="col">
            <div class="card h-100 bg-studio-card text-white border border-secondary border-opacity-25 rounded-4 shadow-sm overflow-hidden">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?q=80&w=600&auto=format&fit=crop" class="card-img-top" alt="Olympus Myth" style="height: 240px; object-fit: cover;">
                    <span class="badge bg-dark bg-opacity-75 text-cyan border border-info border-opacity-25 position-absolute top-0 start-0 m-3 rounded-pill">Greece</span>
                    <button class="btn btn-dark btn-sm rounded-circle position-absolute top-0 end-0 m-3 p-2 border-0 bg-opacity-75" onclick="this.classList.toggle('text-danger'); alert('[SIMULATED ACTION] Added to Favorites!');" title="Favorite">
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
                        <a href="{{ route('client.show', 2) }}" class="btn btn-violet btn-sm rounded-pill px-3 fw-semibold">View Obra</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Artwork Card 3 (Japan) -->
        <div class="col">
            <div class="card h-100 bg-studio-card text-white border border-secondary border-opacity-25 rounded-4 shadow-sm overflow-hidden">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1578632767115-351597cf2477?q=80&w=600&auto=format&fit=crop" class="card-img-top" alt="Kyoto Cherry Blossom" style="height: 240px; object-fit: cover;">
                    <span class="badge bg-dark bg-opacity-75 text-cyan border border-info border-opacity-25 position-absolute top-0 start-0 m-3 rounded-pill">Japan</span>
                    <button class="btn btn-dark btn-sm rounded-circle position-absolute top-0 end-0 m-3 p-2 border-0 bg-opacity-75" onclick="this.classList.toggle('text-danger'); alert('[SIMULATED ACTION] Added to Favorites!');" title="Favorite">
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
                        <a href="{{ route('client.show', 3) }}" class="btn btn-violet btn-sm rounded-pill px-3 fw-semibold">View Obra</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Artwork Card 4 (Philippines) -->
        <div class="col">
            <div class="card h-100 bg-studio-card text-white border border-secondary border-opacity-25 rounded-4 shadow-sm overflow-hidden">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1579783902614-a3fb3927b675?q=80&w=600&auto=format&fit=crop" class="card-img-top" alt="Ibong Adarna Reimagined" style="height: 240px; object-fit: cover;">
                    <span class="badge bg-dark bg-opacity-75 text-cyan border border-info border-opacity-25 position-absolute top-0 start-0 m-3 rounded-pill">Philippines</span>
                    <button class="btn btn-dark btn-sm rounded-circle position-absolute top-0 end-0 m-3 p-2 border-0 bg-opacity-75" onclick="this.classList.toggle('text-danger'); alert('[SIMULATED ACTION] Added to Favorites!');" title="Favorite">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart-fill" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"/>
                        </svg>
                    </button>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="card-title fw-bold text-white mb-1">Ibong Adarna Reimagined</h5>
                        <p class="text-muted small mb-3">By <strong class="text-white">@MariaMakiling_Art</strong></p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-25">
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Price</small>
                            <span class="fs-5 fw-bold text-cyan">₱3,500.00</span>
                        </div>
                        <a href="{{ route('client.show', 4) }}" class="btn btn-violet btn-sm rounded-pill px-3 fw-semibold">View Obra</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Artwork Card 5 (Japan) -->
        <div class="col">
            <div class="card h-100 bg-studio-card text-white border border-secondary border-opacity-25 rounded-4 shadow-sm overflow-hidden">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?q=80&w=600&auto=format&fit=crop" class="card-img-top" alt="Futuristic Shinjuku" style="height: 240px; object-fit: cover;">
                    <span class="badge bg-dark bg-opacity-75 text-cyan border border-info border-opacity-25 position-absolute top-0 start-0 m-3 rounded-pill">Japan</span>
                    <button class="btn btn-dark btn-sm rounded-circle position-absolute top-0 end-0 m-3 p-2 border-0 bg-opacity-75" onclick="this.classList.toggle('text-danger'); alert('[SIMULATED ACTION] Added to Favorites!');" title="Favorite">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart-fill" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"/>
                        </svg>
                    </button>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="card-title fw-bold text-white mb-1">Futuristic Shinjuku Alley</h5>
                        <p class="text-muted small mb-3">By <strong class="text-white">@YukiPixel</strong></p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-25">
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Price</small>
                            <span class="fs-5 fw-bold text-cyan">₱1,500.00</span>
                        </div>
                        <a href="{{ route('client.show', 5) }}" class="btn btn-violet btn-sm rounded-pill px-3 fw-semibold">View Obra</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Artwork Card 6 (Greece) -->
        <div class="col">
            <div class="card h-100 bg-studio-card text-white border border-secondary border-opacity-25 rounded-4 shadow-sm overflow-hidden">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1544816155-12df9643f363?q=80&w=600&auto=format&fit=crop" class="card-img-top" alt="Parthenon Night 3D" style="height: 240px; object-fit: cover;">
                    <span class="badge bg-dark bg-opacity-75 text-cyan border border-info border-opacity-25 position-absolute top-0 start-0 m-3 rounded-pill">Greece</span>
                    <button class="btn btn-dark btn-sm rounded-circle position-absolute top-0 end-0 m-3 p-2 border-0 bg-opacity-75" onclick="this.classList.toggle('text-danger'); alert('[SIMULATED ACTION] Added to Favorites!');" title="Favorite">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart-fill" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"/>
                        </svg>
                    </button>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="card-title fw-bold text-white mb-1">Parthenon Under the Stars</h5>
                        <p class="text-muted small mb-3">By <strong class="text-white">@Nikolaos3D</strong></p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-25">
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Price</small>
                            <span class="fs-5 fw-bold text-cyan">₱2,800.00</span>
                        </div>
                        <a href="{{ route('client.show', 6) }}" class="btn btn-violet btn-sm rounded-pill px-3 fw-semibold">View Obra</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection