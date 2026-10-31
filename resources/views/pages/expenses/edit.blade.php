<?php

use App\Models\Expense;
use App\Models\InventoryItem;
use App\Services\ExpenseInventoryService;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('تعديل مصروف')] class extends Component
{
    use WithFileUploads;
    public Expense $expense;
    public string $title = '';
    public string $category = 'supplies';
    public string $amount = '';
    public string $expense_date = '';
    public string $payment_method = 'cash';
    public string $vendor = '';
    public string $receipt_reference = '';
    public string $notes = '';
    public array $receiptImages = [];
    public array $existingReceiptImages = [];
    public ?int $inventory_item_id = null;
    public string $inventory_quantity = '';
    public array $inventoryOptions = [];
    public bool $inventoryItemLocked = false;

    public function mount(Expense $expense): void
    {
        $this->ensureAdmin();
        $this->expense = $expense;
        $this->title = $expense->title;
        $this->category = $expense->category;
        $this->amount = (string) $expense->amount;
        $this->expense_date = $expense->expense_date->format('Y-m-d');
        $this->payment_method = $expense->payment_method;
        $this->vendor = $expense->vendor ?? '';
        $this->receipt_reference = $expense->receipt_reference ?? '';
        $this->notes = $expense->notes ?? '';
        $this->existingReceiptImages = $expense->receipt_images ?? [];
        $this->inventory_item_id = $expense->inventory_item_id;
        $this->inventory_quantity = $expense->inventory_quantity !== null
            ? (string) $expense->inventory_quantity
            : '';
        $this->inventoryItemLocked = $expense->inventory_item_id !== null;
        $this->loadInventoryOptions();
    }

    private function loadInventoryOptions(): void
    {
        $query = InventoryItem::query()->where('is_active', true);

        if ($this->inventory_item_id !== null) {
            $query->orWhereKey($this->inventory_item_id);
        }

        $this->inventoryOptions = $query
            ->orderBy('name')
            ->get(['id', 'name', 'sku', 'unit', 'quantity'])
            ->mapWithKeys(function (InventoryItem $item): array {
                $skuLabel = filled($item->sku) ? " · كود: {$item->sku}" : '';

                return [
                    $item->id => "{$item->name} ({$item->unit}) · المتاح: {$item->quantity}{$skuLabel}",
                ];
            })
            ->all();
    }

    protected function ensureAdmin(): void
    {
        abort_unless(auth('employee')->user()?->hasRole('admin'), 403);
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(array_keys(Expense::CATEGORIES))],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999999.99'],
            'expense_date' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'payment_method' => ['required', Rule::in(array_keys(Expense::PAYMENT_METHODS))],
            'vendor' => ['nullable', 'string', 'max:255'],
            'receipt_reference' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'inventory_item_id' => ['nullable', 'integer', Rule::exists('inventory_items', 'id')],
            'inventory_quantity' => ['nullable', 'integer', 'min:1', 'max:100000000', 'required_with:inventory_item_id'],
            'receiptImages' => ['nullable', 'array', 'max:5'],
            'receiptImages.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    public function removeExistingReceiptImage(int $index): void
    {
        unset($this->existingReceiptImages[$index]);
        $this->existingReceiptImages = array_values($this->existingReceiptImages);
    }

    public function update(ExpenseInventoryService $inventoryPurchases): mixed
    {
        $this->ensureAdmin();
        $validated = $this->validate();
        $uploadedImages = $validated['receiptImages'] ?? [];
        unset($validated['receiptImages']);

        if (count($this->existingReceiptImages) + count($uploadedImages) > 5) {
            $this->addError('receiptImages', 'يمكن إرفاق 5 صور كحد أقصى للمصروف الواحد.');
            return null;
        }

        $validated['title'] = trim($validated['title']);
        $validated['vendor'] = filled($validated['vendor']) ? trim($validated['vendor']) : null;
        $validated['receipt_reference'] = filled($validated['receipt_reference']) ? trim($validated['receipt_reference']) : null;
        $validated['notes'] = filled($validated['notes']) ? trim($validated['notes']) : null;

        $validated['inventory_item_id'] = filled($validated['inventory_item_id'] ?? null)
            ? (int) $validated['inventory_item_id']
            : null;
        $validated['inventory_quantity'] = $validated['inventory_item_id'] !== null
            ? (int) $validated['inventory_quantity']
            : null;

        $removedImages = array_diff($this->expense->receipt_images ?? [], $this->existingReceiptImages);
        $storedImages = array_values($this->existingReceiptImages);
        $newlyStoredImages = [];

        foreach ($uploadedImages as $image) {
            $path = $image->store('expenses/receipts/' . $this->expense->id, 'public');
            if (is_string($path)) {
                $storedImages[] = $path;
                $newlyStoredImages[] = $path;
            }
        }

        $validated['receipt_images'] = array_values($storedImages);

        try {
            DB::transaction(function () use ($validated, $inventoryPurchases): void {
                $expense = Expense::query()
                    ->lockForUpdate()
                    ->findOrFail($this->expense->id);

                $inventoryPurchases->syncPurchase(
                    $expense->inventory_item_id !== null ? (int) $expense->inventory_item_id : null,
                    $expense->inventory_quantity !== null ? (int) $expense->inventory_quantity : null,
                    $validated['inventory_item_id'],
                    $validated['inventory_quantity'],
                );

                $expense->update($validated);
            });
        } catch (\Throwable $exception) {
            foreach ($newlyStoredImages as $newImage) {
                Storage::disk('public')->delete($newImage);
            }

            throw $exception;
        }

        foreach ($removedImages as $removedImage) {
            Storage::disk('public')->delete($removedImage);
        }

        session()->flash('message', 'تم تحديث المصروف بنجاح.');

        return $this->redirect(route('admin.expenses.index'));
    }
};
?>

<div dir="rtl" class="mx-auto max-w-5xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
    <x-ui.page-header title="تعديل المصروف" description="عدّل بيانات المصروف مع الحفاظ على سجل من أنشأه أول مرة." />
    <x-ui.alert type="danger" :message="session('error')" />
    <form wire:submit="update" class="space-y-6">
        @include('pages.expenses.partials.form', ['submitLabel' => 'حفظ التعديلات'])
    </form>
</div>
