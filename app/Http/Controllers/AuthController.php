<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route($this->destination(Auth::user()));
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! in_array($user->role, ['client', 'artist'], true) || $user->status !== 'active'
            || ! Auth::attempt([
                'email' => $credentials['email'],
                'password' => $credentials['password'],
                'role' => $user->role,
                'status' => 'active',
            ], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'These credentials could not be verified. Admin accounts must use the dedicated access portal.',
            ]);
        }

        $this->prepareAuthenticatedSession($request);

        return redirect()->route($this->destination(Auth::user()));
    }

    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route($this->destination(Auth::user()));
        }

        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['client', 'artist'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $artistId = $validated['role'] === 'artist'
            ? Str::slug($validated['name']).'-'.Str::lower(Str::random(8))
            : null;

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'artist_id' => $artistId,
            'status' => 'active',
            'password' => $validated['password'],
            'profile' => [],
        ]);

        Auth::login($user);
        $this->prepareAuthenticatedSession($request);

        return redirect()->route($this->destination($user));
    }

    public function showAdminLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'admin' && Auth::user()->status === 'active') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('explore');
        }

        return view('auth.admin-login');
    }

    public function adminLogin(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials + ['role' => 'admin', 'status' => 'active'], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Administrative credentials could not be verified.',
            ]);
        }

        $this->prepareAuthenticatedSession($request);

        return redirect()->route('admin.dashboard');
    }

    public function verifyAdminClearance(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pin' => ['required', 'string', 'max:128'],
        ]);
        $expectedPin = (string) config('auth.admin_clearance_pin', '');

        if ($expectedPin === '' || ! hash_equals($expectedPin, $validated['pin'])) {
            throw ValidationException::withMessages([
                'pin' => 'The clearance PIN is not valid.',
            ]);
        }

        $request->session()->put('admin_clearance_at', now()->timestamp);

        return redirect()->route('admin.users')->with('status', 'Vault clearance granted for 30 minutes.');
    }

    public function updateClientProfile(Request $request): RedirectResponse
    {
        return $this->saveProfile($request, false);
    }

    public function updateArtistProfile(Request $request): RedirectResponse
    {
        return $this->saveProfile($request, true);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function saveProfile(Request $request, bool $artist): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $profile = $user->profile ?? [];
        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'current_password' => ['required_with:new_password', 'current_password'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'notifications' => ['nullable', 'array'],
            'notifications.*' => ['boolean'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];

        if ($artist) {
            $rules += [
                'country' => ['required', Rule::in(['Philippines', 'Greece', 'Scotland', 'Japan'])],
                'studio' => ['required', 'string', 'max:120'],
                'bio' => ['required', 'string', 'max:500'],
            ];
        } else {
            $rules += [
                'bio' => ['nullable', 'string', 'max:400'],
                'contact' => ['nullable', 'string', 'max:40'],
                'address' => ['nullable', 'string', 'max:255'],
                'city' => ['nullable', 'string', 'max:120'],
                'province' => ['nullable', 'string', 'max:120'],
                'postal' => ['nullable', 'string', 'max:20'],
            ];
        }

        $validated = $request->validate($rules);
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (! empty($validated['new_password'])) {
            $user->password = $validated['new_password'];
        }

        if ($request->hasFile('avatar')) {
            $oldAvatar = $profile['avatar'] ?? null;
            $profile['avatar'] = $request->file('avatar')->store('avatars', 'public');
            if (is_string($oldAvatar) && str_starts_with($oldAvatar, 'avatars/')) {
                Storage::disk('public')->delete($oldAvatar);
            }
        }

        $profileFields = $artist
            ? ['country', 'studio', 'bio']
            : ['bio', 'contact', 'address', 'city', 'province', 'postal'];
        foreach ($profileFields as $field) {
            $profile[$field] = $validated[$field] ?? '';
        }
        $profile['notifications'] = $validated['notifications'] ?? [];
        $user->profile = $profile;
        $user->save();

        if ($user->artist_id) {
            $request->session()->put('artist_id', $user->artist_id);
        }

        return redirect()->route($artist ? 'artist.profile' : 'profile')
            ->with('status', 'Your profile has been saved.');
    }

    private function prepareAuthenticatedSession(Request $request): void
    {
        $user = Auth::user();
        $request->session()->regenerate();
        $request->session()->forget('admin_clearance_at');

        if ($user->role === 'artist' && $user->artist_id) {
            $request->session()->put('artist_id', $user->artist_id);
        } else {
            $request->session()->forget('artist_id');
        }
    }

    private function destination(?User $user): string
    {
        return match ($user?->role) {
            'admin' => 'admin.dashboard',
            'artist' => 'artist.dashboard',
            default => 'explore',
        };
    }
}
