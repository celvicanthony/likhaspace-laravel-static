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

        $artist = array_replace(
            [
                'name' => $user->display_name,
                'first_name' => $user->first_name,
                'middle_name' => $user->middle_name,
                'last_name' => $user->last_name,
                'name_extension' => $user->name_extension,
                'display_name' => $user->display_name,
                'birthdate' => $user->birthdate,
                'gender' => $user->gender,
                'email' => $user->email,
                'country' => 'Philippines',
                'studio' => $user->name,
                'bio' => '',
                'contact' => '',
                'portfolio_links' => [],
                'notifications' => [],
                'payment_method' => $user->payment_method,
                'payment_account_name' => $user->payment_account_name,
                'payment_account_identifier' => $user->payment_account_identifier,
                'payment_qr_configured' => filled($user->payment_qr_path),
            ],
            config("demo.artists.{$user->artist_id}", []),
            $user->profile ?? [],
            [
                'name' => $user->display_name,
                'first_name' => $user->first_name,
                'middle_name' => $user->middle_name,
                'last_name' => $user->last_name,
                'name_extension' => $user->name_extension,
                'display_name' => $user->display_name,
                'birthdate' => $user->birthdate,
                'gender' => $user->gender,
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
            'first_name' => ['required', 'string', 'max:60'],
            'middle_name' => ['nullable', 'string', 'max:60'],
            'last_name' => ['required', 'string', 'max:60'],
            'name_extension' => ['nullable', 'string', 'max:20'],
            'display_name' => ['nullable', 'string', 'max:120'],
            'birthdate' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', Rule::in(['female', 'male', 'non_binary', 'other', 'prefer_not_to_say'])],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->getKey())],
            'contact' => ['nullable', 'string', 'max:40'],
            'current_password' => ['required_with:new_password', 'current_password'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
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
                'payment_qr' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
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
        $user->first_name = $validated['first_name'];
        $user->middle_name = $validated['middle_name'] ?? null;
        $user->last_name = $validated['last_name'];
        $user->name_extension = $validated['name_extension'] ?? null;
        $user->name = collect([
            $validated['first_name'],
            $validated['middle_name'] ?? null,
            $validated['last_name'],
            $validated['name_extension'] ?? null,
        ])->filter()->implode(' ');
        $user->display_name = filled($validated['display_name'] ?? null)
            ? $validated['display_name']
            : trim($validated['first_name'].' '.$validated['last_name']);
        $user->birthdate = $validated['birthdate'] ?? null;
        $user->gender = $validated['gender'] ?? null;
        $user->email = $validated['email'];

        if ($oldEmail !== $user->email) {
            $user->email_verified_at = null;
        }

        if (! empty($validated['new_password'])) {
            $user->password = $validated['new_password'];
        }

        $profileFields = $role === 'artist'
            ? ['country', 'studio', 'bio', 'contact']
            : ['bio', 'contact', 'address', 'city', 'province', 'postal'];

        foreach ($profileFields as $field) {
            $profile[$field] = $validated[$field] ?? '';
        }

        if ($role === 'artist') {
            $user->payment_method = $validated['payment_method'] ?? null;
            $user->payment_account_name = $validated['payment_account_name'] ?? null;
            $user->payment_account_identifier = $validated['payment_account_identifier'] ?? null;

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

        $replacedPaymentQr = null;
        if ($role === 'artist' && $request->hasFile('payment_qr')) {
            $oldPath = $user->payment_qr_path;
            $storedPath = $request->file('payment_qr')->store("artist-payments/{$user->getKey()}", 'local');
            if (! is_string($storedPath)) {
                throw new RuntimeException('Unable to store the payment QR code.');
            }
            $user->payment_qr_path = $storedPath;

            if (is_string($oldPath) && str_starts_with($oldPath, "artist-payments/{$user->getKey()}/")) {
                $replacedPaymentQr = $oldPath;
            }
        }

        $user->profile = $profile;
        $user->save();
        if ($replacedPaymentQr && ! Order::where('payment_qr_path', $replacedPaymentQr)->exists()) {
            Storage::disk('local')->delete($replacedPaymentQr);
        }
        Storage::disk('public')->delete($replacedFiles);

        if ($role === 'artist' && $user->artist_id) {
            $request->session()->put('artist_id', $user->artist_id);
        }

        return redirect()
            ->route($role === 'artist' ? 'artist.profile.edit' : 'client.profile.edit')
            ->with('status', 'Profile updated successfully!');
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
