<?php

namespace App\Http\Controllers;

use App\Models\PlatformPayoutSetting;
use App\Support\PlatformPayoutImage;
use App\Support\PrivateImageResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;

class PlatformPayoutController extends Controller
{
    public function edit(): View
    {
        $settings = PlatformPayoutSetting::find(1);

        return view('admin.payout-details', [
            'settings' => $settings,
            'payoutImageUrl' => PlatformPayoutImage::url($settings?->payout_qr_path),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'payment_method' => ['nullable', 'in:bank_transfer,ewallet,instapay', 'required_with:provider_name,account_name,account_identifier'],
            'provider_name' => ['nullable', 'string', 'max:120', 'required_with:payment_method'],
            'account_name' => ['nullable', 'string', 'max:120', 'required_with:payment_method'],
            'account_identifier' => ['nullable', 'string', 'max:160', 'required_with:payment_method'],
            'payout_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $settings = PlatformPayoutSetting::find(1) ?? new PlatformPayoutSetting(['id' => 1]);
        $oldImagePath = $settings->payout_qr_path;
        $newImagePath = null;
        if ($request->hasFile('payout_image')) {
            $newImagePath = $request->file('payout_image')->store('platform-payout-images', 'public');
            if (! is_string($newImagePath) || ! Storage::disk('public')->exists($newImagePath)) {
                if (is_string($newImagePath)) {
                    Storage::disk('public')->delete($newImagePath);
                }
                throw new RuntimeException('Unable to store the platform payout image.');
            }
        }

        $settings->fill([
            'payment_method' => $validated['payment_method'] ?? null,
            'provider_name' => $validated['provider_name'] ?? null,
            'account_name' => $validated['account_name'] ?? null,
            'account_identifier' => $validated['account_identifier'] ?? null,
        ]);
        if ($newImagePath !== null) {
            $settings->payout_qr_path = $newImagePath;
        }

        if (! $settings->save()) {
            if ($newImagePath !== null) {
                Storage::disk('public')->delete($newImagePath);
            }
            throw new RuntimeException('Unable to save the platform payout settings.');
        }

        if ($newImagePath !== null && PlatformPayoutImage::isPath($oldImagePath)) {
            Storage::disk('public')->delete($oldImagePath);
            Storage::disk('local')->delete($oldImagePath);
        }

        return redirect()
            ->route('admin.payout-details')
            ->with('status', 'Platform payout details saved.');
    }

    public function image(): Response
    {
        $path = PlatformPayoutSetting::find(1)?->payout_qr_path;
        if (PlatformPayoutImage::isPath($path) && Storage::disk('public')->exists($path)) {
            $mimeType = Storage::disk('public')->mimeType($path);
            abort_unless(in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp'], true), 404);

            $response = response(Storage::disk('public')->get($path), 200, [
                'Content-Type' => $mimeType,
            ]);
            $response->headers->set('X-Content-Type-Options', 'nosniff');

            return $response;
        }

        $directory = is_string($path) && str_starts_with($path, 'platform-payouts/')
            ? 'platform-payouts'
            : 'platform-payout-images';

        return PrivateImageResponse::make($path, $directory);
    }
}
