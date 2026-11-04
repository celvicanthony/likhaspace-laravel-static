<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Artwork extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'country',
        'description',
        'price',
        'image_path',
        'status',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'reviewed_at' => 'datetime',
        ];
    }

    public function artist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
