@extends('layouts.app')

@section('title', 'Commission settlements | LikhaSpace')

@push('styles')
<style>
    .commission-metric { height: 100%; padding: 18px; border: 1px solid var(--studio-border); border-radius: 18px; background: var(--studio-card); color: var(--studio-text); box-shadow: var(--shadow); }
    .commission-metric-label { display: flex; align-items: center; gap: 8px; color: var(--studio-muted); font-size: .82rem; font-weight: 600; }
    .commission-metric-value { display: block; margin-top: 8px; color: var(--studio-text); font-size: clamp(1.2rem, 2vw, 1.65rem); font-weight: 700; overflow-wrap: anywhere; }
    .commission-metric-icon { display: grid; width: 34px; height: 34px; flex: 0 0 34px; place-items: center; border-radius: 11px; background: var(--surface-strong); color: var(--studio-link); }
</style>
@endpush

@section('content')
<div class="mx-auto" style="max-width: 1180px">
    <header class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <div class="home-kicker">Platform administration</div>
            <h1 class="h2 fw-bold mb-1">Artist commission settlements</h1>
            <p class="text-muted mb-0">{{ $pendingCount }} payment{{ $pendingCount === 1 ? '' : 's' }} waiting for review. Only confirmed amounts reduce an artist's outstanding commission.</p>
        </div>
        <a href="{{ route('admin.payout-details') }}" class="btn btn-outline-likha rounded-pill"><i class="bi bi-bank me-1"></i>Payout details</a>
    </header>

    @if (session('status'))
        <div class="alert alert-success rounded-4" role="status">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger rounded-4" role="alert">{{ $errors->first() }}</div>
    @endif

    <section class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mb-4" aria-label="Platform commission summary">
        <div class="col"><article class="commission-metric"><span class="commission-metric-label"><i class="commission-metric-icon bi bi-graph-up-arrow" aria-hidden="true"></i>Commission generated</span><strong class="commission-metric-value">₱{{ number_format($commissionDue, 2) }}</strong></article></div>
        <div class="col"><article class="commission-metric"><span class="commission-metric-label"><i class="commission-metric-icon bi bi-check2-circle" aria-hidden="true"></i>Confirmed received</span><strong class="commission-metric-value">₱{{ number_format($amountReceived, 2) }}</strong></article></div>
        <div class="col"><article class="commission-metric"><span class="commission-metric-label"><i class="commission-metric-icon bi bi-hourglass-split" aria-hidden="true"></i>Pending review</span><strong class="commission-metric-value">₱{{ number_format($pendingAmount, 2) }}</strong></article></div>
        <div class="col"><article class="commission-metric"><span class="commission-metric-label"><i class="commission-metric-icon bi bi-wallet2" aria-hidden="true"></i>Outstanding commission</span><strong class="commission-metric-value">₱{{ number_format($outstandingCommission, 2) }}</strong></article></div>
    </section>

    <section class="d-grid gap-3" aria-label="Artist commission payment submissions">
        @forelse ($settlements as $settlement)
            <article class="bg-studio-card rounded-4 p-3 p-md-4">
                <header class="d-flex flex-wrap justify-content-between gap-2">
                    <div>
                        <h2 class="h5 fw-bold mb-1">{{ $settlement->artist->name }} <span class="badge rounded-pill {{ $settlement->status === 'submitted' ? 'text-bg-warning' : ($settlement->status === 'approved' ? 'text-bg-success' : 'text-bg-secondary') }}">{{ ucfirst($settlement->status) }}</span></h2>
                        <p class="small text-muted mb-1">{{ $settlement->public_id }} · {{ $settlement->artist->email }} · Submitted {{ $settlement->created_at->format('M j, Y g:i A') }}</p>
                    </div>
                    <div class="text-end"><span class="small text-muted">Artist reported sent</span><strong class="d-block">₱{{ number_format($settlement->amount_sent, 2) }}</strong></div>
                </header>
                <dl class="row small mt-3 mb-2">
                    <dt class="col-sm-3">Method</dt><dd class="col-sm-9">{{ strtoupper($settlement->payment_method) === 'INSTAPAY' ? 'InstaPay' : ($settlement->payment_method === 'ewallet' ? 'E-wallet' : 'Bank transfer') }}</dd>
                    <dt class="col-sm-3">Reference</dt><dd class="col-sm-9">{{ $settlement->payment_reference }}</dd>
                    @if ($settlement->artist_note)<dt class="col-sm-3">Artist note</dt><dd class="col-sm-9">{{ $settlement->artist_note }}</dd>@endif
                </dl>
                <a class="btn btn-outline-likha btn-sm rounded-pill mb-3" href="{{ request()->getBaseUrl() . route('admin.commission-settlements.proof', $settlement, false) }}" target="_blank" rel="noopener">View receipt image</a>
                @if ($settlement->status === 'submitted')
                    <div class="border-top pt-3">
                        <form method="POST" action="{{ route('admin.commission-settlements.review', $settlement) }}" class="d-flex flex-wrap align-items-end gap-2 mb-2">
                            @csrf
                            <input type="hidden" name="decision" value="approve">
                            <div>
                                <label class="form-label small mb-1" for="received-{{ $settlement->id }}">Amount confirmed received</label>
                                <input id="received-{{ $settlement->id }}" name="amount_received" type="number" class="form-control form-control-sm" min="0.01" max="{{ number_format($settlement->amount_sent, 2, '.', '') }}" step="0.01" value="{{ number_format($settlement->amount_sent, 2, '.', '') }}" required>
                            </div>
                            <button class="btn btn-success btn-sm rounded-pill px-3" type="submit">Confirm received</button>
                        </form>
                        <form method="POST" action="{{ route('admin.commission-settlements.review', $settlement) }}" class="d-flex flex-wrap gap-2">
                            @csrf
                            <input type="hidden" name="decision" value="reject">
                            <input class="form-control form-control-sm" style="max-width: 420px" name="admin_note" maxlength="1000" placeholder="Reason to request corrected information" required>
                            <button class="btn btn-outline-danger btn-sm rounded-pill px-3" type="submit">Reject submission</button>
                        </form>
                    </div>
                @else
                    <div class="border-top pt-3 small">
                        @if ($settlement->status === 'approved')
                            <span class="text-success">Confirmed received: ₱{{ number_format($settlement->amount_received, 2) }}</span>
                        @endif
                        @if ($settlement->reviewed_at)
                            <span class="text-muted"> · Reviewed {{ $settlement->reviewed_at->format('M j, Y g:i A') }}</span>
                        @endif
                        @if ($settlement->admin_note)<div class="text-muted mt-1">Admin note: {{ $settlement->admin_note }}</div>@endif
                    </div>
                @endif
            </article>
        @empty
            <div class="bg-studio-card rounded-4 p-4 text-center">
                <h2 class="h5 fw-bold">No commission submissions yet</h2>
                <p class="text-muted mb-0">Artist payment receipts will appear here for review.</p>
            </div>
        @endforelse
    </section>
    <div class="mt-4">{{ $settlements->links() }}</div>
</div>
@endsection
