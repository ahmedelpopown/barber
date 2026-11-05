<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Casts\Attribute;
#[Fillable(['name', 'email', 'password','phone','avatar'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token, 'customer'));
    }

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable ,HasRoles;

        protected string $guard_name = 'web';
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
        ];
    }
       public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%$search%")
               
                ->orWhere('email', 'like', "%$search%");
     
        });
    }

    public function bookings(): HasMany
{
    return $this->hasMany(Booking::class);
}

protected function avatar(): Attribute
{
    return Attribute::make(
        get: fn ($value) => $value
            ? (
                filter_var($value, FILTER_VALIDATE_URL)
                    ? $value
                    : asset('storage/' . $value)
            )
            : null,
    );
}
}
