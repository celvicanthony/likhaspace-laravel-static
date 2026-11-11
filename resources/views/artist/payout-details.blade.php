@extends('layouts.app')

@section('title', 'Platform payout details | LikhaSpace')

@section('content')
<div class="mx-auto" style="max-width: 860px">
    <header class="mb-4">
        <div class="home-kicker">Artist workspace</div>
        <h1 class="h2 fw-bold mb-1">Platform payout details</h1>
        <p class="text-muted mb-0">Use these details to settle the platform commission tracked on your paid sales.</p>
    </header>

    <section class="bg-studio-card rounded-4 p-3 p-md-4">
        @if ($settings && (filled($settings->payment_method) || $payoutImageUrl))
            @if (filled($settings->payment_method))
                <div class="d-flex align-items-start gap-3">
                    <span class="rounded-circle bg-body-secondary p-3" aria-hidden="true"><i class="bi bi-bank"></i></span>
                    <div>
                        <h2 class="h5 fw-bold mb-1">{{ strtoupper($settings->payment_method) === 'INSTAPAY' ? 'InstaPay' : ($settings->payment_method === 'ewallet' ? 'E-wallet' : 'Bank transfer') }}</h2>
                        <p class="text-muted mb-3">{{ $settings->provider_name }}</p>
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Account holder</dt>
                            <dd class="col-sm-8">{{ $settings->account_name }}</dd>
                            <dt class="col-sm-4">Account details</dt>
                            <dd class="col-sm-8"><span class="user-select-all">{{ $settings->account_identifier }}</span></dd>
                        </dl>
                    </div>
                </div>
            @endif
            @if ($payoutImageUrl)
                <div class="border-top pt-3 mt-3">
                    <h2 class="h6 fw-bold mb-2">Payment image</h2>
                    <img src="{{ $payoutImageUrl }}" alt="Platform commission payment image" class="rounded-3 border bg-white p-2" style="max-width: 100%; max-height: 420px; height: auto; object-fit: contain" loading="eager" decoding="sync">
                </div>
            @endif
            @if (filled($settings->payment_method))
                <p class="small text-muted border-top pt-3 mt-3 mb-0">The platform commission is currently 10% of paid orders. Contact the platform administrator if you need help confirming or settling a commission.</p>
            @endif
        @else
            <div class="text-center py-4">
                <i class="bi bi-bank display-5 text-primary" aria-hidden="true"></i>
                <h2 class="h5 fw-bold mt-3">Payout details are not available yet</h2>
                <p class="text-muted mb-0">The platform administrator has not published a commission payout destination.</p>
            </div>
        @endif
    </section>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3 mt-1 mb-4">
        <div class="col"><article class="bg-studio-card rounded-4 p-3 h-100"><small class="text-muted">Commission due</small><strong class="d-block mt-1">₱{{ number_format($commissionDue, 2) }}</strong></article></div>
        <div class="col"><article class="bg-studio-card rounded-4 p-3 h-100"><small class="text-muted">Confirmed received</small><strong class="d-block mt-1">₱{{ number_format($amountReceived, 2) }}</strong></article></div>
        <div class="col"><article class="bg-studio-card rounded-4 p-3 h-100"><small class="text-muted">Pending review</small><strong class="d-block mt-1">₱{{ number_format($pendingAmount, 2) }}</strong></article></div>
        <div class="col"><article class="bg-studio-card rounded-4 p-3 h-100"><small class="text-muted">Outstanding balance</small><strong class="d-block mt-1">₱{{ number_format($outstanding, 2) }}</strong></article></div>
    </div>

    <section class="bg-studio-card rounded-4 p-3 p-md-4 mt-4" aria-labelledby="submit-commission-heading">
        <h2 id="submit-commission-heading" class="h5 fw-bold mb-2">Submit commission payment</h2>
        <p class="text-muted small">Send the commission to the platform account above, then submit the transfer details and receipt. The balance decreases only after an administrator confirms the amount received.</p>
        @if (session('status'))
            <div class="alert alert-success rounded-4" role="status">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger rounded-4" role="alert">{{ $errors->first() }}</div>
        @endif
        @if ($availableToSubmit > 0)
            <form method="POST" action="{{ route('artist.commission-settlements.submit') }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="commission-amount">Amount sent</label>
                        <input id="commission-amount" name="amount_sent" type="number" class="form-control @error('amount_sent') is-invalid @enderror" min="0.01" max="{{ number_format($availableToSubmit, 2, '.', '') }}" step="0.01" value="{{ old('amount_sent') }}" required>
                        <div class="form-text">Available to submit: ₱{{ number_format($availableToSubmit, 2) }}</div>
                        @error('amount_sent')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="commission-method">Payment method</label>
                        <select id="commission-method" name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                            <option value="">Choose a method</option>
                            <option value="bank_transfer" @selected(old('payment_method') === 'bank_transfer')>Bank transfer</option>
                            <option value="ewallet" @selected(old('payment_method') === 'ewallet')>E-wallet</option>
                            <option value="instapay" @selected(old('payment_method') === 'instapay')>InstaPay</option>
                        </select>
                        @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="commission-reference">Transaction reference</label>
                        <input id="commission-reference" name="payment_reference" class="form-control @error('payment_reference') is-invalid @enderror" value="{{ old('payment_reference') }}" maxlength="120" required>
                        @error('payment_reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="commission-proof">Payment receipt image</label>
                        <input id="commission-proof" name="payment_proof" type="file" class="form-control @error('payment_proof') is-invalid @enderror" accept="image/png,image/jpeg,image/webp" required>
                        <div class="form-text">PNG, JPG, or WebP up to 5 MB.</div>
                        @error('payment_proof')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="commission-note">Note <span class="text-muted">(optional)</span></label>
                        <textarea id="commission-note" name="artist_note" class="form-control @error('artist_note') is-invalid @enderror" rows="2" maxlength="1000">{{ old('artist_note') }}</textarea>
                        @error('artist_note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-likha rounded-pill px-4 mt-3"><i class="bi bi-send me-1"></i>Submit payment for review</button>
            </form>
        @else
            <div class="alert alert-info mb-0">{{ $pendingAmount > 0 ? 'Your submitted commission payment is waiting for admin review.' : 'There is no commission balance available to submit.' }}</div>
        @endif
    </section>

    <section class="bg-studio-card rounded-4 p-3 p-md-4 mt-4" aria-labelledby="commission-history-heading">
        <h2 id="commission-history-heading" class="h5 fw-bold mb-3">Commission payment history</h2>
        @forelse ($settlements as $settlement)
            <article class="border-top py-3">
                <div class="d-flex flex-wrap justify-content-between gap-2">
                    <div>
                        <div class="small text-muted">{{ $settlement->public_id }}</div>
                        <strong>{{ ucfirst($settlement->status) }} · {{ $settlement->payment_method }}</strong>
                        <div class="small text-muted">Reference: {{ $settlement->payment_reference }} · Submitted {{ $settlement->created_at->format('M j, Y g:i A') }}</div>
                    </div>
                    <div class="text-end"><strong>Sent ₱{{ number_format($settlement->amount_sent, 2) }}</strong>
                        @if ($settlement->status === 'approved')
                            <div class="small text-success">Confirmed ₱{{ number_format($settlement->amount_received, 2) }}</div>
                        @endif
                    </div>
                </div>
                <a class="btn btn-outline-likha btn-sm rounded-pill mt-2" href="{{ request()->getBaseUrl() . route('artist.commission-settlements.proof', $settlement, false) }}" target="_blank" rel="noopener">View submitted receipt</a>
                @if ($settlement->admin_note)<p class="small text-muted mt-2 mb-0">Admin note: {{ $settlement->admin_note }}</p>@endif
            </article>
        @empty
            <p class="text-muted mb-0">No commission payments submitted yet.</p>
        @endforelse
        <div class="mt-3">{{ $settlements->links() }}</div>
    </section>
</div>
@endsection
