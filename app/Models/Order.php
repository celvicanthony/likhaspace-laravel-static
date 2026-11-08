<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const PLATFORM_COMMISSION_RATE = 0.10;

    protected $hidden = [
        'payment_proof_path',
        'payment_reference',
        'payment_review_note',
        'payment_recipient_name',
        'payment_recipient_identifier',
        'payment_qr_path',
    ];

    protected $fillable = [
        'order_code',
        'user_id',
        'seller_id',
        'buyer_name',
        'buyer_email',
        'shipping_address',
        'payment_method',
        'payment_status',
        'status',
        'total_amount',
        'payment_proof_path',
        'payment_reference',
        'payment_submitted_at',
        'payment_review_note',
        'payment_reviewed_at',
        'cancelled_at',
        'payment_recipient_name',
        'payment_recipient_identifier',
        'payment_qr_path',
        'platform_commission',
        'artist_earnings',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'platform_commission' => 'decimal:2',
            'artist_earnings' => 'decimal:2',
            'payment_submitted_at' => 'datetime',
            'payment_reviewed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'payment_recipient_name' => 'encrypted',
            'payment_recipient_identifier' => 'encrypted',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function calculatePlatformCommission(): float
    {
        return round((float) $this->total_amount * self::PLATFORM_COMMISSION_RATE, 2);
    }

    public function canBeCancelled(): bool
    {
        return ($this->payment_status === 'unpaid' && $this->status === 'Awaiting payment')
            || ($this->payment_status === 'rejected' && $this->status === 'Payment rejected');
    }
}
