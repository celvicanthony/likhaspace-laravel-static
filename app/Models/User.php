<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Support\HasPublicId;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    use HasPublicId;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'artist_id',
        'status',
        'profile',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'payment_settings',
        'identity_profile',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'profile' => 'array',
        ];
    }
    public function artworks(): HasMany
{
    return $this->hasMany(Artwork::class);
}

    public function sales(): HasMany
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    public function paymentSettings(): HasOne
    {
        return $this->hasOne(ArtistPaymentSetting::class);
    }

    public function identityProfile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function setNameAttribute(string $name): void
    {
        $this->attributes['name'] = self::formatName($name);
    }

    public function getNameAttribute(string $name): string
    {
        return self::formatName($name);
    }

    private static function formatName(string $name): string
    {
        return collect(explode(' ', Str::squish($name)))
            ->map(static function (string $part): string {
                $firstCharacter = substr($part, 0, 1);
                $firstCharacterIsLowercase = Str::lower($firstCharacter) === $firstCharacter
                    && Str::upper($firstCharacter) !== $firstCharacter;

                return $firstCharacterIsLowercase || Str::upper($part) === $part
                    ? Str::title(Str::lower($part))
                    : $part;
            })
            ->implode(' ');
    }

    protected function publicIdPrefix(): string
    {
        return match ($this->role) {
            'artist' => 'ARTIST',
            'admin' => 'ADMIN',
            'client', null => 'CLIENT',
            default => 'USER',
        };
    }
}
