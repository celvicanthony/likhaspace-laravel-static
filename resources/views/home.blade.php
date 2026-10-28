@extends('layouts.app')

@section('title', 'LikhaSpace | Art inspired by world mythology')

@push('styles')
<style>
    .home-hero { display: grid; grid-template-columns: minmax(0, 1.25fr) minmax(280px, .75fr); gap: 28px; align-items: start; margin-bottom: 34px; }
    .home-kicker { color: var(--studio-link); font-size: .72rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
    .home-title { max-width: 700px; margin: 8px 0 12px; font-size: clamp(2.2rem, 5vw, 4rem); font-weight: 700; letter-spacing: -.055em; line-height: 1.08; }
    .home-title span { color: var(--studio-primary); }
    .home-intro { max-width: 650px; color: var(--muted); font-size: 1rem; line-height: 1.7; }
    .creator-panel { padding: 18px; border: 1px solid var(--line); border-radius: 20px; background: var(--surface); box-shadow: var(--shadow); }
    .creator-panel h2 { font-size: 1.15rem; font-weight: 700; }
    .creator-row { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-top: 1px solid var(--line); color: var(--ink); text-decoration: none; }
    .creator-avatar { display: grid; width: 40px; height: 40px; flex: 0 0 40px; place-items: center; border: 1px solid var(--studio-accent); border-radius: 50%; background: var(--sidebar-bg); color: var(--studio-link); }
    .creator-row:hover .creator-arrow { color: var(--studio-primary); transform: translate(2px, -2px); }
    .creator-arrow { margin-left: auto; color: var(--studio-link); transition: transform .15s ease; }
    .collection-heading { margin-bottom: 16px; }
    .collection-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; }
    .collection-card { display: flex; min-height: 260px; flex-direction: column; overflow: hidden; padding: 18px; border: 1px solid var(--line); border-radius: 20px; background: var(--surface); box-shadow: var(--shadow); color: var(--ink); text-decoration: none; transition: transform .18s ease, box-shadow .18s ease; }
    .collection-card:hover { transform: translateY(-4px); box-shadow: 0 14px 28px rgba(35, 30, 65, .16); color: var(--ink); }
    .collection-card h3 { margin: 0 0 6px; font-size: 1.08rem; font-weight: 700; }
    .collection-card p { margin: 0; color: var(--muted); font-size: .82rem; line-height: 1.5; }
    .collection-art { display: grid; min-height: 118px; flex: 1; place-items: center; overflow: hidden; }
    .collection-glyph { display: flex; align-items: center; justify-content: center; gap: 0; transform: rotate(-8deg); }
    .collection-glyph span { display: grid; width: 70px; height: 78px; place-items: center; border: 1px solid var(--line); border-radius: 17px; background: var(--studio-primary); color: #fff; font-size: 1.8rem; box-shadow: 0 8px 18px rgba(25, 22, 55, .18); }
    .collection-glyph span + span { margin-left: -10px; transform: rotate(13deg); background: var(--surface-strong); color: var(--studio-link); }
    .collection-footer { display: flex; align-items: center; justify-content: space-between; gap: 8px; color: var(--studio-link); font-size: .78rem; font-weight: 600; }
    .collection-footer i { display: grid; width: 30px; height: 30px; place-items: center; border-radius: 50%; background: var(--studio-primary); color: #fff; }
    @media (max-width: 991.98px) { .home-hero { grid-template-columns: 1fr 1fr; gap: 18px; } .collection-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 575.98px) { .home-hero { grid-template-columns: 1fr; gap: 18px; margin-bottom: 28px; } .home-title { font-size: clamp(2.1rem, 11vw, 2.8rem); } .collection-grid { grid-template-columns: 1fr; gap: 12px; } .collection-card { min-height: 230px; } }
</style>
@endpush

@section('content')
@php
    $creators = [
        ['name' => 'Janessa Mae Ramos', 'role' => 'Digital artist'],
        ['name' => 'Celvic Anthony Borromeo', 'role' => 'Digital artist'],
        ['name' => 'Lauren Jane Isleta', 'role' => 'Digital artist'],
        ['name' => 'Vinz Jerby Acaylar', 'role' => 'Digital artist'],
    ];
    $collections = [
        ['country' => 'Philippines', 'description' => 'Meet diwata, brave heroes, and the legendary guardians of Filipino folklore.', 'icons' => ['bi-feather', 'bi-flower1']],
        ['country' => 'Greece', 'description' => 'Discover Olympian gods, ancient monsters, and timeless Greek legends.', 'icons' => ['bi-columns-gap', 'bi-stars']],
        ['country' => 'Scotland', 'description' => 'Explore kelpies, selkies, and the mysterious creatures of the Highlands.', 'icons' => ['bi-moon-stars', 'bi-water']],
        ['country' => 'Japan', 'description' => 'Encounter kitsune, yokai, and spirits woven through Japanese mythology.', 'icons' => ['bi-flower3', 'bi-brightness-high']],
    ];
@endphp
<section class="home-hero">
    <div>
        <div class="home-kicker">Art, myth and imagination</div>
        <h1 class="home-title">Find your next favorite <span>piece.</span></h1>
        <p class="home-intro">Discover original digital artworks inspired by the myths, creatures, and legendary stories of cultures around the world.</p>
        <div class="d-flex flex-wrap align-items-center gap-3 mt-4">
            <a href="{{ route('explore') }}" class="btn btn-likha btn-lg rounded-4 px-4 py-3">Discover art <i class="bi bi-arrow-right-circle-fill ms-2"></i></a>
            <div class="d-flex align-items-center gap-2 small text-muted"><span class="d-flex"><i class="bi bi-person-circle fs-4 text-primary"></i><i class="bi bi-person-circle fs-4 text-secondary ms-n1"></i><i class="bi bi-person-circle fs-4 text-primary ms-n1"></i></span><span><strong class="d-block text-body">4 digital artists</strong>Sharing their imagination</span></div>
        </div>
    </div>
    <aside class="creator-panel" aria-labelledby="creator-heading">
        <div class="home-kicker">Meet the creators</div>
        <h2 class="mb-2" id="creator-heading">Popular artists</h2>
        @foreach ($creators as $creator)
            <a class="creator-row" href="{{ route('explore') }}#artworks">
                <span class="creator-avatar"><i class="bi bi-person-fill fs-5"></i></span>
                <span><strong class="d-block small">{{ $creator['name'] }}</strong><small class="text-muted">{{ $creator['role'] }}</small></span>
                <i class="bi bi-arrow-up-right creator-arrow"></i>
            </a>
        @endforeach
    </aside>
</section>

<section id="collections" aria-labelledby="collections-heading">
    <div class="collection-heading">
        <div class="home-kicker">Explore cultures through art</div>
        <h2 class="h4 fw-bold mb-0" id="collections-heading">Featured collections</h2>
        <p class="text-muted small mb-0 mt-1">Myths and legends from four cultures, retold by independent artists.</p>
    </div>
    <div class="collection-grid">
        @foreach ($collections as $collection)
            <a class="collection-card" href="{{ route('explore', ['country' => $collection['country']]) }}">
                <h3>{{ $collection['country'] }}</h3>
                <p>{{ $collection['description'] }}</p>
                <span class="collection-art" aria-hidden="true"><span class="collection-glyph"><span><i class="bi {{ $collection['icons'][0] }}"></i></span><span><i class="bi {{ $collection['icons'][1] }}"></i></span></span></span>
                <span class="collection-footer"><span>Explore collection</span><i class="bi bi-arrow-right"></i></span>
            </a>
        @endforeach
    </div>
</section>

<section id="about" class="bg-studio-card rounded-4 shadow-sm p-4 p-md-5 mt-4">
    <div class="home-kicker">Stories worth keeping</div>
    <h2 class="h4 fw-bold mt-2">A space for Filipino creativity.</h2>
    <p class="text-muted mb-0">LikhaSpace connects collectors with independent artists through original artwork inspired by folklore and culture from around the world.</p>
</section>
@endsection
