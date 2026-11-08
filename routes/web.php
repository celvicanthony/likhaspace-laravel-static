
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

Route::get('/', function () {
    $creators = User::where('role', 'artist')
        ->where('status', 'active')
        ->orderBy('name')
        ->take(4)
        ->get();

    return view('home', compact('creators'));
})->name('home');

Route::get('/artists/{artist}', function (int $artist) {
    $artist = User::where('role', 'artist')
        ->where('status', 'active')
        ->findOrFail($artist);

    $profile = array_replace(
        config("demo.artists.{$artist->artist_id}", []),
        $artist->profile ?? [],
        ['name' => $artist->display_name]
    );

    $artworks = Artwork::where('user_id', $artist->id)
        ->where('status', 'approved')
        ->withAvg('reviews', 'rating')
        ->withCount('reviews')
        ->latest()
        ->paginate(12, ['*'], 'artworks_page')
        ->withQueryString();

    $demoArtworks = collect(config('demo.artworks'))
        ->filter(fn (array $artwork) => $artwork['artist_id'] === $artist->artist_id && $artwork['status'] === 'approved')
        ->map(fn (array $artwork, int|string $id) => ['id' => $id] + $artwork);
    $artistRating = \App\Models\ArtworkReview::whereHas('artwork', fn ($query) => $query->where('user_id', $artist->id))->avg('rating');
    $artistReviewCount = \App\Models\ArtworkReview::whereHas('artwork', fn ($query) => $query->where('user_id', $artist->id))->count();

    return view('artists.show', compact('artist', 'profile', 'artworks', 'demoArtworks', 'artistRating', 'artistReviewCount'));
})->whereNumber('artist')->name('artists.show');

Route::get('/explore', function () {
    $query = Artwork::with('artist')
        ->withAvg('reviews', 'rating')
        ->withCount('reviews')
        ->withSum([
            'orderItems as paid_sales_count' => fn ($items) => $items->whereHas(
                'order',
                fn ($orders) => $orders->where('payment_status', 'paid')
            ),
        ], 'quantity')
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
        ->withAvg('reviews', 'rating')
        ->withCount('reviews')
        ->withSum([
            'orderItems as paid_sales_count' => fn ($items) => $items->whereHas(
                'order',
                fn ($orders) => $orders->where('payment_status', 'paid')
            ),
        ], 'quantity')
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
        $artists = collect();
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

            if ($artwork->artist) {
                $artists->put($artwork->artist->id, [
                    'name' => $artwork->artist->display_name,
                    'ready' => filled($artwork->artist->payment_method)
                        && filled($artwork->artist->payment_account_name)
                        && filled($artwork->artist->payment_account_identifier),
                ]);
            }
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
            'artists' => $artists,
            'paymentReady' => $artists->isNotEmpty() && $artists->every(fn (array $artist) => $artist['ready']),
        ]);
    })->name('checkout');


    Route::post('/checkout', function (Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
        ]);
        $validated['name'] = \Illuminate\Support\Str::title(
            \Illuminate\Support\Str::squish($validated['name'])
        );

        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart')
                ->withErrors(['cart' => 'Your cart is empty.']);
        }

        DB::transaction(function () use ($cart, $validated) {

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
                $orderItems[] = [
                    'artwork' => $artwork,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => round($unitPrice * $quantity, 2),
                ];
            }

            foreach (collect($orderItems)->groupBy(fn (array $item) => $item['artwork']->user_id) as $sellerId => $sellerItems) {
                $seller = $sellerItems->first()['artwork']->artist;
                if (
                    ! $seller || $seller->role !== 'artist'
                    || blank($seller->payment_method)
                    || blank($seller->payment_account_name)
                    || blank($seller->payment_account_identifier)
                ) {
                    throw ValidationException::withMessages([
                        'cart' => 'An artist has not completed their private payment setup. Please try again later.',
                    ]);
                }

                $total = round($sellerItems->sum('subtotal'), 2);
                $order = Order::create([
                    'order_code' => 'LS-' . now()->format('Ymd') . '-' . Str::upper(Str::random(8)),
                    'user_id' => request()->user()->id,
                    'seller_id' => $sellerId,
                    'buyer_name' => $validated['name'],
                    'buyer_email' => $validated['email'],
                    'shipping_address' => 'Digital delivery',
                    'payment_method' => strtoupper($seller->payment_method),
                    'payment_recipient_name' => $seller->payment_account_name,
                    'payment_recipient_identifier' => $seller->payment_account_identifier,
                    'payment_qr_path' => $seller->payment_qr_path,
                    'payment_status' => 'unpaid',
                    'status' => 'Awaiting payment',
                    'total_amount' => $total,
                ]);

                foreach ($sellerItems as $item) {
                    $artwork = $item['artwork'];
                    $order->items()->create([
                        'artwork_id' => $artwork->id,
                        'artwork_title' => $artwork->title,
                        'artist_name' => $seller->display_name,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'subtotal' => $item['subtotal'],
                    ]);
                }
            }
        });

        Session::forget('cart');

        return redirect()
            ->route('transactions')
            ->with('status', 'Your artist-specific orders are ready. Pay each artist separately and submit proof for review.');
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
        $order = Order::with(['items.review', 'seller'])
            ->where('user_id', request()->user()->id)
            ->where('order_code', $order)
            ->firstOrFail();

        return view('transactions.show', [
            'order' => $order,
        ]);
    })->where('order', 'LS-[A-Z0-9-]+')->name('transactions.show');

    Route::post('/transactions/{order}/items/{item}/review', function (
        Request $request,
        string $order,
        int $item
    ) {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);
        $purchase = Order::where('user_id', $request->user()->id)
            ->where('order_code', $order)
            ->firstOrFail();
        abort_unless(
            $purchase->payment_status === 'paid' && $purchase->status === 'Ready for download',
            403
        );
        $line = $purchase->items()->with('review')->findOrFail($item);
        abort_unless($line->delivery_path && $line->delivered_at && $line->downloaded_at, 403);
        abort_unless($line->artwork_id, 404);
        abort_unless(! $line->review, 409, 'This artwork has already been reviewed.');

        $line->review()->create([
            'buyer_id' => $request->user()->id,
            'artwork_id' => $line->artwork_id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return back()->with('status', 'Thank you. Your verified artwork review is now visible.');
    })->where('order', 'LS-[A-Z0-9-]+')->whereNumber('item')->name('transactions.items.review');

    Route::post('/transactions/{order}/payment', function (Request $request, string $order) {
        $validated = $request->validate([
            'payment_reference' => ['nullable', 'string', 'max:120'],
            'payment_proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
        $purchase = Order::where('user_id', $request->user()->id)
            ->where('order_code', $order)->firstOrFail();
        abort_unless(in_array($purchase->payment_status, ['unpaid', 'rejected'], true), 409);

        $path = $request->file('payment_proof')->store("payment-proofs/{$purchase->order_code}", 'local');
        if (! is_string($path)) {
            throw new \RuntimeException('Unable to store the payment proof.');
        }
        if ($purchase->payment_proof_path) {
            Storage::disk('local')->delete($purchase->payment_proof_path);
        }
        $purchase->update([
            'payment_proof_path' => $path,
            'payment_reference' => $validated['payment_reference'] ?? null,
            'payment_submitted_at' => now(),
            'payment_review_note' => null,
            'payment_status' => 'submitted',
            'status' => 'Payment submitted',
        ]);

        return back()->with('status', 'Payment proof sent privately to the artist. Your order will be delivered after they verify receipt.');
    })->where('order', 'LS-[A-Z0-9-]+')->name('transactions.payment.submit');

    Route::get('/transactions/{order}/payment-qr', function (Request $request, string $order) {
        $purchase = Order::query()
            ->where('user_id', $request->user()->id)
            ->where('order_code', $order)->firstOrFail();
        abort_unless($purchase->payment_qr_path, 404);
        abort_unless(Storage::disk('local')->exists($purchase->payment_qr_path), 404);

        return Storage::disk('local')->response($purchase->payment_qr_path);
    })->where('order', 'LS-[A-Z0-9-]+')->name('transactions.payment.qr');

    Route::get('/transactions/{order}/payment-proof', function (Request $request, string $order) {
        $purchase = Order::where('user_id', $request->user()->id)
            ->where('order_code', $order)->firstOrFail();
        abort_unless($purchase->payment_proof_path && Storage::disk('local')->exists($purchase->payment_proof_path), 404);

        return Storage::disk('local')->response($purchase->payment_proof_path);
    })->where('order', 'LS-[A-Z0-9-]+')->name('transactions.payment.proof');

    Route::get('/transactions/{order}/items/{item}/download', function (Request $request, string $order, int $item) {
        $purchase = Order::with('items')
            ->where('user_id', $request->user()->id)
            ->where('order_code', $order)->firstOrFail();
        abort_unless($purchase->payment_status === 'paid' && $purchase->status === 'Ready for download', 403);
        $line = $purchase->items->firstWhere('id', $item);
        abort_unless($line && $line->delivery_path && Storage::disk('local')->exists($line->delivery_path), 404);
        $line->forceFill(['downloaded_at' => now()])->save();

        return Storage::disk('local')->download($line->delivery_path, $line->delivery_filename);
    })->where('order', 'LS-[A-Z0-9-]+')->whereNumber('item')->name('transactions.items.download');

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

        Route::get('/orders', function () {
            $orders = Order::with(['customer', 'items'])
                ->where('seller_id', request()->user()->id)
                ->latest()
                ->paginate(15);

            return view('artist.orders', compact('orders'));
        })->name('orders');

        Route::get('/orders/{order}/payment-proof', function (string $order) {
            $purchase = Order::where('seller_id', request()->user()->id)
                ->where('order_code', $order)->firstOrFail();
            abort_unless($purchase->payment_proof_path && Storage::disk('local')->exists($purchase->payment_proof_path), 404);

            return Storage::disk('local')->response($purchase->payment_proof_path);
        })->where('order', 'LS-[A-Z0-9-]+')->name('orders.payment-proof');

        Route::post('/orders/{order}/payment-review', function (Request $request, string $order) {
            $validated = $request->validate([
                'decision' => ['required', 'in:approve,reject'],
                'payment_review_note' => ['nullable', 'string', 'max:500', 'required_if:decision,reject'],
            ]);
            $purchase = Order::where('seller_id', $request->user()->id)
                ->where('order_code', $order)->firstOrFail();
            abort_unless($purchase->payment_status === 'submitted', 409);
            abort_unless(
                $purchase->payment_proof_path
                    && Storage::disk('local')->exists($purchase->payment_proof_path),
                409,
                'The submitted payment proof is no longer available.'
            );

            $approved = $validated['decision'] === 'approve';
            $commission = $purchase->calculatePlatformCommission();
            $purchase->update([
                'payment_status' => $approved ? 'paid' : 'rejected',
                'status' => $approved ? 'Processing' : 'Payment rejected',
                'platform_commission' => $approved ? $commission : 0,
                'artist_earnings' => $approved ? round((float) $purchase->total_amount - $commission, 2) : 0,
                'payment_review_note' => $validated['payment_review_note'] ?? null,
                'payment_reviewed_at' => now(),
            ]);

            return back()->with('status', $approved
                ? 'Payment marked as received. Upload each artwork delivery file to complete this order.'
                : 'Payment proof rejected. The buyer can submit a corrected proof.');
        })->where('order', 'LS-[A-Z0-9-]+')->name('orders.payment-review');

        Route::post('/orders/{order}/items/{item}/delivery', function (Request $request, string $order, int $item) {
            $validated = $request->validate([
                'delivery_file' => ['required', 'file', 'mimes:zip,pdf,jpg,jpeg,png,webp', 'max:51200'],
            ]);
            $purchase = Order::with('items')
                ->where('seller_id', $request->user()->id)
                ->where('order_code', $order)->firstOrFail();
            abort_unless($purchase->payment_status === 'paid' && $purchase->status === 'Processing', 409);
            $line = $purchase->items->firstWhere('id', $item);
            abort_unless($line, 404);

            $path = $request->file('delivery_file')->store("artwork-deliveries/{$purchase->order_code}", 'local');
            if (! is_string($path)) {
                throw new \RuntimeException('Unable to store the artwork delivery file.');
            }
            if ($line->delivery_path) {
                Storage::disk('local')->delete($line->delivery_path);
            }

            $filename = pathinfo($request->file('delivery_file')->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $request->file('delivery_file')->getClientOriginalExtension();
            $line->update([
                'delivery_path' => $path,
                'delivery_filename' => preg_replace('/[^A-Za-z0-9._-]/', '_', $filename) . '.' . strtolower($extension),
                'delivered_at' => now(),
            ]);

            if (! $purchase->items()->whereNull('delivery_path')->exists()) {
                $purchase->update(['status' => 'Ready for download']);
            }

            return back()->with('status', 'Delivery file saved privately. The buyer can download it from their order page.');
        })->where('order', 'LS-[A-Z0-9-]+')->whereNumber('item')->name('orders.items.delivery');

        Route::get('/dashboard', function () {
            $artistId = request()->user()->artist_id;

            $artist = array_replace(
                [
                    'name' => request()->user()->display_name,
                    'email' => request()->user()->email,
                    'country' => 'Philippines',
                    'studio' => request()->user()->display_name,
                    'bio' => '',
                    'sales' => 0,
                    'revenue' => 0,
                ],
                config("demo.artists.$artistId", []),
                request()->user()->profile ?? [],
                [
                    'name' => request()->user()->display_name,
                    'email' => request()->user()->email,
                ]
            );

            $artworks = collect(config('demo.artworks'))
                ->where('artist_id', $artistId)
                ->values();

            $submissions = Artwork::where('user_id', request()->user()->id)
                ->latest()
                ->get();
            $artistOrders = Order::with(['customer', 'items'])
                ->where('seller_id', request()->user()->id)
                ->latest()
                ->take(5)
                ->get();
            $paidOrders = Order::where('seller_id', request()->user()->id)
                ->where('payment_status', 'paid');
            $artist['sales'] = (int) \App\Models\OrderItem::whereIn(
                'order_id',
                (clone $paidOrders)->select('id')
            )->sum('quantity');
            $artist['revenue'] = (clone $paidOrders)->sum('artist_earnings');
            $artist['commission'] = (clone $paidOrders)->sum('platform_commission');

            return view('artist.dashboard', compact(
                'artist',
                'artworks',
                'submissions',
                'artistOrders'
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
                    ->sum('platform_commission'),
                'artistEarnings' => Order::where('payment_status', 'paid')
                    ->sum('artist_earnings'),
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
