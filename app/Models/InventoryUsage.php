<?php

namespace App\Models;

use Database\Factories\InventoryUsageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryUsage extends Model
{
    /** @use HasFactory<InventoryUsageFactory> */
    use HasFactory;

    public const STATUS_IN_USE = 'in_use';

    public const STATUS_CONSUMED = 'consumed';

    protected $fillable = [
        'inventory_item_id',
        'started_by_employee_id',
        'finished_by_employee_id',
        'status',
        'started_at',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function startedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'started_by_employee_id');
    }

    public function finishedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'finished_by_employee_id');
    }
}
