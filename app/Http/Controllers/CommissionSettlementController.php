<?php

namespace App\Http\Controllers;

use App\Models\ArtistCommissionPayment;
use App\Models\Order;
use App\Models\PlatformPayoutSetting;
use App\Models\User;
use App\Support\PlatformPayoutImage;
use App\Support\PrivateImageResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;

class CommissionSettlementController extends Controller
{
    public function artistIndex(Request $request): View
    {
        $artistId = $request->user()->id;
        $settings = PlatformPayoutSetting::find(1);
        $commissionDue = $this->commissionDue($artistId);
        $amountReceived = $this->amountReceived($artistId);
        $pendingAmount = $this->pendingAmount($artistId);
        $outstanding = max(0, round($commissionDue - $amountReceived, 2));
        $availableToSubmit = max(0, round($outstanding - $pendingAmount, 2));

        return view('artist.payout-details', [
            'settings' => $settings,
            'payoutImageUrl' => PlatformPayoutImage::url($settings?->payout_qr_path),
            'commissionDue' => $commissionDue,
            'amountReceived' => $amountReceived,
            'pendingAmount' => $pendingAmount,
            'outstanding' => $outstanding,
            'availableToSubmit' => $availableToSubmit,
            'settlements' => ArtistCommissionPayment::where('artist_id', $artistId)
                ->latest()
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    public function submit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount_sent' => ['required', 'numeric', 'gt:0', 'max:99999999.99'],
            'payment_method' => ['required', 'in:bank_transfer,ewallet,instapay'],
            'payment_reference' => ['required', 'string', 'max:120'],
            'artist_note' => ['nullable', 'string', 'max:1000'],
            'payment_proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $artist = $request->user();
        $amountSent = round((float) $validated['amount_sent'], 2);

        $proofPath = $request->file('payment_proof')->store("commission-settlements/{$artist->id}", 'local');
        if (! is_string($proofPath) || ! Storage::disk('local')->exists($proofPath)) {
            throw new RuntimeException('Unable to store the commission payment proof.');
        }

        try {
            DB::transaction(function () use ($artist, $amountSent, $validated, $proofPath): void {
                User::whereKey($artist->id)->lockForUpdate()->firstOrFail();
                $available = max(0, round(
                    $this->commissionDue($artist->id)
                    - $this->amountReceived($artist->id)
                    - $this->pendingAmount($artist->id),
                    2
                ));
                if ($amountSent > $available) {
                    throw ValidationException::withMessages([
                        'amount_sent' => 'The amount cannot exceed your commission balance available to submit.',
                    ]);
                }

                ArtistCommissionPayment::create([
                    'artist_id' => $artist->id,
                    'amount_sent' => $amountSent,
                    'payment_method' => $validated['payment_method'],
                    'payment_reference' => trim($validated['payment_reference']),
                    'artist_note' => $validated['artist_note'] ?? null,
                    'proof_path' => $proofPath,
                    'status' => 'submitted',
                ]);
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($proofPath);
            throw $exception;
        }

        return redirect()
            ->route('artist.payout-details')
            ->with('status', 'Commission payment submitted. The administrator will review your receipt.');
    }

    public function adminIndex(): View
    {
        $commissionDue = (float) Order::where('payment_status', 'paid')->sum('platform_commission');
        $amountReceived = (float) ArtistCommissionPayment::where('status', 'approved')->sum('amount_received');

        return view('admin.commission-settlements', [
            'settlements' => ArtistCommissionPayment::with('artist')
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            'pendingCount' => ArtistCommissionPayment::where('status', 'submitted')->count(),
            'commissionDue' => $commissionDue,
            'amountReceived' => $amountReceived,
            'outstandingCommission' => max(0, round($commissionDue - $amountReceived, 2)),
            'pendingAmount' => (float) ArtistCommissionPayment::where('status', 'submitted')->sum('amount_sent'),
        ]);
    }

    public function review(Request $request, ArtistCommissionPayment $settlement): RedirectResponse
    {
        $validated = $request->validate([
            'decision' => ['required', 'in:approve,reject'],
            'amount_received' => ['nullable', 'numeric', 'gt:0', 'max:99999999.99', 'required_if:decision,approve'],
            'admin_note' => ['nullable', 'string', 'max:1000', 'required_if:decision,reject'],
        ]);

        DB::transaction(function () use ($request, $settlement, $validated): void {
            User::whereKey($settlement->artist_id)->lockForUpdate()->firstOrFail();
            $payment = ArtistCommissionPayment::whereKey($settlement->id)
                ->lockForUpdate()
                ->firstOrFail();
            abort_unless($payment->status === 'submitted', 409, 'This commission payment has already been reviewed.');

            $approved = $validated['decision'] === 'approve';
            $received = $approved ? round((float) $validated['amount_received'], 2) : 0;
            if ($approved) {
                abort_if($received > (float) $payment->amount_sent, 422, 'The confirmed amount cannot exceed the amount the artist reported sending.');
                abort_if($received > max(0, round(
                    $this->commissionDue($payment->artist_id) - $this->amountReceived($payment->artist_id),
                    2
                )), 422, 'The confirmed amount cannot exceed this artist’s outstanding commission.');
            }

            $payment->update([
                'status' => $approved ? 'approved' : 'rejected',
                'amount_received' => $received,
                'admin_note' => $validated['admin_note'] ?? null,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);
        });

        return redirect()
            ->route('admin.commission-settlements')
            ->with('status', $validated['decision'] === 'approve'
                ? 'Commission payment confirmed. The artist’s outstanding commission was updated.'
                : 'Commission payment rejected. The artist can submit corrected payment information.');
    }

    public function proof(Request $request, ArtistCommissionPayment $settlement): Response
    {
        abort_unless(
            $request->user()->role === 'admin'
                || $settlement->artist_id === $request->user()->id,
            404
        );

        return PrivateImageResponse::make(
            $settlement->proof_path,
            "commission-settlements/{$settlement->artist_id}"
        );
    }

    private function commissionDue(int $artistId): float
    {
        return (float) Order::where('seller_id', $artistId)
            ->where('payment_status', 'paid')
            ->sum('platform_commission');
    }

    private function amountReceived(int $artistId): float
    {
        return (float) ArtistCommissionPayment::where('artist_id', $artistId)
            ->where('status', 'approved')
            ->sum('amount_received');
    }

    private function pendingAmount(int $artistId): float
    {
        return (float) ArtistCommissionPayment::where('artist_id', $artistId)
            ->where('status', 'submitted')
            ->sum('amount_sent');
    }
}
