
@extends('layouts.app')

@section('title', 'Home | LikhaSpace')

@section('content')

<section class="home-top">

    <div class="hero-panel">
        <h1 class="hero-title">
            Find Your Next<br>
            Favorite <span class="accent">Piece.</span>
        </h1>

        <p>
            Discover unique digital artworks created by talented artists
            from different cultures around the world.
        </p>

        <div class="hero-actions">
            <a href="{{ route('explore') }}" class="discover-button">
                Discover Art
                <i class="bi bi-arrow-right-circle-fill"></i>
            </a>

            <div class="artist-count">
                <div class="avatar-stack">
                    <span><i class="bi bi-person-fill"></i></span>
                    <span><i class="bi bi-person-fill"></i></span>
                    <span><i class="bi bi-person-fill"></i></span>
                    <span><i class="bi bi-person-fill"></i></span>
                </div>

                <div>
                    <strong>4 Digital Artists</strong>
                    <span>Sharing their imagination</span>
                </div>
            </div>
        </div>
    </div>

    <div class="popular-panel">
        <div class="section-eyebrow">MEET THE CREATORS</div>
        <h2 class="section-heading">Popular Artists</h2>

        <div class="popular-list">

            <a href="{{ route('explore', ['artist' => 'Janessa Mae Ramos']) }}"
               class="popular-artist">
                <div class="artist-avatar">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div class="artist-info">
                    <strong>Janessa Mae Ramos</strong>
                    <span>Digital artist</span>
                </div>
                <i class="bi bi-arrow-up-right artist-arrow"></i>
            </a>

            <a href="{{ route('explore', ['artist' => 'Celvic Anthony Borromeo']) }}"
               class="popular-artist">
                <div class="artist-avatar">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div class="artist-info">
                    <strong>Celvic Anthony Borromeo</strong>
                    <span>Digital artist</span>
                </div>
                <i class="bi bi-arrow-up-right artist-arrow"></i>
            </a>

            <a href="{{ route('explore', ['artist' => 'Lauren Jane Isleta']) }}"
               class="popular-artist">
                <div class="artist-avatar">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div class="artist-info">
                    <strong>Lauren Jane Isleta</strong>
                    <span>Digital artist</span>
                </div>
                <i class="bi bi-arrow-up-right artist-arrow"></i>
            </a>

            <a href="{{ route('explore', ['artist' => 'Vinz Jerby Acaylar']) }}"
               class="popular-artist">
                <div class="artist-avatar">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div class="artist-info">
                    <strong>Vinz Jerby Acaylar</strong>
                    <span>Digital artist</span>
                </div>
                <i class="bi bi-arrow-up-right artist-arrow"></i>
            </a>

        </div>
    </div>

</section>

<section id="collections">

    <div class="collections-heading">
        <div class="section-eyebrow">EXPLORE CULTURES THROUGH ART</div>
        <h2 class="section-heading">Featured Collections</h2>
    </div>

    <div class="collection-grid">

        <article class="collection-card">
            <h3>Philippines</h3>
            <p>
                Explore the rich traditions, colorful festivals, and
                beautiful stories behind Filipino art.
            </p>
            <div class="art-placeholder">
                <div class="art-tile"><i class="bi bi-brush"></i></div>
                <div class="art-tile"><i class="bi bi-flower1"></i></div>
            </div>
            <div class="collection-bottom">
                <a href="{{ route('explore', ['category' => 'Philippines']) }}">
                    Explore collection
                </a>
                <a href="{{ route('explore', ['category' => 'Philippines']) }}"
                   class="round-arrow" aria-label="Explore Philippines">
                    <i class="bi bi-arrow-up-right"></i>
                </a>
            </div>
        </article>

        <article class="collection-card">
            <h3>Greece</h3>
            <p>
                Discover classical inspirations, ancient mythology,
                and artistic expressions from Greece.
            </p>
            <div class="art-placeholder">
                <div class="art-tile"><i class="bi bi-columns-gap"></i></div>
                <div class="art-tile"><i class="bi bi-stars"></i></div>
            </div>
            <div class="collection-bottom">
                <a href="{{ route('explore', ['category' => 'Greece']) }}">
                    Explore collection
                </a>
                <a href="{{ route('explore', ['category' => 'Greece']) }}"
                   class="round-arrow" aria-label="Explore Greece">
                    <i class="bi bi-arrow-up-right"></i>
                </a>
            </div>
        </article>

        <article class="collection-card">
            <h3>Scotland</h3>
            <p>
                Experience enchanting folklore, legendary creatures,
                and the landscapes of Scotland.
            </p>
            <div class="art-placeholder">
                <div class="art-tile"><i class="bi bi-moon-stars"></i></div>
                <div class="art-tile"><i class="bi bi-mountain"></i></div>
            </div>
            <div class="collection-bottom">
                <a href="{{ route('explore', ['category' => 'Scotland']) }}">
                    Explore collection
                </a>
                <a href="{{ route('explore', ['category' => 'Scotland']) }}"
                   class="round-arrow" aria-label="Explore Scotland">
                    <i class="bi bi-arrow-up-right"></i>
                </a>
            </div>
        </article>

        <article class="collection-card">
            <h3>Japan</h3>
            <p>
                Discover Japanese-inspired illustrations, mythical
                beings, and timeless cultural designs.
            </p>
            <div class="art-placeholder">
                <div class="art-tile"><i class="bi bi-flower2"></i></div>
                <div class="art-tile"><i class="bi bi-brightness-high"></i></div>
            </div>
            <div class="collection-bottom">
                <a href="{{ route('explore', ['category' => 'Japan']) }}">
                    Explore collection
                </a>
                <a href="{{ route('explore', ['category' => 'Japan']) }}"
                   class="round-arrow" aria-label="Explore Japan">
                    <i class="bi bi-arrow-up-right"></i>
                </a>
            </div>
        </article>

    </div>
</section>

@endsection
