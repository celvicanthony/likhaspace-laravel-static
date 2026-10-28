@extends('layouts.app')

@section('title', 'Welcome to LikhaSpace')

@section('content')
<section class="bg-studio-card rounded-4 shadow-sm p-4 p-md-5 mx-auto text-center" style="max-width: 720px">
    <div class="home-kicker">Art, myth and imagination</div>
    <h1 class="h2 fw-bold mt-2">Welcome to LikhaSpace</h1>
    <p class="text-muted mb-4">Discover original artwork inspired by mythology and stories from around the world.</p>
    <a class="btn btn-likha rounded-pill px-4" href="{{ route('explore') }}">Explore artworks</a>
</section>
@endsection
