<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'artwork_id',
        'artwork_title',
        'artist_name',
        'quantity',
        'unit_price',
        'subtotal',
        'delivery_path',
        'delivery_filename',
        'delivered_at',
        'downloaded_at',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'delivered_at' => 'datetime',
            'downloaded_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function artwork(): BelongsTo
    {
        return $this->belongsTo(Artwork::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(ArtworkReview::class);
    }
}
