<?php

use App\Models\Booking;
use App\Models\InventoryItem;
use App\Models\InventoryUsage;
use App\Models\EmailVerification;
use Illuminate\Support\Facades\Hash;
use App\Models\Service;
use App\Models\SubService;
use Illuminate\Support\Facades\Artisan;

it('creates bookings with linked sub-services through the factory state', function (): void {
    $booking = Booking::factory()->withServices(2)->create();

    expect($booking->services()->count())->toBe(2)
        ->and($booking->services()->first())->toBeInstanceOf(SubService::class)
        ->and(Service::query()->count())->toBeGreaterThanOrEqual(1);
});

it('creates an inventory item with exactly one current usage through the factory state', function (): void {
    $item = InventoryItem::factory()->inUse()->create();

    expect($item->fresh()->quantity)->toBeGreaterThanOrEqual(1)
        ->and($item->fresh()->currentUsage)->not->toBeNull()
        ->and($item->usages()->where('status', InventoryUsage::STATUS_IN_USE)->count())->toBe(1);
});

it('creates OTP verification records with hashed OTPs and expiry state helpers', function (): void {
    $verification = EmailVerification::factory()->withOtp('123456')->create();
    $expired = EmailVerification::factory()->expired()->create();

    expect(Hash::check('123456', $verification->otp))->toBeTrue()
        ->and($verification->password)->not->toBe('password123')
        ->and($expired->expires_at->isPast())->toBeTrue();
});

it('creates consumed inventory usage with a finish time after its start time', function (): void {
    $usage = InventoryUsage::factory()->consumed()->create();

    expect($usage->status)->toBe(InventoryUsage::STATUS_CONSUMED)
        ->and($usage->started_at)->not->toBeNull()
        ->and($usage->finished_at)->not->toBeNull()
        ->and($usage->finished_at->greaterThan($usage->started_at))->toBeTrue();
});

it('can seed demo data twice without multiplying fixed catalog, demo accounts, or same-day bookings', function (): void {
    Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\DatabaseSeeder', '--force' => true]);

    $firstCounts = [
        'users' => \App\Models\User::query()->count(),
        'employees' => \App\Models\Employee::query()->count(),
        'services' => Service::query()->count(),
        'sub_services' => SubService::query()->count(),
        'bookings' => Booking::query()->count(),
        'inventory_items' => InventoryItem::query()->count(),
        'inventory_usages' => InventoryUsage::query()->count(),
        'email_verifications' => EmailVerification::query()->count(),
    ];

    Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\DatabaseSeeder', '--force' => true]);

    $secondCounts = [
        'users' => \App\Models\User::query()->count(),
        'employees' => \App\Models\Employee::query()->count(),
        'services' => Service::query()->count(),
        'sub_services' => SubService::query()->count(),
        'bookings' => Booking::query()->count(),
        'inventory_items' => InventoryItem::query()->count(),
        'inventory_usages' => InventoryUsage::query()->count(),
        'email_verifications' => EmailVerification::query()->count(),
    ];

    expect($secondCounts)->toBe($firstCounts);
});
