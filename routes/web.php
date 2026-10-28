<?php

use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1')->name('register.submit');
});

Route::get('/admin/access-gate', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/access-gate', [AuthController::class, 'adminLogin'])->middleware('throttle:6,1')->name('admin.login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/', fn () => view('home'))->name('home');

Route::get('/explore', function () {
    $countries = ['Philippines', 'Greece', 'Scotland', 'Japan'];
    $selectedCountry = request()->query('country');
    $reviews = Session::get('artwork_reviews', []);
    $artworks = collect(config('demo.artworks'))
        ->filter(fn (array $artwork, int|string $id) => ($reviews[(string) $id] ?? $artwork['status']) === 'approved')
        ->all();

    return view('explore', [
        'selectedCountry' => in_array($selectedCountry, $countries, true) ? $selectedCountry : 'all',
        'artworks' => $artworks,
        'favorites' => Session::get('favorites', []),
    ]);
})->name('explore');

Route::get('/product/{id?}', function ($id = 1) {
    $artwork = config("demo.artworks.$id");
    $status = Session::get("artwork_reviews.$id", is_array($artwork) ? $artwork['status'] : null);
    abort_unless(is_array($artwork) && $status === 'approved', 404);

    return view('product', [
        'id' => $id,
        'artwork' => $artwork,
        'isFavorite' => in_array((string) $id, Session::get('favorites', []), true),
    ]);
})->name('product.show');

Route::match(['HEAD'], '/product/{id?}', function ($id = 1) {
    $artwork = config("demo.artworks.$id");
    $status = Session::get("artwork_reviews.$id", is_array($artwork) ? $artwork['status'] : null);
    abort_unless(is_array($artwork) && $status === 'approved', 404);

    return response()->noContent();
})->name('client.show');

Route::middleware('role:client')->group(function () {
    Route::get('/cart', function () {
        return view('cart', [
            'items' => [
                ['title' => 'Maria Makiling, Guardian of the Mountain', 'artist' => 'Maria Makiling Studio', 'country' => 'Philippines', 'price' => 1250, 'artwork_id' => 1],
                ['title' => 'The Foxfire Promise', 'artist' => 'Kaito Renders', 'country' => 'Japan', 'price' => 1850, 'artwork_id' => 4],
            ],
        ]);
    })->name('cart');

    Route::get('/favorites', function () {
        $favoriteIds = Session::get('favorites', []);
        $reviews = Session::get('artwork_reviews', []);
        $artworks = collect(config('demo.artworks'))
            ->only($favoriteIds)
            ->filter(fn (array $artwork, int|string $id) => ($reviews[(string) $id] ?? $artwork['status']) === 'approved')
            ->all();

        return view('favorites', ['artworks' => $artworks]);
    })->name('favorites');

    Route::post('/favorites/{id}', function (int $id) {
        $artwork = config("demo.artworks.$id");
        $status = Session::get("artwork_reviews.$id", is_array($artwork) ? $artwork['status'] : null);
        abort_unless(is_array($artwork) && $status === 'approved', 404);

        $favorites = Session::get('favorites', []);
        $id = (string) $id;

        if (in_array($id, $favorites, true)) {
            $favorites = array_values(array_filter($favorites, fn ($favoriteId) => $favoriteId !== $id));
            $message = 'Artwork removed from favorites.';
        } else {
            $favorites[] = $id;
            $message = 'Artwork added to favorites.';
        }

        Session::put('favorites', $favorites);

        return back()->with('status', $message);
    })->name('favorites.toggle');

    Route::get('/profile', function () {
        return view('profile', [
            'user' => request()->user(),
            'profile' => request()->user()->profile ?? [],
        ]);
    })->name('profile');

    Route::post('/profile', [AuthController::class, 'updateClientProfile'])->name('profile.update');

    Route::get('/checkout', function () {
        return view('checkout', [
            'items' => [
                ['title' => 'Maria Makiling, Guardian of the Mountain', 'quantity' => 1, 'price' => 1250],
                ['title' => 'The Foxfire Promise', 'quantity' => 1, 'price' => 1850],
            ],
        ]);
    })->name('checkout');

    Route::post('/checkout', function (Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'payment' => ['required', 'in:card,gcash'],
        ]);

        $transactions = Session::get('transactions', []);
        $orderCode = 'LS-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        $items = [
            ['title' => 'Maria Makiling, Guardian of the Mountain', 'artist' => 'Maria Makiling Studio', 'country' => 'Philippines', 'quantity' => 1, 'price' => 1250, 'artwork_id' => 1],
            ['title' => 'The Foxfire Promise', 'artist' => 'Kaito Renders', 'country' => 'Japan', 'quantity' => 1, 'price' => 1850, 'artwork_id' => 4],
        ];

        $transactions[$orderCode] = [
            'order_code' => $orderCode,
            'items' => $items,
            'total' => array_sum(array_map(fn ($item) => $item['quantity'] * $item['price'], $items)),
            'payment' => $validated['payment'] === 'gcash' ? 'GCash (demo)' : 'Card (demo)',
            'status' => 'Order placed',
            'placed_at' => now()->toIso8601String(),
            'buyer' => $validated['name'],
        ];
        Session::put('transactions', $transactions);

        return redirect()->route('transactions.show', $orderCode)
            ->with('status', 'Your demo order has been placed. No payment was collected.');
    })->name('checkout.submit');

    Route::get('/transactions', function () {
        return view('transactions.index', ['transactions' => Session::get('transactions', [])]);
    })->name('transactions');

    Route::get('/transactions/{order}', function (string $order) {
        $transaction = Session::get("transactions.$order");
        abort_unless(is_array($transaction), 404);

        return view('transactions.show', ['transaction' => $transaction]);
    })->where('order', 'LS-[A-Z0-9-]+')->name('transactions.show');
});

Route::middleware('role:artist')->prefix('artist')->name('artist.')->group(function () {
    Route::get('/dashboard', function () {
        $artistId = Session::get('artist_id');
        $artist = array_replace(
            [
                'name' => request()->user()->name,
                'email' => request()->user()->email,
                'country' => 'Philippines',
                'studio' => request()->user()->name,
                'bio' => '',
                'sales' => 0,
                'revenue' => 0,
            ],
            config("demo.artists.$artistId", []),
            ['name' => request()->user()->name, 'email' => request()->user()->email],
            request()->user()->profile ?? []
        );
        $artworks = collect(config('demo.artworks'))->where('artist_id', $artistId)->values();
        $submissions = collect(Session::get('artist_submissions', []))
            ->where('artist_id', $artistId)
            ->values();

        return view('artist.dashboard', compact('artist', 'artworks', 'submissions'));
    })->name('dashboard');

    Route::get('/inventory', function () {
        $artistId = Session::get('artist_id');
        $reviews = Session::get('artwork_reviews', []);
        $artworks = collect(config('demo.artworks'))
            ->where('artist_id', $artistId)
            ->map(function (array $artwork, int|string $id) use ($reviews) {
                $artwork['id'] = (string) $id;
                $artwork['status'] = $reviews[(string) $id] ?? $artwork['status'];

                return $artwork;
            })
            ->values();
        $submissions = collect(Session::get('artist_submissions', []))
            ->where('artist_id', $artistId)
            ->values();

        return view('artist.inventory', compact('artistId', 'artworks', 'submissions'));
    })->name('inventory');

    Route::post('/inventory', function (Request $request) {
        $artistId = Session::get('artist_id');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'country' => ['required', 'in:Philippines,Greece,Scotland,Japan'],
            'price' => ['required', 'numeric', 'min:1', 'max:1000000'],
            'artwork_file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $submissions = Session::get('artist_submissions', []);
        $submissionId = 'SUB-' . Str::upper(Str::random(8));
        $uploadedFile = $request->file('artwork_file');
        $submissions[$submissionId] = [
            'id' => $submissionId,
            'artist_id' => $artistId,
            'artist' => $request->user()->name,
            'title' => $validated['title'],
            'country' => $validated['country'],
            'price' => (float) $validated['price'],
            'file_name' => $uploadedFile->getClientOriginalName(),
            'storage_path' => $uploadedFile->store('artist-submissions'),
            'status' => 'pending',
            'submitted_at' => now()->toDateString(),
        ];
        Session::put('artist_submissions', $submissions);

        return redirect()->route('artist.inventory')->with('status', 'Artwork submitted to the platform admin for review.');
    })->name('inventory.submit');

    Route::post('/inventory/{submission}/remove', function (string $submission) {
        $artistId = Session::get('artist_id');
        $submissions = Session::get('artist_submissions', []);
        $submissionData = $submissions[$submission] ?? null;
        abort_unless(is_array($submissionData) && $submissionData['artist_id'] === $artistId, 404);
        abort_unless(str_starts_with($submissionData['storage_path'], 'artist-submissions/'), 404);
        abort_unless(Storage::exists($submissionData['storage_path']), 404);
        abort_unless(Storage::delete($submissionData['storage_path']), 500, 'The artwork file could not be removed.');
        unset($submissions[$submission]);
        Session::put('artist_submissions', $submissions);

        return redirect()->route('artist.inventory')->with('status', 'Your submission has been withdrawn.');
    })->where('submission', 'SUB-[A-Z0-9]+')->name('inventory.remove');

    Route::get('/profile', function () {
        $artistId = Session::get('artist_id');
        $user = request()->user();
        $artist = array_replace(
            [
                'name' => $user->name,
                'email' => $user->email,
                'country' => 'Philippines',
                'studio' => $user->name,
                'bio' => '',
            ],
            config("demo.artists.$artistId", []),
            ['name' => $user->name, 'email' => $user->email],
            $user->profile ?? []
        );

        return view('artist.profile', compact('artist'));
    })->name('profile');

    Route::post('/profile', [AuthController::class, 'updateArtistProfile'])->name('profile.update');
});

Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        $reviews = Session::get('artwork_reviews', []);
        $pendingCatalogCount = collect(config('demo.artworks'))
            ->filter(fn (array $artwork, int|string $id) => ($reviews[(string) $id] ?? $artwork['status']) === 'pending')
            ->count();
        $pendingSubmissionCount = collect(Session::get('artist_submissions', []))
            ->filter(fn (array $submission, string $id) => ($reviews[$id] ?? $submission['status']) === 'pending')
            ->count();

        $users = User::query()->orderBy('id')->get()->map(fn (User $user) => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'status' => ucfirst($user->status),
        ])->all();

        return view('admin.dashboard', [
            'users' => $users,
            'adminName' => Auth::user()->name,
            'artistCount' => User::where('role', 'artist')->count(),
            'artworkCount' => count(config('demo.artworks')) + count(Session::get('artist_submissions', [])),
            'pendingCount' => $pendingCatalogCount + $pendingSubmissionCount,
            'platformRevenue' => collect(config('demo.artists'))->sum('revenue'),
            'transactions' => Session::get('transactions', []),
        ]);
    })->name('dashboard');

    Route::get('/super-vault-access', fn () => view('auth.admin-vault'))->name('vault.gate');
    Route::post('/super-vault-access', [AuthController::class, 'verifyAdminClearance'])
        ->middleware('throttle:5,1')
        ->name('vault.verify');

    Route::middleware('admin.clearance')->group(function () {
    Route::get('/users', function () {
        $users = User::query()->orderBy('id')->get()->map(fn (User $user) => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'status' => ucfirst($user->status),
            'joined' => $user->created_at?->format('M d, Y') ?? '—',
        ])->all();

        return view('admin.users', [
            'users' => $users,
        ]);
    })->name('users');

    Route::post('/users/{user}/action', function (Request $request, int $user) {
        $target = User::findOrFail($user);
        $validated = $request->validate([
            'action' => ['required', 'in:role,status,delete'],
            'role' => ['required_if:action,role', 'nullable', 'in:client,artist'],
        ]);

        if ($target->is(Auth::user())) {
            return back()->withErrors(['user' => 'You cannot modify your own administrator account.']);
        }

        if ($validated['action'] === 'role') {
            $target->role = $validated['role'];
            if ($validated['role'] === 'client') {
                $target->artist_id = null;
            } elseif (! $target->artist_id) {
                $target->artist_id = Str::slug($target->name).'-'.Str::lower(Str::random(8));
            }
            $target->save();
        } elseif ($validated['action'] === 'status') {
            $target->status = $target->status === 'active' ? 'suspended' : 'active';
            $target->save();
        } else {
            $target->delete();
        }

        return back()->with('status', 'User account updated.');
    })->whereNumber('user')->name('users.action');
    });

    Route::get('/approvals', function () {
        $reviews = Session::get('artwork_reviews', []);
        $artworks = collect(config('demo.artworks'))
            ->map(function (array $artwork, int|string $id) use ($reviews) {
                $artwork['id'] = (string) $id;
                $artwork['status'] = $reviews[(string) $id] ?? $artwork['status'];
                $artwork['submitted_at'] = 'Oct 18, 2026';

                return $artwork;
            })
            ->concat(collect(Session::get('artist_submissions', []))->map(function (array $submission) use ($reviews) {
                $submission['status'] = $reviews[$submission['id']] ?? $submission['status'];

                return $submission;
            }))
            ->values();

        return view('admin.approvals', compact('artworks'));
    })->name('approvals');

    Route::post('/approvals/{artwork}/action', function (Request $request, string $artwork) {
        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
        ]);
        $isCatalogArtwork = isset(config('demo.artworks')[$artwork]);
        $submissions = Session::get('artist_submissions', []);
        abort_unless($isCatalogArtwork || isset($submissions[$artwork]), 404);

        $reviews = Session::get('artwork_reviews', []);
        $reviews[$artwork] = $validated['status'];
        Session::put('artwork_reviews', $reviews);

        if (isset($submissions[$artwork])) {
            $submissions[$artwork]['status'] = $validated['status'];
            Session::put('artist_submissions', $submissions);
        }

        return back()->with('status', 'Artwork ' . $validated['status'] . '.');
    })->name('approvals.action');

    Route::get('/approvals/{submission}/file', function (string $submission) {
        $submissionData = Session::get("artist_submissions.$submission");
        abort_unless(is_array($submissionData) && isset($submissionData['storage_path']), 404);
        abort_unless(Storage::exists($submissionData['storage_path']), 404);
        $downloadName = preg_replace('/[^A-Za-z0-9._-]/', '_', $submissionData['file_name']);

        return Storage::download($submissionData['storage_path'], $downloadName);
    })->where('submission', 'SUB-[A-Z0-9]+')->name('approvals.file');
});
