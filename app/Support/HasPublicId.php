<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use RuntimeException;

trait HasPublicId
{
    public static function bootHasPublicId(): void
    {
        static::creating(function (Model $model): void {
            if (filled($model->getAttribute('public_id'))) {
                return;
            }

            $prefix = $model->publicIdPrefix();
            $value = DB::transaction(function () use ($prefix): int {
                DB::table('public_id_sequences')->insertOrIgnore([
                    'prefix' => $prefix,
                    'next_value' => 11,
                ]);

                $sequence = DB::table('public_id_sequences')
                    ->where('prefix', $prefix)
                    ->lockForUpdate()
                    ->first();

                if (! $sequence) {
                    throw new RuntimeException("Unable to allocate a public ID for {$prefix}.");
                }

                DB::table('public_id_sequences')
                    ->where('prefix', $prefix)
                    ->update(['next_value' => (int) $sequence->next_value + 1]);

                return (int) $sequence->next_value;
            });

            $model->setAttribute(
                'public_id',
                $prefix.'-'.str_pad((string) $value, 5, '0', STR_PAD_LEFT)
            );
        });
    }

    abstract protected function publicIdPrefix(): string;
}
