<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordChangeRequest extends Model
{
    protected $fillable = [
        'account_type',
        'account_id',
        'requested_by_employee_id',
        'requested_by_name',
        'requested_by_email',
        'token_hash',
        'status',
        'expires_at',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'responded_at' => 'datetime',
        ];
    }

    public function initiator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'requested_by_employee_id');
    }

    public function account(): User|Employee|null
    {
        return match ($this->account_type) {
            'customer' => User::query()->find($this->account_id),
            'employee' => Employee::query()->find($this->account_id),
            default => null,
        };
    }
}
