<?php

namespace App\Services;

use App\Models\InventoryItem;
use DomainException;
use Illuminate\Validation\ValidationException;

class ExpenseInventoryService
{
    /**
     * Apply the difference between the old and new quantity for a purchase
     * linked to one inventory item. Call inside a database transaction.
     */
    public function syncPurchase(
        ?int $oldItemId,
        ?int $oldQuantity,
        ?int $newItemId,
        ?int $newQuantity,
    ): void {
        if ($oldItemId !== null && $oldItemId !== $newItemId) {
            throw ValidationException::withMessages([
                'inventory_item_id' => 'لا يمكن تغيير المورد المرتبط بشراء محفوظ. عدّل كمية الشراء فقط للحفاظ على سجل المخزون.',
            ]);
        }

        if ($newItemId === null) {
            return;
        }

        if ($newQuantity === null || $newQuantity < 1) {
            throw ValidationException::withMessages([
                'inventory_quantity' => 'أدخل كمية صحيحة أكبر من صفر عند ربط المصروف بالمخزون.',
            ]);
        }

        $item = InventoryItem::query()
            ->lockForUpdate()
            ->findOrFail($newItemId);

        $isExistingPurchase = $oldItemId !== null && $oldItemId === $newItemId;

        if (! $isExistingPurchase && ! $item->is_active) {
            throw ValidationException::withMessages([
                'inventory_item_id' => 'لا يمكن ربط شراء جديد بمورد مخزون غير نشط.',
            ]);
        }

        $previousPurchaseQuantity = $isExistingPurchase ? (int) $oldQuantity : 0;
        $quantityDelta = $newQuantity - $previousPurchaseQuantity;
        $updatedQuantity = (int) $item->quantity + $quantityDelta;

        if ($updatedQuantity < 0) {
            throw ValidationException::withMessages([
                'inventory_quantity' => 'لا يمكن تقليل كمية الشراء بهذا المقدار لأن الرصيد الحالي لا يكفي. غالبًا تم استهلاك جزء من المخزون.',
            ]);
        }

        if ($updatedQuantity > 100_000_000) {
            throw ValidationException::withMessages([
                'inventory_quantity' => 'الكمية الناتجة ستتجاوز الحد المسموح به للمخزون.',
            ]);
        }

        if ($quantityDelta !== 0) {
            $item->quantity = $updatedQuantity;
            $item->save();
        }
    }

    /** Reverse a stock purchase before soft-deleting its expense. */
    public function reversePurchase(?int $itemId, ?int $quantity): void
    {
        if ($itemId === null) {
            return;
        }

        if ($quantity === null || $quantity < 1) {
            throw new DomainException('بيانات كمية المخزون المرتبطة بهذا المصروف غير مكتملة، لذلك لم يتم حذفه.');
        }

        $item = InventoryItem::query()
            ->lockForUpdate()
            ->find($itemId);

        if (! $item) {
            throw new DomainException('تعذر العثور على مورد المخزون المرتبط بهذا المصروف، لذلك لم يتم حذفه.');
        }

        if ((int) $item->quantity < $quantity) {
            throw new DomainException(
                'لا يمكن حذف هذا المصروف لأن جزءًا من الكمية المشتراة قد استُهلك. احتفظ بسجل الشراء وسجّل تسوية مخزون بدلًا من حذف عملية تاريخية.'
            );
        }

        $item->quantity = (int) $item->quantity - $quantity;
        $item->save();
    }
}
