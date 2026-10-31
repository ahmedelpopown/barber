<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\InventoryItem;
use App\Models\InventoryUsage;
use DomainException;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Start using one unit of an inventory item.
     *
     * Quantity includes the package currently in use. When a new unit is
     * started while another unit of the same item is active, the old unit is
     * marked consumed and removed from stock in the same database transaction.
     */
    public function startNewUnit(int $itemId, Employee $actor): InventoryUsage
    {
        return DB::transaction(function () use ($itemId, $actor): InventoryUsage {
            $item = InventoryItem::query()
                ->lockForUpdate()
                ->findOrFail($itemId);

            if (! $item->is_active) {
                throw new DomainException('هذا المورد غير نشط ولا يمكن بدء استخدامه.');
            }

            $currentUsage = $item->usages()
                ->where('status', InventoryUsage::STATUS_IN_USE)
                ->lockForUpdate()
                ->first();

            if ($currentUsage) {
                // One unit is currently opened. A second physical unit must be in stock.
                if ($item->quantity < 2) {
                    throw new DomainException(
                        'لا توجد عبوة جديدة متاحة. أنهِ الاستخدام الحالي أو أضف مخزونًا أولًا.'
                    );
                }

                $this->consumeCurrentUsage($item, $currentUsage, $actor);
            } elseif ($item->quantity < 1) {
                throw new DomainException('المخزون نفد. أضف كمية جديدة قبل بدء الاستخدام.');
            }

            return $item->usages()->create([
                'started_by_employee_id' => $actor->id,
                'status' => InventoryUsage::STATUS_IN_USE,
                'started_at' => now(),
            ]);
        });
    }

    /** Finish the currently opened unit and decrement stock by one. */
    public function finishCurrentUnit(int $itemId, Employee $actor): InventoryUsage
    {
        return DB::transaction(function () use ($itemId, $actor): InventoryUsage {
            $item = InventoryItem::query()
                ->lockForUpdate()
                ->findOrFail($itemId);

            $currentUsage = $item->usages()
                ->where('status', InventoryUsage::STATUS_IN_USE)
                ->lockForUpdate()
                ->first();

            if (! $currentUsage) {
                throw new DomainException('لا يوجد استخدام نشط لهذا المورد.');
            }

            return $this->consumeCurrentUsage($item, $currentUsage, $actor);
        });
    }

    private function consumeCurrentUsage(
        InventoryItem $item,
        InventoryUsage $usage,
        Employee $actor
    ): InventoryUsage {
        if ($item->quantity < 1) {
            throw new DomainException('كمية المخزون غير متسقة مع حالة الاستخدام الحالية.');
        }

        $usage->update([
            'status' => InventoryUsage::STATUS_CONSUMED,
            'finished_by_employee_id' => $actor->id,
            'finished_at' => now(),
        ]);

        $item->decrement('quantity');

        return $usage->fresh(['item', 'startedBy', 'finishedBy']);
    }
}
