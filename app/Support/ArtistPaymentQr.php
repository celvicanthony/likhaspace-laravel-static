<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ArtistPaymentQr
{
    public static function url(?string $path): ?string
    {
        if (! self::isPath($path)) {
            return null;
        }

        if (! Storage::disk('public')->exists($path) && Storage::disk('local')->exists($path)) {
            $copied = Storage::disk('public')->put(
                $path,
                Storage::disk('local')->get($path)
            );
            if (! $copied || ! Storage::disk('public')->exists($path)) {
                throw new RuntimeException('Unable to publish the saved artist payment QR image.');
            }

            Storage::disk('local')->delete($path);
        }

        return Storage::disk('public')->exists($path)
            ? asset('storage/'.ltrim($path, '/'))
            : null;
    }

    public static function isPath(?string $path, ?int $artistId = null): bool
    {
        if (! is_string($path) || str_contains($path, '..')) {
            return false;
        }

        return $artistId === null
            ? preg_match('#\Aartist-payments/\d+/[^/]+\z#', $path) === 1
            : str_starts_with($path, "artist-payments/{$artistId}/")
                && substr_count($path, '/') === 2;
    }
}
