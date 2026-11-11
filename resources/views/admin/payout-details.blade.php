@extends('layouts.app')

@section('title', 'Platform payout details | LikhaSpace')

@section('content')
<div class="mx-auto" style="max-width: 860px">
    <header class="mb-4">
        <div class="home-kicker">Platform administration</div>
        <h1 class="h2 fw-bold mb-1">Commission payout details</h1>
        <p class="text-muted mb-0">Manage where artists send the platform commission on paid artwork sales.</p>
    </header>

    @if (session('status'))
        <div class="alert alert-success rounded-4" role="status">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger rounded-4" role="alert">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.payout-details.update') }}" enctype="multipart/form-data" class="bg-studio-card rounded-4 p-3 p-md-4">
        @csrf
        <p class="text-muted small">These payment destination details are encrypted at rest and visible only to signed-in artists in their payout section.</p>
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label" for="payment-method">Payment type</label>
                <select id="payment-method" name="payment_method" class="form-select @error('payment_method') is-invalid @enderror">
                    <option value="">Not configured</option>
                    <option value="bank_transfer" @selected(old('payment_method', $settings?->payment_method) === 'bank_transfer')>Bank transfer</option>
                    <option value="ewallet" @selected(old('payment_method', $settings?->payment_method) === 'ewallet')>E-wallet</option>
                    <option value="instapay" @selected(old('payment_method', $settings?->payment_method) === 'instapay')>InstaPay</option>
                </select>
                @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label" for="provider-name">Bank or service name</label>
                <input id="provider-name" name="provider_name" class="form-control @error('provider_name') is-invalid @enderror" value="{{ old('provider_name', $settings?->provider_name) }}" maxlength="120" placeholder="e.g. BPI, GCash">
                @error('provider_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label" for="account-name">Account holder name</label>
                <input id="account-name" name="account_name" class="form-control @error('account_name') is-invalid @enderror" value="{{ old('account_name', $settings?->account_name) }}" maxlength="120" autocomplete="off">
                @error('account_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label" for="account-identifier">Account number or payment identifier</label>
                <input id="account-identifier" name="account_identifier" class="form-control @error('account_identifier') is-invalid @enderror" value="{{ old('account_identifier', $settings?->account_identifier) }}" maxlength="160" autocomplete="off">
                @error('account_identifier')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="payout-image">Payment image <span class="text-muted">(optional)</span></label>
                @if ($payoutImageUrl)
                    <div class="mb-3">
                        <img src="{{ $payoutImageUrl }}" alt="Current platform payment image" class="rounded-3 border bg-white p-2" style="max-width: 100%; max-height: 360px; height: auto; object-fit: contain">
                    </div>
                @else
                    <p class="text-muted small mb-3">No payment image uploaded yet.</p>
                @endif
                <input id="payout-image" name="payout_image" type="file" class="form-control @error('payout_image') is-invalid @enderror" accept="image/png,image/jpeg,image/webp">
                @error('payout_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text text-muted">Upload an image for artists to view. PNG, JPG, or WebP up to 5 MB. Leave empty to keep the current image.</div>
                <img id="payout-image-preview" alt="Selected payment image preview" class="rounded-3 border bg-white p-2 mt-3 d-none" style="max-width: 100%; max-height: 360px; height: auto; object-fit: contain">
            </div>
        </div>
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4">
            <span class="text-muted small">Leave all fields empty to remove the published payout destination.</span>
            <button type="submit" class="btn btn-likha rounded-pill px-4"><i class="bi bi-check2 me-1"></i>Save payout details</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('payout-image')?.addEventListener('change', event => {
        const preview = document.getElementById('payout-image-preview');
        const file = event.target.files?.[0];
        if (!file || !preview) {
            preview?.classList.add('d-none');
            return;
        }

        preview.src = URL.createObjectURL(file);
        preview.classList.remove('d-none');
    });
</script>
@endpush
