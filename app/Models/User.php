<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'middle_name',
        'last_name',
        'name_extension',
        'display_name',
        'birthdate',
        'gender',
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
        'name',
        'first_name',
        'middle_name',
        'last_name',
        'name_extension',
        'birthdate',
        'gender',
        'password',
        'remember_token',
        'birthdate',
        'gender',
        'payment_method',
        'payment_account_name',
        'payment_account_identifier',
        'payment_qr_path',
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
            'birthdate' => 'date:Y-m-d',
            'password' => 'hashed',
            'profile' => 'array',
            'payment_method' => 'encrypted',
            'payment_account_name' => 'encrypted',
            'payment_account_identifier' => 'encrypted',
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

    public function setNameAttribute(string $name): void
    {
        $this->attributes['name'] = self::formatName($name);
    }

    public function setFirstNameAttribute(?string $firstName): void
    {
        $this->attributes['first_name'] = filled($firstName) ? self::formatName($firstName) : null;
    }

    public function setMiddleNameAttribute(?string $middleName): void
    {
        $this->attributes['middle_name'] = filled($middleName) ? self::formatName($middleName) : null;
    }

    public function setLastNameAttribute(?string $lastName): void
    {
        $this->attributes['last_name'] = filled($lastName) ? self::formatName($lastName) : null;
    }

    public function setNameExtensionAttribute(?string $extension): void
    {
        $this->attributes['name_extension'] = filled($extension) ? self::formatName($extension) : null;
    }

    public function getNameAttribute(string $name): string
    {
        if (filled($this->attributes['first_name'] ?? null) && filled($this->attributes['last_name'] ?? null)) {
            return self::formatName(collect([
                $this->attributes['first_name'],
                $this->attributes['middle_name'] ?? null,
                $this->attributes['last_name'],
                $this->attributes['name_extension'] ?? null,
            ])->filter()->implode(' '));
        }

        return self::formatName($name);
    }

    public function setDisplayNameAttribute(?string $displayName): void
    {
        $this->attributes['display_name'] = filled($displayName)
            ? self::formatName($displayName)
            : null;
    }

    public function getDisplayNameAttribute(?string $displayName): string
    {
        return filled($displayName) ? self::formatName($displayName) : $this->name;
    }

    private static function formatName(string $name): string
    {
        return collect(explode(' ', Str::squish($name)))
            ->map(static function (string $part): string {
                if (preg_match('/^[IVXLCDM]+$/i', $part)) {
                    return Str::upper($part);
                }

                $firstCharacter = substr($part, 0, 1);
                $firstCharacterIsLowercase = Str::lower($firstCharacter) === $firstCharacter
                    && Str::upper($firstCharacter) !== $firstCharacter;

                return $firstCharacterIsLowercase || Str::upper($part) === $part
                    ? Str::title(Str::lower($part))
                    : $part;
            })
            ->implode(' ');
    }
}
