
@extends('layouts.app')

@section('title', 'LikhaSpace | My Favorites')

@push('styles')
<style>
    .favorites-page {
        color: #1d1b68;
        font-family: 'Poppins', sans-serif;
    }

    .favorites-heading {
        font-size: 1.45rem;
        font-weight: 800;
        margin-bottom: 4px;
    }

    .favorites-subtitle {
        color: #77739c;
        font-size: .75rem;
        margin-bottom: 22px;
    }

    .favorites-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .favorite-card {
        min-width: 0;
        padding: 10px;
        border: 1px solid #efedf4;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 3px 12px rgba(0, 0, 0, .15);
    }

    .favorite-image {
        height: 140px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 13px;
        background: #aa94ef;
        overflow: hidden;
    }

    .favorite-image > a {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-decoration: none;
    }

    .favorite-image > a:hover {
        background: rgba(29, 27, 104, .08);
    }

    .favorite-image .bi-image {
        font-size: 2.6rem;
        opacity: .55;
    }

    .favorite-remove {
        position: absolute;
        z-index: 2;
        top: 7px;
        right: 7px;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 0;
        border-radius: 50%;
        background: #fff;
        color: #ed1237;
        cursor: pointer;
    }

    .favorite-info {
        display: flex;
        justify-content: space-between;
        gap: 8px;
        padding: 9px 4px 3px;
        font-size: .68rem;
        line-height: 1.5;
    }

    .favorite-name {
        display: block;
        font-weight: 800;
    }

    .favorite-artist {
        display: block;
        color: #77739c;
    }

    .favorite-price {
        white-space: nowrap;
        font-weight: 700;
        text-align: right;
    }

    .favorite-details {
        display: inline-block;
        margin: 6px 4px 3px;
        color: #7057e8;
        font-size: .7rem;
        font-weight: 700;
        text-decoration: none;
    }

    .favorite-details:hover {
        text-decoration: underline;
    }

    .favorites-empty {
        display: none;
        padding: 45px 20px;
        text-align: center;
        border: 1px dashed #d9cff8;
        border-radius: 16px;
        background: #faf8ff;
    }

    .favorites-empty i {
        display: block;
        color: #7057e8;
        font-size: 2.6rem;
        margin-bottom: 10px;
    }

    .favorites-empty h2 {
        font-size: 1.05rem;
        font-weight: 800;
    }

    .favorites-empty p {
        color: #77739c;
        font-size: .8rem;
    }

    .explore-link {
        display: inline-block;
        padding: 10px 17px;
        border-radius: 22px;
        background: #7057e8;
        color: #fff;
        text-decoration: none;
        font-size: .75rem;
        font-weight: 700;
    }

    .explore-link:hover {
        background: #5941d0;
        color: #fff;
    }

    @media (max-width: 1000px) {
        .favorites-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 560px) {
        .favorites-grid {
            grid-template-columns: 1fr;
        }

        .favorite-image {
            height: 190px;
        }
    }
</style>
@endpush

@section('content')
<div class="favorites-page">
    <h1 class="favorites-heading">My Favorites <i class="bi bi-heart-fill" style="color:#ed1237"></i></h1>
    <p class="favorites-subtitle">
        Your saved artworks, all in one place.
    </p>

    @php
        $artworks = [
            ['id' => 1, 'name' => 'Tikbalang in the Mist', 'artist' => 'Janessa Mae Ramos', 'price' => 300, 'category' => 'Philippines'],
            ['id' => 2, 'name' => 'Moonlit Kitsune', 'artist' => 'Vinz Jerby Acaylar', 'price' => 350, 'category' => 'Japan'],
            ['id' => 3, 'name' => 'Olympian Dreams', 'artist' => 'Celvic Anthony Borromeo', 'price' => 400, 'category' => 'Greece'],
            ['id' => 4, 'name' => 'Guardian of the Highlands', 'artist' => 'Vinz Jerby Acaylar', 'price' => 320, 'category' => 'Scotland'],
            ['id' => 5, 'name' => 'Sakura Reverie', 'artist' => 'Lauren Jane Isleta', 'price' => 280, 'category' => 'Japan'],
            ['id' => 6, 'name' => 'Enchanted Philippine Forest', 'artist' => 'Celvic Anthony Borromeo', 'price' => 300, 'category' => 'Philippines'],
            ['id' => 7, 'name' => 'Highland Moon', 'artist' => 'Janessa Mae Ramos', 'price' => 330, 'category' => 'Scotland'],
            ['id' => 8, 'name' => 'Athena’s Wisdom', 'artist' => 'Lauren Jane Isleta', 'price' => 380, 'category' => 'Greece'],
        ];
    @endphp

    <div class="favorites-grid" id="favoritesGrid">
        @foreach ($artworks as $art)
            <article class="favorite-card" data-id="{{ $art['id'] }}" style="display:none">
                <div class="favorite-image">
                    <a href="{{ route('product.show', ['id' => $art['id']]) }}"
                       aria-label="View {{ $art['name'] }} details">
                        <i class="bi bi-image"></i>
                    </a>

                    <button type="button"
                            class="favorite-remove"
                            data-id="{{ $art['id'] }}"
                            aria-label="Remove {{ $art['name'] }} from favorites"
                            title="Remove from favorites">
                        <i class="bi bi-heart-fill"></i>
                    </button>
                </div>

                <div class="favorite-info">
                    <div>
                        <span class="favorite-name">{{ $art['name'] }}</span>
                        <span class="favorite-artist">By: {{ $art['artist'] }}</span>
                        <span class="favorite-artist">{{ $art['category'] }}</span>
                    </div>

                    <span class="favorite-price">
                        Php {{ number_format($art['price'], 2) }}
                    </span>
                </div>

                <a class="favorite-details"
                   href="{{ route('product.show', ['id' => $art['id']]) }}">
                    View details <i class="bi bi-arrow-right"></i>
                </a>
            </article>
        @endforeach
    </div>

    <div class="favorites-empty" id="favoritesEmpty">
        <i class="bi bi-heart"></i>
        <h2>No favorites yet</h2>
        <p>Explore the artwork collection and tap a heart to save an artwork here.</p>
        <a href="{{ route('explore') }}" class="explore-link">
            <i class="bi bi-image"></i> Explore Artworks
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const favoritesKey = 'likhaspace_favorites';
    const grid = document.getElementById('favoritesGrid');
    const emptyMessage = document.getElementById('favoritesEmpty');
    const cards = Array.from(grid.querySelectorAll('.favorite-card'));

    function getFavorites() {
        try {
            const saved = JSON.parse(localStorage.getItem(favoritesKey) || '[]');
            return Array.isArray(saved) ? saved.map(String) : [];
        } catch (error) {
            return [];
        }
    }

    function renderFavorites() {
        const favorites = getFavorites();

        cards.forEach(function (card) {
            card.style.display = favorites.includes(String(card.dataset.id))
                ? ''
                : 'none';
        });

        const hasFavorites = cards.some(function (card) {
            return card.style.display !== 'none';
        });

        emptyMessage.style.display = hasFavorites ? 'none' : 'block';
        grid.style.display = hasFavorites ? 'grid' : 'none';
    }

    grid.querySelectorAll('.favorite-remove').forEach(function (button) {
        button.addEventListener('click', function () {
            const artworkId = String(button.dataset.id);
            const favorites = getFavorites().filter(function (id) {
                return id !== artworkId;
            });

            localStorage.setItem(favoritesKey, JSON.stringify(favorites));
            renderFavorites();
        });
    });

    renderFavorites();

    // Refresh the list when returning to this page in the same tab.
    window.addEventListener('pageshow', renderFavorites);
});
</script>
@endpush
