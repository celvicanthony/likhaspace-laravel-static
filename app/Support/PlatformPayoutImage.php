<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use RuntimeException;

class PlatformPayoutImage
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
                throw new RuntimeException('Unable to publish the saved platform payout image.');
            }

            Storage::disk('local')->delete($path);
        }

        return Storage::disk('public')->exists($path)
            ? asset('storage/'.ltrim($path, '/'))
            : null;
    }

    public static function isPath(?string $path): bool
    {
        return is_string($path)
            && (
                str_starts_with($path, 'platform-payout-images/')
                || str_starts_with($path, 'platform-payouts/')
            )
            && ! str_contains($path, '..');
    }
}
