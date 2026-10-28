@extends('layouts.app')

@section('title', 'Track order | LikhaSpace')

@php
    $steps = ['Order placed', 'Processing', 'Ready for download'];
    $currentStep = match ($transaction['status']) {
        'Processing' => 2,
        'Ready for download' => 3,
        default => 1,
    };
@endphp

@push('styles')
<style>
    .transaction-page { max-width: 1000px; margin: 0 auto; }
    .transaction-heading { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 12px; }
    .transaction-code { color: var(--studio-link); font-size: .82rem; font-weight: 700; overflow-wrap: anywhere; }
    .transaction-panel { border: 1px solid var(--studio-border); border-radius: 20px; background: var(--studio-card); box-shadow: var(--shadow); }
    .transaction-panel h2 { font-size: 1.05rem; font-weight: 700; }
    .transaction-timeline { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); margin: 24px 0 12px; }
    .transaction-step { position: relative; min-width: 0; color: var(--muted); text-align: center; }
    .transaction-step:not(:last-child)::after { position: absolute; top: 16px; left: calc(50% + 17px); width: calc(100% - 34px); height: 2px; background: var(--studio-border); content: ""; }
    .transaction-step.is-complete:not(:last-child)::after { background: var(--studio-primary); }
    .step-marker { position: relative; z-index: 1; display: grid; width: 34px; height: 34px; margin: 0 auto 9px; place-items: center; border: 1px solid var(--studio-border); border-radius: 50%; background: var(--surface); color: var(--muted); }
    .transaction-step.is-complete .step-marker { border-color: var(--studio-primary); background: var(--studio-primary); color: #fff; }
    .transaction-step.is-current { color: var(--studio-link); font-weight: 700; }
    .step-label { display: block; font-size: .75rem; }
    .transaction-item { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 0; border-top: 1px solid var(--studio-border); }
    .transaction-item:first-of-type { margin-top: 12px; }
    .transaction-item-title { color: var(--ink); font-size: .86rem; font-weight: 700; }
    .transaction-item-meta { margin-top: 3px; color: var(--muted); font-size: .76rem; }
    .transaction-total { color: var(--ink); font-weight: 700; white-space: nowrap; }
    .transaction-detail { display: flex; justify-content: space-between; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--studio-border); font-size: .84rem; }
    .transaction-detail:last-child { border-bottom: 0; }
    .transaction-detail span:first-child { color: var(--muted); }
    .transaction-detail span:last-child { color: var(--ink); font-weight: 600; text-align: right; }
    @media (max-width: 575.98px) { .transaction-panel { border-radius: 16px; } .transaction-timeline { margin-right: -6px; margin-left: -6px; } .step-label { font-size: .66rem; } .transaction-step:not(:last-child)::after { top: 15px; left: calc(50% + 15px); width: calc(100% - 30px); } .step-marker { width: 32px; height: 32px; } }
</style>
@endpush

@section('content')
<div class="transaction-page">
    <div class="transaction-heading mb-4">
        <div>
            <div class="home-kicker">Order tracking</div>
            <h1 class="h2 fw-bold mb-1">Order status</h1>
            <div class="transaction-code">{{ $transaction['order_code'] }}</div>
        </div>
        <a href="{{ route('transactions') }}" class="btn btn-outline-likha rounded-pill"><i class="bi bi-arrow-left me-1"></i>All orders</a>
    </div>

    @if (session('status'))
        <div class="alert bg-studio-card rounded-4" role="status"><i class="bi bi-check-circle me-2 text-primary"></i>{{ session('status') }}</div>
    @endif
    <div class="alert bg-studio-card rounded-4 small" role="note">
        <i class="bi bi-info-circle me-2 text-primary"></i>This is a simulated order flow for the prototype. No payment has been collected.
    </div>

    <section class="transaction-panel p-3 p-md-4 mb-4" aria-labelledby="progress-heading">
        <h2 id="progress-heading">Fulfillment progress</h2>
        <div class="transaction-timeline">
            @foreach ($steps as $index => $step)
                @php($stepNumber = $index + 1)
                <div class="transaction-step {{ $stepNumber <= $currentStep ? 'is-complete' : '' }} {{ $stepNumber === $currentStep ? 'is-current' : '' }}" @if ($stepNumber === $currentStep) aria-current="step" @endif>
                    <span class="step-marker"><i class="bi {{ $stepNumber < $currentStep ? 'bi-check-lg' : ($stepNumber === $currentStep ? 'bi-record-circle' : 'bi-circle') }}"></i></span>
                    <span class="step-label">{{ $step }}</span>
                </div>
            @endforeach
        </div>
        <p class="text-muted small mb-0 mt-3">Placed {{ \Illuminate\Support\Carbon::parse($transaction['placed_at'])->format('M j, Y \a\t g:i A') }}</p>
    </section>

    <div class="row g-3">
        <div class="col-12 col-lg-7">
            <section class="transaction-panel p-3 p-md-4 h-100" aria-labelledby="items-heading">
                <h2 id="items-heading">Items in this order</h2>
                @foreach ($transaction['items'] as $item)
                    <div class="transaction-item">
                        <div class="min-w-0">
                            <div class="transaction-item-title">{{ $item['title'] }}</div>
                            <div class="transaction-item-meta">{{ $item['country'] }} · {{ $item['artist'] }} · Qty {{ $item['quantity'] }}</div>
                        </div>
                        <span class="transaction-total">₱{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                    </div>
                @endforeach
                <div class="d-flex justify-content-between gap-3 fw-bold mt-3"><span>Total</span><span class="text-nowrap">₱{{ number_format($transaction['total'], 2) }}</span></div>
            </section>
        </div>
        <div class="col-12 col-lg-5">
            <section class="transaction-panel p-3 p-md-4 h-100" aria-labelledby="details-heading">
                <h2 id="details-heading">Transaction details</h2>
                <div class="transaction-detail"><span>Order number</span><span>{{ $transaction['order_code'] }}</span></div>
                <div class="transaction-detail"><span>Payment method</span><span>{{ $transaction['payment'] }}</span></div>
                <div class="transaction-detail"><span>Payment status</span><span>Not charged · Demo</span></div>
                <div class="transaction-detail"><span>Order status</span><span>{{ $transaction['status'] }}</span></div>
            </section>
        </div>
    </div>
    @if ($transaction['status'] === 'Ready for download')
        <div class="alert bg-studio-card rounded-4 mt-3 mb-0" role="status"><i class="bi bi-download me-2 text-primary"></i>Your artwork is ready. Digital downloads are simulated in this prototype.</div>
    @endif
</div>
@endsection
