<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformPayoutSetting extends Model
{
    protected $fillable = [
        'payment_method',
        'provider_name',
        'account_name',
        'account_identifier',
        'payout_qr_path',
    ];

    protected $hidden = [
        'payment_method',
        'provider_name',
        'account_name',
        'account_identifier',
        'payout_qr_path',
    ];

    protected function casts(): array
    {
        return [
            'payment_method' => 'encrypted',
            'provider_name' => 'encrypted',
            'account_name' => 'encrypted',
            'account_identifier' => 'encrypted',
        ];
    }
}
