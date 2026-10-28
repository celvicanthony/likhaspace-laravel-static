@extends('layouts.app')

@section('title', 'Master vault clearance | LikhaSpace')

@section('content')
<div class="mx-auto" style="max-width: 520px">
    <section class="bg-studio-card rounded-4 shadow-sm p-4 p-md-5">
        <div class="home-kicker">Second-step verification</div>
        <h1 class="h3 fw-bold mt-2">Master vault clearance</h1>
        <p class="text-muted mb-4">Verify the configured master PIN to manage platform accounts. Clearance expires after 30 minutes.</p>

        @if (session('status'))
            <div class="alert bg-studio-card rounded-4" role="status">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert bg-studio-card rounded-4" role="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.vault.verify') }}">
            @csrf
            <div class="mb-4">
                <label class="form-label" for="pin">Master PIN</label>
                <input class="form-control" id="pin" name="pin" type="password" autocomplete="off" required autofocus>
            </div>
            <button class="btn btn-likha rounded-pill w-100 py-2" type="submit">Verify clearance</button>
        </form>
    </section>
</div>
@endsection
