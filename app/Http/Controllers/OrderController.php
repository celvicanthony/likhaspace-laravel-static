<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function cart(Request $request): View
    {
        $cart = Session::get('cart', []);
        $artworks = Artwork::with('artist')
            ->where('status', 'approved')
            ->whereHas('artist', fn ($artist) => $artist->where('role', 'artist')->where('status', 'active'))
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        $items = [];
        $validCart = [];

        foreach ($cart as $artworkId => $quantity) {
            $artwork = $artworks->get($artworkId);
            if (! $artwork || $artwork->stock_quantity < 1) {
                continue;
            }

            $quantity = $artwork->stock_type === 'single_adoptable'
                ? 1
                : min(max(1, (int) $quantity), $artwork->stock_quantity);

            $validCart[$artwork->id] = $quantity;
            $items[] = [
                'artwork' => $artwork,
                'quantity' => $quantity,
                'price' => (float) $artwork->price,
                'line_total' => (float) $artwork->price * $quantity,
            ];
        }

        Session::put('cart', $validCart);

        return view('cart', [
            'items' => $items,
            'subtotal' => collect($items)->sum('line_total'),
        ]);
    }

    public function addToCart(Request $request, int $artwork): RedirectResponse
    {
        $submission = Artwork::where('status', 'approved')
            ->whereHas('artist', fn ($artist) => $artist->where('role', 'artist')->where('status', 'active'))
            ->findOrFail($artwork);
        abort_if($submission->stock_quantity < 1, 409, 'This character is sold out.');

        $quantity = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1', 'max:9999'],
        ])['quantity'] ?? 1;
        $cart = Session::get('cart', []);

        if ($submission->stock_type === 'single_adoptable') {
            $cart[$submission->id] = 1;
        } else {
            $newQuantity = (int) ($cart[$submission->id] ?? 0) + (int) $quantity;
            if ($newQuantity > $submission->stock_quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'The requested quantity exceeds the available stock.',
                ]);
            }
            $cart[$submission->id] = $newQuantity;
        }

        Session::put('cart', $cart);

        return redirect()->route('cart')->with('status', 'Character added to your cart.');
    }

    public function updateCart(Request $request, int $artwork): RedirectResponse
    {
        $cart = Session::get('cart', []);
        abort_unless(isset($cart[$artwork]), 404);
        $submission = Artwork::where('status', 'approved')
            ->whereHas('artist', fn ($artist) => $artist->where('role', 'artist')->where('status', 'active'))
            ->findOrFail($artwork);

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:9999'],
        ]);

        if (
            $submission->stock_quantity < 1
            || ($submission->stock_type === 'multi_copy' && $validated['quantity'] > $submission->stock_quantity)
        ) {
            throw ValidationException::withMessages([
                'quantity' => 'The requested quantity exceeds the available stock.',
            ]);
        }

        $cart[$artwork] = $submission->stock_type === 'single_adoptable'
            ? 1
            : (int) $validated['quantity'];
        Session::put('cart', $cart);

        return redirect()->route('cart')->with('status', 'Cart quantity updated.');
    }

    public function removeFromCart(int $artwork): RedirectResponse
    {
        $cart = Session::get('cart', []);
        unset($cart[$artwork]);
        Session::put('cart', $cart);

        return redirect()->route('cart')->with('status', 'Character removed from your cart.');
    }

    public function checkout(): View|RedirectResponse
    {
        $cart = Session::get('cart', []);
        if ($cart === []) {
            return redirect()->route('cart')->withErrors(['cart' => 'Your cart is empty.']);
        }

        $artworks = Artwork::with('artist')
            ->where('status', 'approved')
            ->whereHas('artist', fn ($artist) => $artist->where('role', 'artist')->where('status', 'active'))
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');
        $items = [];
        $artists = collect();
        $validCart = [];

        foreach ($cart as $id => $quantity) {
            $artwork = $artworks->get($id);
            if (! $artwork || $artwork->stock_quantity < 1) {
                continue;
            }
            $quantity = $artwork->stock_type === 'single_adoptable'
                ? 1
                : min(max(1, (int) $quantity), $artwork->stock_quantity);
            $validCart[$artwork->id] = $quantity;
            $items[] = [
                'artwork' => $artwork,
                'title' => $artwork->title,
                'artist' => $artwork->artist?->display_name ?? 'Unknown Artist',
                'quantity' => $quantity,
                'price' => (float) $artwork->price,
                'subtotal' => round((float) $artwork->price * $quantity, 2),
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
        if ($items === []) {
            return redirect()->route('cart')->withErrors([
                'cart' => 'No approved, in-stock characters are available in your cart.',
            ]);
        }

        return view('checkout', [
            'items' => $items,
            'total' => round(collect($items)->sum('subtotal'), 2),
            'buyer' => request()->user(),
            'artists' => $artists,
            'paymentReady' => $artists->isNotEmpty() && $artists->every(fn (array $artist) => $artist['ready']),
        ]);
    }

    public function placeOrder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
        ]);
        $cart = Session::get('cart', []);
        if ($cart === []) {
            return redirect()->route('cart')->withErrors(['cart' => 'Your cart is empty.']);
        }

        DB::transaction(function () use ($cart, $validated, $request) {
            $artworks = Artwork::with('artist')
                ->whereIn('id', array_keys($cart))
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($artworks->count() !== count($cart)) {
                throw ValidationException::withMessages([
                    'cart' => 'A character in your cart is no longer available.',
                ]);
            }

            $groupedItems = [];
            foreach ($cart as $id => $quantity) {
                $artwork = $artworks->get($id);
                $quantity = filter_var($quantity, FILTER_VALIDATE_INT);
                if (
                    ! $artwork
                    || $artwork->status !== 'approved'
                    || $quantity === false
                    || $quantity < 1
                    || ($artwork->stock_type === 'single_adoptable' && $quantity !== 1)
                    || ! $artwork->hasAvailableStock((int) $quantity)
                ) {
                    throw ValidationException::withMessages([
                        'cart' => 'A character has sold out or the requested quantity is no longer available.',
                    ]);
                }

                $seller = $artwork->artist;
                if (
                    ! $seller || $seller->role !== 'artist' || $seller->status !== 'active'
                    || blank($seller->payment_method)
                    || blank($seller->payment_account_name)
                    || blank($seller->payment_account_identifier)
                ) {
                    throw ValidationException::withMessages([
                        'cart' => 'An artist has not completed their private payment setup. Please try again later.',
                    ]);
                }

                $groupedItems[$seller->id][] = [
                    'artwork' => $artwork,
                    'quantity' => (int) $quantity,
                    'subtotal' => round((float) $artwork->price * (int) $quantity, 2),
                ];
            }

            foreach ($groupedItems as $sellerId => $items) {
                $seller = $items[0]['artwork']->artist;
                $total = round(collect($items)->sum('subtotal'), 2);
                $order = Order::create([
                    'order_code' => 'LS-' . now()->format('Ymd') . '-' . Str::upper(Str::random(8)),
                    'user_id' => $request->user()->id,
                    'seller_id' => $sellerId,
                    'buyer_name' => $validated['name'],
                    'buyer_email' => $validated['email'],
                    'buyer_contact' => data_get($request->user()->profile, 'contact'),
                    'shipping_address' => 'Digital delivery',
                    'payment_method' => strtoupper($seller->payment_method),
                    'payment_recipient_name' => $seller->payment_account_name,
                    'payment_recipient_identifier' => $seller->payment_account_identifier,
                    'payment_qr_path' => $seller->payment_qr_path,
                    'payment_status' => 'unpaid',
                    'status' => 'Pending',
                    'total_amount' => $total,
                ]);

                foreach ($items as $item) {
                    $artwork = $item['artwork'];
                    $order->items()->create([
                        'artwork_id' => $artwork->id,
                        'artwork_title' => $artwork->title,
                        'artist_name' => $seller->display_name,
                        'quantity' => $item['quantity'],
                        'unit_price' => $artwork->price,
                        'subtotal' => $item['subtotal'],
                    ]);
                    $artwork->decrement('stock_quantity', $item['quantity']);
                }
            }
        });

        Session::forget('cart');

        return redirect()->route('transactions')
            ->with('status', 'Your digital character orders are ready. Submit payment proof for each artist to confirm your orders.');
    }

    public function index(Request $request): View
    {
        $orders = Order::with('items')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return view('transactions.index', compact('orders'));
    }

    public function show(Request $request, string $order): View
    {
        $purchase = Order::with(['items.review', 'items.artwork', 'seller'])
            ->where('user_id', $request->user()->id)
            ->where('order_code', $order)
            ->firstOrFail();

        return view('transactions.show', ['order' => $purchase]);
    }

    public function downloadAsset(Request $request, string $order, int $item): StreamedResponse
    {
        $purchase = Order::with('items.artwork')
            ->where('user_id', $request->user()->id)
            ->where('order_code', $order)
            ->firstOrFail();
        abort_unless(
            $purchase->payment_status === 'paid'
                && in_array($purchase->status, ['Accepted', 'Completed', 'Ready for download'], true),
            403
        );

        $line = $purchase->items->firstWhere('id', $item);
        abort_unless($line, 404);

        if ($line->artwork?->art_file_path && Storage::disk('local')->exists($line->artwork->art_file_path)) {
            $path = $line->artwork->art_file_path;
            $filename = Str::slug($line->artwork_title) . '.' . strtolower($line->artwork->file_format ?? pathinfo($path, PATHINFO_EXTENSION));
        } else {
            abort_unless($line->delivery_path && Storage::disk('local')->exists($line->delivery_path), 404);
            $path = $line->delivery_path;
            $filename = $line->delivery_filename ?: basename($path);
        }

        DB::transaction(function () use ($line, $purchase) {
            $line->forceFill(['downloaded_at' => now()])->save();
            if (! $purchase->items()->whereNull('downloaded_at')->exists()) {
                $purchase->update(['status' => 'Completed']);
            }
        });

        return Storage::disk('local')->download($path, $filename);
    }

    public function artistOrders(Request $request): View
    {
        $query = Order::with(['customer', 'items'])
            ->where('seller_id', $request->user()->id);
        $status = $request->query('status');
        if (in_array($status, ['Pending', 'Accepted', 'Completed', 'Cancelled'], true)) {
            $query->where('status', $status);
        }

        return view('artist.orders', [
            'orders' => $query->with('items.artwork')->latest()->paginate(15)->withQueryString(),
            'selectedStatus' => $status,
        ]);
    }

    public function updateArtistOrder(Request $request, string $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:Accepted,Cancelled'],
        ]);

        DB::transaction(function () use ($request, $order, $validated) {
            $purchase = Order::where('seller_id', $request->user()->id)
                ->where('order_code', $order)
                ->lockForUpdate()
                ->firstOrFail();
            abort_unless($purchase->status === 'Pending', 409, 'Only pending orders can be changed.');

            if ($validated['status'] === 'Accepted') {
                abort_unless($purchase->payment_status === 'paid', 409, 'Confirm the buyer payment before accepting this order.');
                $purchase->update(['status' => 'Accepted']);

                return;
            }

            abort_if(
                ! in_array($purchase->payment_status, ['unpaid', 'rejected'], true),
                409,
                'Orders with submitted or confirmed payment cannot be cancelled until the payment is reviewed and any refund is handled.'
            );
            $purchase->load('items');
            foreach ($purchase->items as $item) {
                if ($item->artwork_id) {
                    Artwork::whereKey($item->artwork_id)
                        ->lockForUpdate()
                        ->increment('stock_quantity', $item->quantity);
                }
            }
            $purchase->update(['status' => 'Cancelled']);
        });

        return back()->with('status', 'Order status updated.');
    }
}
