
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ArtworkController;
use App\Http\Controllers\ProfileController;
use App\Models\User;
use App\Models\Artwork;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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

    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

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
    $query = Artwork::with('artist')
        ->where('status', 'approved');

    if (request()->filled('search')) {
        $search = request('search');

        $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('country', 'like', "%{$search}%")
                ->orWhereHas('artist', function ($artistQuery) use ($search) {
                    $artistQuery->where('name', 'like', "%{$search}%");
                });
        });
    }

    $countries = ['Philippines', 'Greece', 'Scotland', 'Japan'];
    $selectedCountry = request('country', 'all');

    if (in_array($selectedCountry, $countries, true)) {
        $query->where('country', $selectedCountry);
    } else {
        $selectedCountry = 'all';
    }

    $sort = request('sort', 'newest');

    switch ($sort) {
        case 'title':
            $query->orderBy('title', 'asc');
            break;

        case 'price_low':
            $query->orderBy('price', 'asc');
            break;

        case 'price_high':
            $query->orderBy('price', 'desc');
            break;

        default:
            $query->latest();
            $sort = 'newest';
            break;
    }

    $artworks = $query->paginate(9)->withQueryString();

    return view('explore', [
        'artworks' => $artworks,
        'countries' => $countries,
        'selectedCountry' => $selectedCountry,
        'search' => request('search', ''),
        'sort' => $sort,
        'favorites' => Session::get('favorites', []),
    ]);
})->name('explore');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('explore'))
        ->name('dashboard');
});


/*
|--------------------------------------------------------------------------
| Demo Product Details
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| Database Artwork Details
|--------------------------------------------------------------------------
*/

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

    /*
    | Client Profile Routes
    */

    Route::get('/client/profile/edit', [ProfileController::class, 'editClient'])
        ->name('client.profile.edit');

    Route::match(['POST', 'PUT'], '/client/profile', [ProfileController::class, 'updateClient'])
        ->name('client.profile.update');


    /*
    | Database Artwork Favorites
    */

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


    /*
    |--------------------------------------------------------------------------
    | Shopping Cart
    |--------------------------------------------------------------------------
    */

    Route::get('/cart', function () {
        $cart = Session::get('cart', []);

        $artworks = Artwork::with('artist')
            ->where('status', 'approved')
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        $items = [];
        $validCart = [];

        foreach ($cart as $artworkId => $quantity) {
            $artwork = $artworks->get($artworkId);

            if (! $artwork) {
                continue;
            }

            $quantity = max(1, min(99, (int) $quantity));

            $validCart[$artworkId] = $quantity;

            $items[] = [
                'artwork' => $artwork,
                'quantity' => $quantity,
                'price' => (float) $artwork->price,
                'line_total' => (float) $artwork->price * $quantity,
            ];
        }

        Session::put('cart', $validCart);

        $subtotal = collect($items)->sum('line_total');

        return view('cart', compact('items', 'subtotal'));
    })->name('cart');


    Route::post('/cart/{artwork}', function (Request $request, int $artwork) {
        $validated = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $submission = Artwork::where('status', 'approved')
            ->findOrFail($artwork);

        $cart = Session::get('cart', []);

        $quantity = (int) ($validated['quantity'] ?? 1);
        $currentQuantity = (int) ($cart[$submission->id] ?? 0);

        $cart[$submission->id] = min(99, $currentQuantity + $quantity);

        Session::put('cart', $cart);

        return redirect()
            ->route('cart')
            ->with('status', 'Artwork added to your cart.');
    })->whereNumber('artwork')->name('cart.add');


    Route::patch('/cart/{artwork}', function (Request $request, int $artwork) {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $submission = Artwork::where('status', 'approved')
            ->findOrFail($artwork);

        $cart = Session::get('cart', []);

        abort_unless(isset($cart[$submission->id]), 404);

        $cart[$submission->id] = (int) $validated['quantity'];

        Session::put('cart', $cart);

        return redirect()
            ->route('cart')
            ->with('status', 'Cart quantity updated.');
    })->whereNumber('artwork')->name('cart.update');


    Route::delete('/cart/{artwork}', function (int $artwork) {
        $cart = Session::get('cart', []);

        unset($cart[$artwork]);

        Session::put('cart', $cart);

        return redirect()
            ->route('cart')
            ->with('status', 'Artwork removed from your cart.');
    })->whereNumber('artwork')->name('cart.remove');


    /*
    |--------------------------------------------------------------------------
    | Demo Favorites
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | Client Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'editClient'])
        ->name('profile');

    Route::post('/profile', [ProfileController::class, 'updateClient'])
        ->name('profile.update');


    /*
    |--------------------------------------------------------------------------
    | Checkout - Database Orders
    |--------------------------------------------------------------------------
    */

    Route::get('/checkout', function () {
        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart')
                ->withErrors(['cart' => 'Your cart is empty.']);
        }

        $artworks = Artwork::with('artist')
            ->where('status', 'approved')
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        $items = [];
        $validCart = [];

        foreach ($cart as $artworkId => $quantity) {
            $artwork = $artworks->get($artworkId);

            if (! $artwork) {
                continue;
            }

            $quantity = max(1, min(99, (int) $quantity));

            $validCart[$artworkId] = $quantity;

            $price = (float) $artwork->price;

            $items[] = [
                'artwork' => $artwork,
                'title' => $artwork->title,
                'artist' => $artwork->artist?->name ?? 'Unknown Artist',
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => round($price * $quantity, 2),
            ];
        }

        Session::put('cart', $validCart);

        if (empty($items)) {
            return redirect()->route('cart')
                ->withErrors([
                    'cart' => 'No approved artworks are available in your cart.',
                ]);
        }

        $total = round(collect($items)->sum('subtotal'), 2);

        return view('checkout', [
            'items' => $items,
            'total' => $total,
            'buyer' => request()->user(),
        ]);
    })->name('checkout');


    Route::post('/checkout', function (Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'payment' => ['required', 'in:card,gcash'],
        ]);

        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart')
                ->withErrors(['cart' => 'Your cart is empty.']);
        }

        $order = DB::transaction(function () use ($cart, $validated) {

            $artworks = Artwork::with('artist')
                ->where('status', 'approved')
                ->whereIn('id', array_keys($cart))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($artworks->count() !== count($cart)) {
                throw ValidationException::withMessages([
                    'cart' => 'One or more artworks are no longer available. Please review your cart.',
                ]);
            }

            $orderItems = [];
            $total = 0;

            foreach ($cart as $artworkId => $quantity) {
                $artwork = $artworks->get($artworkId);
                $quantity = filter_var($quantity, FILTER_VALIDATE_INT);

                if (
                    ! $artwork ||
                    $quantity === false ||
                    $quantity < 1 ||
                    $quantity > 99
                ) {
                    throw ValidationException::withMessages([
                        'cart' => 'An artwork or quantity in your cart is invalid. Please review your cart.',
                    ]);
                }

                $unitPrice = (float) $artwork->price;
                $lineTotal = round($unitPrice * $quantity, 2);

                $total += $lineTotal;

                $orderItems[] = [
                    'artwork' => $artwork,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $lineTotal,
                ];
            }

            $total = round($total, 2);

            $orderCode = 'LS-' . now()->format('Ymd') . '-' . Str::upper(Str::random(8));

            $order = Order::create([
                'order_code' => $orderCode,
                'user_id' => request()->user()->id,
                'buyer_name' => $validated['name'],
                'buyer_email' => $validated['email'],
                'shipping_address' => $validated['address'],
                'payment_method' => $validated['payment'] === 'gcash'
                    ? 'GCash (demo)'
                    : 'Credit/debit card (demo)',
                'payment_status' => 'unpaid',
                'status' => 'Order placed',
                'total_amount' => $total,
            ]);

            foreach ($orderItems as $item) {
                $artwork = $item['artwork'];

                $order->items()->create([
                    'artwork_id' => $artwork->id,
                    'artwork_title' => $artwork->title,
                    'artist_name' => $artwork->artist?->name ?? 'Unknown Artist',
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            return $order;
        });

        Session::forget('cart');

        return redirect()
            ->route('transactions.show', $order->order_code)
            ->with('status', 'Your order has been saved successfully. No payment was collected.');
    })->name('checkout.submit');


    /*
    |--------------------------------------------------------------------------
    | Customer Transaction History
    |--------------------------------------------------------------------------
    */

    Route::get('/transactions', function () {
        $orders = Order::with('items')
            ->where('user_id', request()->user()->id)
            ->latest()
            ->paginate(10);

        return view('transactions.index', compact('orders'));
    })->name('transactions');


    Route::get('/transactions/{order}', function (string $order) {
        $order = Order::with('items')
            ->where('user_id', request()->user()->id)
            ->where('order_code', $order)
            ->firstOrFail();

        return view('transactions.show', [
            'order' => $order,
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
                    Session::get("artwork_reviews.$id", $artwork['status']) === 'pending'
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
                'platformRevenue' => Order::where('payment_status', 'paid')
                    ->sum('total_amount'),
                'transactions' => Order::with(['items', 'customer'])
                    ->latest()
                    ->take(5)
                    ->get(),
                'orderCount' => Order::count(),
            ]);
        })->name('dashboard');


        /*
        | Admin Transaction Management
        */

        Route::get('/transactions', function () {
            $orders = Order::with(['items', 'customer'])
                ->latest()
                ->paginate(15);

            return view('admin.transactions', compact('orders'));
        })->name('transactions');


        /*
        | Admin Clearance
        */

        Route::get('/super-vault-access', fn () => view('auth.admin-vault'))
            ->name('vault.gate');

        Route::post('/super-vault-access', [AuthController::class, 'verifyAdminClearance'])
            ->middleware('throttle:5,1')
            ->name('vault.verify');


        /*
        | User Management and Artwork Approvals
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
