
@extends('layouts.app')

@section('title', 'Artwork Approvals | LikhaSpace')

@push('styles')
<style>
    .approval-panel {
        overflow: hidden;
        border: 1px solid var(--studio-border);
        border-radius: 20px;
        background: var(--studio-card);
        box-shadow: var(--shadow);
    }

    .approval-table {
        --bs-table-bg: transparent;
        --bs-table-color: var(--studio-text);
        --bs-table-hover-bg: var(--surface-soft);
        --bs-table-hover-color: var(--studio-text);
        margin: 0;
    }

    .approval-table th {
        background: var(--surface-soft);
        color: var(--studio-muted);
        font-size: .72rem;
        white-space: nowrap;
    }

    .approval-table td,
    .approval-table th {
        border-color: var(--studio-border);
        vertical-align: middle;
    }

    .approval-status {
        display: inline-block;
        padding: 5px 9px;
        border: 1px solid var(--studio-border);
        border-radius: 999px;
        background: var(--surface-strong);
        color: var(--studio-link);
        font-size: .72rem;
        font-weight: 700;
    }

    .approval-actions {
        min-width: 170px;
    }

    .approval-description {
        max-width: 250px;
        white-space: normal;
        font-size: .78rem;
        color: var(--studio-muted);
    }
</style>
@endpush

@section('content')
<div class="mx-auto" style="max-width: 1480px">

    {{-- PAGE HEADER --}}
    <header class="mb-4">
        <div class="home-kicker">Marketplace quality</div>

        <h1 class="h2 fw-bold mb-1">
            Artwork approvals
        </h1>

        <p class="text-muted mb-0">
            Review submissions from independent artists before they appear in the public catalog.
        </p>
    </header>

    {{-- SUCCESS MESSAGE --}}
    @if (session('status'))
        <div class="alert bg-studio-card rounded-4" role="status">
            {{ session('status') }}
        </div>
    @endif

    {{-- VALIDATION ERRORS --}}
    @if ($errors->any())
        <div class="alert alert-danger rounded-4" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- APPROVAL PANEL --}}
    <section class="approval-panel">

        {{-- PANEL HEADER --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 p-3 p-md-4">

            <div>
                <h2 class="h6 fw-bold mb-1">
                    Artwork submissions
                </h2>

                <p class="text-muted small mb-0">
                    Approve or reject artwork submitted by artists.
                </p>
            </div>

            <span
                class="badge rounded-pill px-3 py-2"
                style="background: var(--surface-strong); color: var(--studio-link)"
            >
                {{ collect($artworks)->where('status', 'pending')->count() }}
                awaiting review
            </span>

        </div>

        {{-- SUBMISSIONS TABLE --}}
        <div class="table-responsive">

            <table class="table table-hover approval-table align-middle">

                <thead>
                    <tr>
                        <th class="ps-3 ps-md-4">Artwork</th>
                        <th>Artist</th>
                        <th>Culture</th>
                        <th>Price</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th>Review</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($artworks as $artwork)

                        <tr>

                            {{-- ARTWORK INFORMATION --}}
                            <td class="ps-3 ps-md-4">

                                <div class="fw-semibold">
                                    <i class="bi bi-image text-primary me-2"></i>
                                    {{ $artwork['title'] }}
                                </div>

                                @if (!empty($artwork['description']))
                                    <div class="approval-description ms-4">
                                        {{ \Illuminate\Support\Str::limit($artwork['description'], 100) }}
                                    </div>
                                @endif

                                @if (!empty($artwork['file_name']))
                                    <small class="d-block text-muted ms-4">
                                        {{ $artwork['file_name'] }}
                                    </small>
                                @endif

                                <a
                                    href="{{ route('admin.approvals.file', $artwork['id']) }}"
                                    class="d-inline-block small ms-4 mt-1"
                                >
                                    <i class="bi bi-download me-1"></i>
                                    Download submitted file
                                </a>

                            </td>

                            {{-- ARTIST --}}
                            <td>
                                {{ $artwork['artist'] }}
                            </td>

                            {{-- COUNTRY --}}
                            <td>
                                {{ $artwork['country'] }}
                            </td>

                            {{-- PRICE --}}
                            <td class="text-nowrap">
                                ₱{{ number_format((float) $artwork['price'], 2) }}
                            </td>

                            {{-- SUBMISSION DATE --}}
                            <td class="text-nowrap">
                                {{ $artwork['submitted_at'] ?? '—' }}
                            </td>

                            {{-- APPROVAL STATUS --}}
                            <td>
                                <span class="approval-status">
                                    {{ ucfirst($artwork['status']) }}
                                </span>
                            </td>

                            {{-- APPROVE / REJECT BUTTONS --}}
                            <td class="approval-actions">

                                @if ($artwork['status'] === 'pending')

                                    <form
                                        method="POST"
                                        action="{{ route('admin.approvals.action', $artwork['id']) }}"
                                        class="d-flex flex-wrap gap-2"
                                        onsubmit="return confirm('Are you sure you want to {{ $artwork['status'] === 'pending' ? 'submit this review decision' : 'continue' }}?');"
                                    >
                                        @csrf

                                        <button
                                            class="btn btn-sm btn-violet rounded-pill"
                                            type="submit"
                                            name="status"
                                            value="approved"
                                        >
                                            <i class="bi bi-check-circle me-1"></i>
                                            Approve
                                        </button>

                                        <button
                                            class="btn btn-sm btn-outline-danger rounded-pill"
                                            type="submit"
                                            name="status"
                                            value="rejected"
                                        >
                                            <i class="bi bi-x-circle me-1"></i>
                                            Reject
                                        </button>

                                    </form>

                                @else

                                    <span class="text-muted small">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Reviewed
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="text-center py-5">

                                <i class="bi bi-inbox fs-1 text-muted"></i>

                                <h3 class="h6 fw-bold mt-3">
                                    No artwork submissions yet
                                </h3>

                                <p class="text-muted small mb-0">
                                    Artist submissions will appear here when they upload their artwork.
                                </p>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </section>

    <p class="small text-muted mt-3">
        <i class="bi bi-info-circle me-1"></i>
        Approval decisions are saved in the database. Only approved artworks should be visible in the public Explore catalog.
    </p>

</div>
@endsection
