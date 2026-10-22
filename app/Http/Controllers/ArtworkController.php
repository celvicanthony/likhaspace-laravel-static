<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use Illuminate\Http\Request;

class ArtworkController extends Controller
{
    public function index()
    {
        $artworks = Artwork::where('is_active', true)
            ->latest()
            ->get();

        return view('explore', compact('artworks'));
    }

    public function show($id)
    {
        $artwork = Artwork::where('is_active', true)
            ->findOrFail($id);

        return view('product', compact('artwork'));
    }
}
