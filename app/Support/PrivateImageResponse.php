<?php

namespace App\Support;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class PrivateImageResponse
{
    public static function make(?string $path, string $directory): Response
    {
        abort_unless(
            is_string($path)
                && str_starts_with($path, $directory.'/')
                && ! str_contains($path, '..')
                && (
                    Storage::disk('local')->exists($path)
                    || Storage::disk('public')->exists($path)
                ),
            404
        );

        $disk = Storage::disk('local')->exists($path) ? 'local' : 'public';
        $mimeType = Storage::disk($disk)->mimeType($path);
        abort_unless(in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp'], true), 404);

        $response = response(Storage::disk($disk)->get($path), 200, [
            'Content-Type' => $mimeType,
        ]);
        $response->headers->set('Cache-Control', 'private, no-store');
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        return $response;
    }
}
