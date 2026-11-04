
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ArtworkController;
use App\Http\Controllers\ProfileController;
use App\Models\User;
use App\Models\Artwork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('login.submit');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:6,1')
        ->name('register.submit');
});

Route::get('/admin/access-gate', [AuthController::class, 'showAdminLogin'])
    ->name('admin.login');

Route::post('/admin/access-gate', [AuthController::class, 'adminLogin'])
    ->middleware('throttle:6,1')
    ->name('admin.login.submit');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => view('home'))->name('home');


Route::get('/explore', function () {
    $countries = ['Philippines', 'Greece', 'Scotland', 'Japan'];
    $selectedCountry = request()->query('country');

    $artworks = Artwork::with('artist')
        ->where('status', 'approved')
        ->latest()
        ->get();

    return view('explore', [
        'selectedCountry' => in_array($selectedCountry, $countries, true)
            ? $selectedCountry
            : 'all',
        'artworks' => $artworks,
        'favorites' => Session::get('favorites', []),
    ]);
})->name('explore');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('explore'))
        ->name('dashboard');
});



Route::get('/product/{id?}', function ($id = 1) {
    $artwork = config("demo.artworks.$id");

    $status = Session::get(
        "artwork_reviews.$id",
        is_array($artwork) ? $artwork['status'] : null
    );

    abort_unless(
        is_array($artwork) && $status === 'approved',
        404
    );

    return view('product', [
        'id' => $id,
        'artwork' => $artwork,
        'isFavorite' => in_array(
            (string) $id,
            Session::get('favorites', []),
            true
        ),
    ]);
})->name('product.show');

// Database artwork details - approved artworks only
Route::get('/artwork/{artwork}', function (int $artwork) {
    $artwork = Artwork::with('artist')
        ->where('status', 'approved')
        ->findOrFail($artwork);

    $favoriteKey = 'db-' . $artwork->id;

    return view('product', [
        'artwork' => $artwork,
        'isFavorite' => in_array(
            $favoriteKey,
            Session::get('favorites', []),
            true
        ),
    ]);
})->whereNumber('artwork')->name('artwork.show');


Route::match(['HEAD'], '/product/{id?}', function ($id = 1) {
    $artwork = config("demo.artworks.$id");

    $status = Session::get(
        "artwork_reviews.$id",
        is_array($artwork) ? $artwork['status'] : null
    );

    abort_unless(
        is_array($artwork) && $status === 'approved',
        404
    );

    return response()->noContent();
})->name('client.show');


/*
|--------------------------------------------------------------------------
| Client Routes
|--------------------------------------------------------------------------
*/

Route::middleware('role:client')->group(function () {
    Route::get('/client/profile/edit', [ProfileController::class, 'editClient'])
        ->name('client.profile.edit');
    Route::match(['POST', 'PUT'], '/client/profile', [ProfileController::class, 'updateClient'])
        ->name('client.profile.update');

    Route::post('/artworks/{artwork}/favorite', function (int $artwork) {
    $submission = Artwork::where('status', 'approved')
        ->findOrFail($artwork);

    $favorites = Session::get('favorites', []);
    $favoriteKey = 'db-' . $submission->id;

    if (in_array($favoriteKey, $favorites, true)) {
        $favorites = array_values(array_filter(
            $favorites,
            fn ($id) => $id !== $favoriteKey
        ));

        $message = 'Artwork removed from favorites.';
    } else {
        $favorites[] = $favoriteKey;
        $message = 'Artwork added to favorites.';
    }

    Session::put('favorites', $favorites);

    return back()->with('status', $message);
})->whereNumber('artwork')->name('artwork.favorite');

    Route::get('/cart', function () {
        return view('cart', [
            'items' => [
                [
                    'title' => 'Maria Makiling, Guardian of the Mountain',
                    'artist' => 'Maria Makiling Studio',
                    'country' => 'Philippines',
                    'price' => 1250,
                    'artwork_id' => 1,
                ],
                [
                    'title' => 'The Foxfire Promise',
                    'artist' => 'Kaito Renders',
                    'country' => 'Japan',
                    'price' => 1850,
                    'artwork_id' => 4,
                ],
            ],
        ]);
    })->name('cart');


    Route::get('/favorites', function () {
        $favoriteIds = Session::get('favorites', []);
        $reviews = Session::get('artwork_reviews', []);

        $artworks = collect(config('demo.artworks'))
            ->only($favoriteIds)
            ->filter(fn (array $artwork, int|string $id) =>
                ($reviews[(string) $id] ?? $artwork['status']) === 'approved'
            )
            ->all();

        return view('favorites', [
            'artworks' => $artworks,
        ]);
    })->name('favorites');


    Route::post('/favorites/{id}', function (int $id) {
        $artwork = config("demo.artworks.$id");

        $status = Session::get(
            "artwork_reviews.$id",
            is_array($artwork) ? $artwork['status'] : null
        );

        abort_unless(
            is_array($artwork) && $status === 'approved',
            404
        );

        $favorites = Session::get('favorites', []);
        $id = (string) $id;

        if (in_array($id, $favorites, true)) {
            $favorites = array_values(
                array_filter(
                    $favorites,
                    fn ($favoriteId) => $favoriteId !== $id
                )
            );

            $message = 'Artwork removed from favorites.';
        } else {
            $favorites[] = $id;
            $message = 'Artwork added to favorites.';
        }

        Session::put('favorites', $favorites);

        return back()->with('status', $message);
    })->name('favorites.toggle');


    Route::get('/profile', [ProfileController::class, 'editClient'])
        ->name('profile');

    Route::post('/profile', [ProfileController::class, 'updateClient'])
        ->name('profile.update');


    Route::get('/checkout', function () {
        return view('checkout', [
            'items' => [
                [
                    'title' => 'Maria Makiling, Guardian of the Mountain',
                    'quantity' => 1,
                    'price' => 1250,
                ],
                [
                    'title' => 'The Foxfire Promise',
                    'quantity' => 1,
                    'price' => 1850,
                ],
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
            [
                'title' => 'Maria Makiling, Guardian of the Mountain',
                'artist' => 'Maria Makiling Studio',
                'country' => 'Philippines',
                'quantity' => 1,
                'price' => 1250,
                'artwork_id' => 1,
            ],
            [
                'title' => 'The Foxfire Promise',
                'artist' => 'Kaito Renders',
                'country' => 'Japan',
                'quantity' => 1,
                'price' => 1850,
                'artwork_id' => 4,
            ],
        ];

        $transactions[$orderCode] = [
            'order_code' => $orderCode,
            'items' => $items,
            'total' => array_sum(
                array_map(
                    fn ($item) => $item['quantity'] * $item['price'],
                    $items
                )
            ),
            'payment' => $validated['payment'] === 'gcash'
                ? 'GCash (demo)'
                : 'Card (demo)',
            'status' => 'Order placed',
            'placed_at' => now()->toIso8601String(),
            'buyer' => $validated['name'],
        ];

        Session::put('transactions', $transactions);

        return redirect()
            ->route('transactions.show', $orderCode)
            ->with('status', 'Your demo order has been placed. No payment was collected.');
    })->name('checkout.submit');


    Route::get('/transactions', function () {
        return view('transactions.index', [
            'transactions' => Session::get('transactions', []),
        ]);
    })->name('transactions');


    Route::get('/transactions/{order}', function (string $order) {
        $transaction = Session::get("transactions.$order");

        abort_unless(is_array($transaction), 404);

        return view('transactions.show', [
            'transaction' => $transaction,
        ]);
    })->where('order', 'LS-[A-Z0-9-]+')->name('transactions.show');

});


/*
|--------------------------------------------------------------------------
| Artist Routes
|--------------------------------------------------------------------------
*/

Route::middleware('role:artist')
    ->prefix('artist')
    ->name('artist.')
    ->group(function () {

        /*
        | Artist Dashboard
        */

        Route::get('/dashboard', function () {
            $artistId = request()->user()->artist_id;

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
                [
                    'name' => request()->user()->name,
                    'email' => request()->user()->email,
                ],
                request()->user()->profile ?? []
            );

            $artworks = collect(config('demo.artworks'))
                ->where('artist_id', $artistId)
                ->values();

            // Get the artist's actual database submissions.
            $submissions = Artwork::where('user_id', request()->user()->id)
                ->latest()
                ->get();

            return view('artist.dashboard', compact(
                'artist',
                'artworks',
                'submissions'
            ));
        })->name('dashboard');


        /*
        | Artist Inventory - Database CRUD
        */

        Route::get('/inventory', [ArtworkController::class, 'index'])
            ->name('inventory');

        Route::post('/inventory', [ArtworkController::class, 'store'])
            ->name('inventory.submit');

        Route::get('/inventory/{artwork}/edit', [ArtworkController::class, 'edit'])
            ->whereNumber('artwork')
            ->name('inventory.edit');

        Route::put('/inventory/{artwork}', [ArtworkController::class, 'update'])
            ->whereNumber('artwork')
            ->name('inventory.update');

        Route::delete('/inventory/{artwork}', [ArtworkController::class, 'destroy'])
            ->whereNumber('artwork')
            ->name('inventory.destroy');


        /*
        | Artist Profile
        */

        Route::get('/profile/edit', [ProfileController::class, 'editArtist'])
            ->name('profile.edit');

        Route::get('/profile', [ProfileController::class, 'editArtist'])
            ->name('profile');

        Route::match(['POST', 'PUT'], '/profile', [ProfileController::class, 'updateArtist'])
            ->name('profile.update');

});


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware('role:admin')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        | Admin Dashboard
        */

        Route::get('/dashboard', function () {
            $pendingCatalogCount = collect(config('demo.artworks'))
                ->filter(fn (array $artwork, int|string $id) =>
                    (Session::get("artwork_reviews.$id", $artwork['status'])) === 'pending'
                )
                ->count();

            $pendingSubmissionCount = Artwork::where('status', 'pending')->count();

            $users = User::query()
                ->orderBy('id')
                ->get()
                ->map(fn (User $user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'status' => ucfirst($user->status),
                ])
                ->all();

            return view('admin.dashboard', [
                'users' => $users,
                'adminName' => Auth::user()->name,
                'artistCount' => User::where('role', 'artist')->count(),
                'artworkCount' => count(config('demo.artworks')) + Artwork::count(),
                'pendingCount' => $pendingCatalogCount + $pendingSubmissionCount,
                'platformRevenue' => collect(config('demo.artists'))->sum('revenue'),
                'transactions' => Session::get('transactions', []),
            ]);
        })->name('dashboard');


        /*
        | Admin Clearance
        */

        Route::get('/super-vault-access', fn () => view('auth.admin-vault'))
            ->name('vault.gate');

        Route::post('/super-vault-access', [AuthController::class, 'verifyAdminClearance'])
            ->middleware('throttle:5,1')
            ->name('vault.verify');


        /*
        | User Management
        */

        Route::middleware('admin.clearance')->group(function () {

            Route::get('/users', function () {
                $users = User::query()
                    ->orderBy('id')
                    ->get()
                    ->map(fn (User $user) => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'status' => ucfirst($user->status),
                        'joined' => $user->created_at?->format('M d, Y') ?? '—',
                    ])
                    ->all();

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
                    return back()->withErrors([
                        'user' => 'You cannot modify your own administrator account.',
                    ]);
                }

                if ($validated['action'] === 'role') {
                    $target->role = $validated['role'];

                    if ($validated['role'] === 'client') {
                        $target->artist_id = null;
                    } elseif (! $target->artist_id) {
                        $target->artist_id = Str::slug($target->name)
                            . '-' . Str::lower(Str::random(8));
                    }

                    $target->save();

                } elseif ($validated['action'] === 'status') {
                    $target->status = $target->status === 'active'
                        ? 'suspended'
                        : 'active';

                    $target->save();

                } else {
                    $target->delete();
                }

                return back()->with('status', 'User account updated.');
            })->whereNumber('user')->name('users.action');

            /*
            | Artwork Approvals - Database
            */

            Route::get('/approvals', function () {

                $artworks = Artwork::with('artist')
                    ->latest()
                    ->get()
                    ->map(function (Artwork $artwork) {

                        return [
                            'id' => $artwork->id,
                            'artist_id' => $artwork->user_id,
                            'artist' => $artwork->artist?->name ?? 'Unknown Artist',
                            'title' => $artwork->title,
                            'country' => $artwork->country,
                            'description' => $artwork->description,
                            'price' => $artwork->price,
                            'status' => $artwork->status,
                            'submitted_at' => $artwork->created_at?->format('M d, Y'),
                            'file_name' => basename($artwork->image_path),
                        ];

                    })
                    ->values();

                return view('admin.approvals', compact('artworks'));

            })->name('approvals');


        /*
        | Approve or Reject Artwork
        */

            Route::post('/approvals/{artwork}/action', function (Request $request, int $artwork) {

                $validated = $request->validate([
                    'status' => ['required', 'in:approved,rejected'],
                ]);

                $submission = Artwork::findOrFail($artwork);

                $submission->update([
                    'status' => $validated['status'],
                    'reviewed_by' => Auth::id(),
                    'reviewed_at' => now(),
                ]);

                return back()->with(
                    'status',
                    'Artwork ' . $validated['status'] . '.'
                );

            })->whereNumber('artwork')->name('approvals.action');


        /*
        | View / Download Submitted Artwork
        */

            Route::get('/approvals/{artwork}/file', function (int $artwork) {

                $submission = Artwork::findOrFail($artwork);

                abort_unless(
                    $submission->image_path &&
                    Storage::disk('public')->exists($submission->image_path),
                    404
                );

                $downloadName = basename($submission->image_path);

                return Storage::disk('public')->download(
                    $submission->image_path,
                    $downloadName
                );

            })->whereNumber('artwork')->name('approvals.file');

        });
});

require __DIR__.'/auth.php';
