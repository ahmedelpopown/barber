<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Booking extends Model
{
    /** @use HasFactory<BookingFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_id',
        'date',
        'time',
        'turn',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(
            SubService::class,
            'booking_sub_service'
        )->withTimestamps();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(
            Employee::class
        );
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $query->where(function ($query) use ($search) {

            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });

        });
    }

    public function getProgressAttribute(): int
    {
        $start = Carbon::parse($this->date)
            ->setTimeFromTimeString($this->time);

        $duration = (int) $this->services->sum('duration');

        if ($duration === 0) {
            return 0;
        }

        $end = $start->copy()->addMinutes($duration);
        $now = now();

        if ($now->lessThan($start)) {
            return 0;
        }

        if ($now->greaterThanOrEqualTo($end)) {
            return 100;
        }

        $elapsed = $start->diffInSeconds($now);

        return min(100, (int) round(($elapsed / ($duration * 60)) * 100));
    }

    public function getRemainingMinutesAttribute(): int
    {
        $start = Carbon::parse($this->date)
            ->setTimeFromTimeString($this->time);

        $duration = (int) $this->services->sum('duration');

        if ($duration === 0) {
            return 0;
        }

        $end = $start->copy()->addMinutes($duration);

        return max(0, (int) now()->diffInMinutes($end, false));
    }

    public function getProgressColorAttribute(): string
    {
        return match (true) {
            $this->progress < 30 => 'bg-success',
            $this->progress < 70 => 'bg-warning',
            default => 'bg-danger',
        };
    }
}
