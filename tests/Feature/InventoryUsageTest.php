<?php

use App\Models\Employee;
use App\Models\InventoryItem;
use App\Models\InventoryUsage;
use App\Services\InventoryService;

function makeInventoryItem(array $overrides = []): InventoryItem
{
    return InventoryItem::query()->create(array_merge([
        'name' => 'كريم اختبار',
        'sku' => 'CREAM-TEST-' . fake()->unique()->numerify('####'),
        'description' => 'مورد للاختبار',
        'unit' => 'عبوة',
        'unit_price' => '75.00',
        'quantity' => 3,
        'minimum_quantity' => 1,
        'is_active' => true,
    ], $overrides));
}

it('starts using the first unit without decrementing stock immediately', function () {
    $employee = Employee::factory()->create();
    $item = makeInventoryItem(['quantity' => 3]);

    $usage = app(InventoryService::class)->startNewUnit($item->id, $employee);

    expect($usage->status)->toBe(InventoryUsage::STATUS_IN_USE)
        ->and($item->fresh()->quantity)->toBe(3)
        ->and($item->fresh()->currentUsage)->not->toBeNull();
});

it('consumes the previous unit and starts a new one when stock permits', function () {
    $employee = Employee::factory()->create();
    $item = makeInventoryItem(['quantity' => 3]);
    $service = app(InventoryService::class);

    $previousUsage = $service->startNewUnit($item->id, $employee);
    $newUsage = $service->startNewUnit($item->id, $employee);

    expect($previousUsage->fresh()->status)->toBe(InventoryUsage::STATUS_CONSUMED)
        ->and($previousUsage->fresh()->finished_by_employee_id)->toBe($employee->id)
        ->and($newUsage->status)->toBe(InventoryUsage::STATUS_IN_USE)
        ->and($item->fresh()->quantity)->toBe(2)
        ->and($item->fresh()->currentUsage->id)->toBe($newUsage->id);
});

it('does not open a new unit if the current unit is the only unit left', function () {
    $employee = Employee::factory()->create();
    $item = makeInventoryItem(['quantity' => 1]);
    $service = app(InventoryService::class);
    $usage = $service->startNewUnit($item->id, $employee);

    expect(fn () => $service->startNewUnit($item->id, $employee))
        ->toThrow(\DomainException::class);

    expect($item->fresh()->quantity)->toBe(1)
        ->and($usage->fresh()->status)->toBe(InventoryUsage::STATUS_IN_USE);
});

it('decrements stock when the current unit is explicitly finished', function () {
    $employee = Employee::factory()->create();
    $item = makeInventoryItem(['quantity' => 2]);
    $service = app(InventoryService::class);
    $usage = $service->startNewUnit($item->id, $employee);

    $finished = $service->finishCurrentUnit($item->id, $employee);

    expect($finished->status)->toBe(InventoryUsage::STATUS_CONSUMED)
        ->and($finished->finished_at)->not->toBeNull()
        ->and($item->fresh()->quantity)->toBe(1)
        ->and($item->fresh()->currentUsage)->toBeNull();
});

it('does not allow an inactive item to enter use', function () {
    $employee = Employee::factory()->create();
    $item = makeInventoryItem(['quantity' => 2, 'is_active' => false]);

    expect(fn () => app(InventoryService::class)->startNewUnit($item->id, $employee))
        ->toThrow(\DomainException::class);

    expect($item->fresh()->quantity)->toBe(2)
        ->and($item->usages()->count())->toBe(0);
});
