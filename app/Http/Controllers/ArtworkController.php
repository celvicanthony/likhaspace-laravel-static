<?php
namespace App\Http\Controllers;

use App\Models\Artwork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArtworkController extends Controller
{
   
    public function index()
{
    $artworks = Artwork::where('user_id', auth()->id())
        ->latest()
        ->get();

    return view('artist.inventory', compact('artworks'));
}

    // Save a new artwork
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'country' => ['required', 'in:Philippines,Greece,Scotland,Japan'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:1', 'max:1000000'],
            'artwork_file' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120'
            ],
        ]);

        $imagePath = $request->file('artwork_file')
            ->store('artworks', 'public');

        auth()->user()->artworks()->create([
            'title' => $validated['title'],
            'country' => $validated['country'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'image_path' => $imagePath,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('artist.inventory')
            ->with('status', 'Artwork successfully submitted for admin approval.');
    }

    // Display artwork editing form
    public function edit(Artwork $artwork)
    {
        $this->authorizeOwnership($artwork);

        return view('artist.edit-artwork', compact('artwork'));
    }

    // Update artwork information
    public function update(Request $request, Artwork $artwork)
    {
        $this->authorizeOwnership($artwork);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'country' => ['required', 'in:Philippines,Greece,Scotland,Japan'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:1', 'max:1000000'],
            'artwork_file' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120'
            ],
        ]);

        if ($request->hasFile('artwork_file')) {
            $newImage = $request->file('artwork_file')
                ->store('artworks', 'public');

            if ($artwork->image_path) {
                Storage::disk('public')->delete($artwork->image_path);
            }

            $artwork->image_path = $newImage;
        }

        $artwork->title = $validated['title'];
        $artwork->country = $validated['country'];
        $artwork->description = $validated['description'] ?? null;
        $artwork->price = $validated['price'];

        // Updated artwork needs another admin review
        $artwork->status = 'pending';
        $artwork->reviewed_by = null;
        $artwork->reviewed_at = null;

        $artwork->save();

        return redirect()
            ->route('artist.inventory')
            ->with('status', 'Artwork updated and submitted for review again.');
    }

    // Delete artwork
    public function destroy(Artwork $artwork)
    {
        $this->authorizeOwnership($artwork);

        if ($artwork->image_path) {
            Storage::disk('public')->delete($artwork->image_path);
        }

        $artwork->delete();

        return redirect()
            ->route('artist.inventory')
            ->with('status', 'Artwork successfully deleted.');
    }

    // Ensure artists can only manage their own artworks
    private function authorizeOwnership(Artwork $artwork): void
    {
        abort_unless(
    (int) $artwork->user_id === (int) auth()->id(),
    403,
    'You are not authorized to manage this artwork.'
);
    }
}
