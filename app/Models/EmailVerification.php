<?php

namespace App\Models;

use Database\Factories\EmailVerificationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
    /** @use HasFactory<EmailVerificationFactory> */
    use HasFactory;

    protected $fillable = [
        'email',
        'name',
        'password',
        'otp',
        'attempts',
        'expires_at',
        'last_sent_at',
        'verified_at',
    ];

    protected $casts = [
        'attempts' => 'integer',
        'expires_at' => 'datetime',
        'last_sent_at' => 'datetime',
        'verified_at' => 'datetime',
    ];
}
