<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class ProfileController extends Controller
{
    public function editClient(Request $request): View
    {
        $user = $request->user();
        abort_unless($user->role === 'client', 403);

        return view('client.profile.edit', [
            'user' => $user,
            'profile' => $user->profile ?? [],
        ]);
    }

    public function editArtist(Request $request): View
    {
        $user = $request->user();
        abort_unless($user->role === 'artist', 403);

        $paymentSettings = $user->paymentSettings;
        $artist = array_replace(
            [
                'name' => $user->name,
                'email' => $user->email,
                'country' => 'Philippines',
                'studio' => $user->name,
                'bio' => '',
                'contact' => '',
                'portfolio_links' => [],
                'notifications' => [],
                'payment_method' => $paymentSettings?->payment_method,
                'payment_account_name' => $paymentSettings?->payment_account_name,
                'payment_account_identifier' => $paymentSettings?->payment_account_identifier,
                'payment_qr_configured' => filled($paymentSettings?->payment_qr_path)
                    && Storage::disk('local')->exists($paymentSettings->payment_qr_path),
            ],
            config("demo.artists.{$user->artist_id}", []),
            $user->profile ?? [],
            [
                'name' => $user->name,
                'email' => $user->email,
            ]
        );
        $artist['portfolio_links'] = is_array($artist['portfolio_links'] ?? null)
            ? $artist['portfolio_links']
            : [];

        return view('artist.profile.edit', compact('artist'));
    }

    public function updateClient(Request $request): RedirectResponse
    {
        return $this->saveMarketplaceProfile($request, 'client');
    }

    public function updateArtist(Request $request): RedirectResponse
    {
        return $this->saveMarketplaceProfile($request, 'artist');
    }

    private function saveMarketplaceProfile(Request $request, string $role): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless($user->role === $role, 403);

        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->getKey())],
            'contact' => ['nullable', 'string', 'max:40'],
            'notifications' => ['nullable', 'array'],
            'notifications.*' => ['boolean'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];

        if ($role === 'artist') {
            $rules += [
                'country' => ['required', Rule::in(['Philippines', 'Greece', 'Scotland', 'Japan'])],
                'studio' => ['required', 'string', 'max:120'],
                'bio' => ['required', 'string', 'max:500'],
                'portfolio_links' => ['nullable', 'array', 'max:10'],
                'portfolio_links.*' => ['nullable', 'url', 'max:2048'],
                'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
                'payment_method' => ['nullable', Rule::in(['gcash', 'instapay'])],
                'payment_account_name' => ['nullable', 'string', 'max:120', 'required_with:payment_method,payment_account_identifier'],
                'payment_account_identifier' => ['nullable', 'string', 'max:120', 'required_with:payment_method,payment_account_name'],
            ];
        } else {
            $rules += [
                'bio' => ['nullable', 'string', 'max:400'],
                'address' => ['nullable', 'string', 'max:255'],
                'city' => ['nullable', 'string', 'max:120'],
                'province' => ['nullable', 'string', 'max:120'],
                'postal' => ['nullable', 'string', 'max:20'],
            ];
        }

        $validated = $request->validate($rules);
        $profile = $user->profile ?? [];
        $oldEmail = $user->email;
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $paymentSettings = $role === 'artist'
            ? $user->paymentSettings()->firstOrNew()
            : null;

        if ($oldEmail !== $user->email) {
            $user->email_verified_at = null;
        }

        $profileFields = $role === 'artist'
            ? ['country', 'studio', 'bio', 'contact']
            : ['bio', 'contact', 'address', 'city', 'province', 'postal'];

        foreach ($profileFields as $field) {
            $profile[$field] = $validated[$field] ?? '';
        }

        if ($role === 'artist') {
            $paymentSettings->payment_method = $validated['payment_method'] ?? null;
            $paymentSettings->payment_account_name = $validated['payment_account_name'] ?? null;
            $paymentSettings->payment_account_identifier = $validated['payment_account_identifier'] ?? null;

            $portfolioLinks = array_map(
                static fn (?string $url): string => trim($url ?? ''),
                $validated['portfolio_links'] ?? []
            );
            $profile['portfolio_links'] = array_values(array_filter(
                $portfolioLinks,
                static fn (string $url): bool => $url !== ''
            ));
        }

        $profile['notifications'] = $validated['notifications'] ?? [];
        $replacedFiles = [];
        $uploadFields = $role === 'artist' ? ['avatar', 'cover_image'] : ['avatar'];

        foreach ($uploadFields as $field) {
            if ($request->hasFile($field)) {
                $oldPath = $profile[$field] ?? null;
                $storedPath = $request->file($field)->store("profiles/{$user->getKey()}", 'public');
                if (! is_string($storedPath)) {
                    throw new RuntimeException("Unable to store the uploaded {$field}.");
                }
                $profile[$field] = $storedPath;

                if (is_string($oldPath) && (
                    str_starts_with($oldPath, "profiles/{$user->getKey()}/")
                    || ($field === 'avatar' && str_starts_with($oldPath, 'avatars/'))
                )) {
                    $replacedFiles[] = $oldPath;
                }
            }
        }

        $user->profile = $profile;
        $user->save();
        $paymentSettings?->save();
        Storage::disk('public')->delete($replacedFiles);

        if ($role === 'artist' && $user->artist_id) {
            $request->session()->put('artist_id', $user->artist_id);
        }

        return redirect()
            ->route($role === 'artist' ? 'artist.profile.edit' : 'client.profile.edit')
            ->with('status', 'Profile updated successfully!');
    }

    public function updateArtistPaymentQr(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless($user->role === 'artist', 403);

        $validated = $request->validate([
            'payment_qr' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $paymentSettings = $user->paymentSettings()->firstOrNew();
        $oldPath = $paymentSettings->payment_qr_path;
        $storedPath = $validated['payment_qr']->store("artist-payments/{$user->getKey()}", 'local');
        if (! is_string($storedPath)) {
            throw new RuntimeException('Unable to store the payment QR code.');
        }

        $paymentSettings->payment_qr_path = $storedPath;
        if (! $paymentSettings->save()) {
            Storage::disk('local')->delete($storedPath);
            throw new RuntimeException('Unable to save the payment QR code settings.');
        }

        if (
            is_string($oldPath)
            && str_starts_with($oldPath, "artist-payments/{$user->getKey()}/")
            && ! Order::where('payment_qr_path', $oldPath)->exists()
        ) {
            Storage::disk('local')->delete($oldPath);
        }

        return redirect()
            ->route('artist.profile.edit')
            ->with('status', 'Payment QR code saved. Buyers will see it during checkout and on their order payment page.');
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
