<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArtistPaymentSetting extends Model
{
    protected $fillable = [
        'user_id',
        'payment_method',
        'payment_account_name',
        'payment_account_identifier',
        'payment_qr_path',
    ];

    protected $hidden = [
        'payment_method',
        'payment_account_name',
        'payment_account_identifier',
        'payment_qr_path',
    ];

    protected function casts(): array
    {
        return [
            'payment_method' => 'encrypted',
            'payment_account_name' => 'encrypted',
            'payment_account_identifier' => 'encrypted',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
