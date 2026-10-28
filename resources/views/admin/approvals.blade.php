@extends('layouts.app')

@section('title', 'Artwork Approvals | LikhaSpace')

@push('styles')
<style>
    .approval-panel { overflow: hidden; border: 1px solid var(--studio-border); border-radius: 20px; background: var(--studio-card); box-shadow: var(--shadow); }
    .approval-table { --bs-table-bg: transparent; --bs-table-color: var(--studio-text); --bs-table-hover-bg: var(--surface-soft); --bs-table-hover-color: var(--studio-text); margin: 0; }
    .approval-table th { background: var(--surface-soft); color: var(--studio-muted); font-size: .72rem; white-space: nowrap; }
    .approval-table td, .approval-table th { border-color: var(--studio-border); vertical-align: middle; }
    .approval-status { padding: 5px 9px; border: 1px solid var(--studio-border); border-radius: 999px; background: var(--surface-strong); color: var(--studio-link); font-size: .72rem; font-weight: 700; }
    .approval-actions { min-width: 170px; }
</style>
@endpush

@section('content')
<div class="mx-auto" style="max-width: 1480px">
    <header class="mb-4">
        <div class="home-kicker">Marketplace quality</div>
        <h1 class="h2 fw-bold mb-1">Artwork approvals</h1>
        <p class="text-muted mb-0">Review submissions from independent artists before they appear in the public catalog.</p>
    </header>

    @if (session('status'))<div class="alert bg-studio-card rounded-4" role="status">{{ session('status') }}</div>@endif
    @if ($errors->any())<div class="alert bg-studio-card rounded-4" role="alert">{{ $errors->first() }}</div>@endif

    <section class="approval-panel">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 p-3 p-md-4">
            <div><h2 class="h6 fw-bold mb-1">Submissions</h2><p class="text-muted small mb-0">Approve or reject an artist’s submitted digital artwork.</p></div>
            <span class="badge rounded-pill px-3 py-2" style="background: var(--surface-strong); color: var(--studio-link)">{{ $artworks->where('status', 'pending')->count() }} awaiting review</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover approval-table align-middle">
                <thead><tr><th class="ps-3 ps-md-4">Artwork</th><th>Artist</th><th>Culture</th><th>Price</th><th>Submitted</th><th>Status</th><th>Review</th></tr></thead>
                <tbody>
                    @foreach ($artworks as $artwork)
                        <tr>
                            <td class="ps-3 ps-md-4"><span class="me-2 text-primary"><i class="bi {{ $artwork['icon'] ?? 'bi-image' }}"></i></span>{{ $artwork['title'] }} @if (!empty($artwork['file_name']))<small class="d-block text-muted ms-4">{{ $artwork['file_name'] }}</small>@endif @if (!empty($artwork['storage_path']))<a class="d-block small ms-4" href="{{ route('admin.approvals.file', $artwork['id']) }}">Review submitted file</a>@endif</td>
                            <td>{{ $artwork['artist'] }}</td>
                            <td>{{ $artwork['country'] }}</td>
                            <td class="text-nowrap">₱{{ number_format($artwork['price'], 2) }}</td>
                            <td class="text-nowrap">{{ $artwork['submitted_at'] ?? 'Oct 18, 2026' }}</td>
                            <td><span class="approval-status">{{ ucfirst($artwork['status']) }}</span></td>
                            <td class="approval-actions">
                                @if ($artwork['status'] === 'pending')
                                    <form method="POST" action="{{ route('admin.approvals.action', $artwork['id']) }}" class="d-flex flex-wrap gap-2">
                                        @csrf
                                        <button class="btn btn-sm btn-violet rounded-pill" type="submit" name="status" value="approved">Approve</button>
                                        <button class="btn btn-sm btn-outline-likha rounded-pill" type="submit" name="status" value="rejected">Reject</button>
                                    </form>
                                @else
                                    <span class="text-muted small">Reviewed</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    <p class="small text-muted mt-3"><i class="bi bi-info-circle me-1"></i>Review decisions are saved in the current prototype session.</p>
</div>
@endsection
