<?php

namespace App\Models;

use App\Support\HasPublicId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArtistCommissionPayment extends Model
{
    use HasPublicId;

    protected $fillable = [
        'artist_id',
        'amount_sent',
        'amount_received',
        'payment_method',
        'payment_reference',
        'artist_note',
        'proof_path',
        'status',
        'admin_note',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $hidden = [
        'proof_path',
    ];

    protected function casts(): array
    {
        return [
            'amount_sent' => 'decimal:2',
            'amount_received' => 'decimal:2',
            'reviewed_at' => 'datetime',
        ];
    }

    public function artist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'artist_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    protected function publicIdPrefix(): string
    {
        return 'COMMISSION';
    }
}
