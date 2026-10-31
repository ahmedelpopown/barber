<?php

use App\Models\InventoryItem;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('إضافة مورد للمخزون')] class extends Component
{
    public string $name = '';
    public string $sku = '';
    public string $description = '';
    public string $unit = 'عبوة';
    public string $unit_price = '0.00';
    public int $quantity = 0;
    public int $minimum_quantity = 0;
    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:inventory_items,name'],
            'sku' => ['nullable', 'string', 'max:64', 'unique:inventory_items,sku'],
            'description' => ['nullable', 'string', 'max:5000'],
            'unit' => ['required', 'string', 'max:40'],
            'unit_price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'quantity' => ['required', 'integer', 'min:0', 'max:100000000'],
            'minimum_quantity' => ['required', 'integer', 'min:0', 'max:100000000'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function mount(): void
    {
        abort_unless(auth('employee')->user()?->hasRole('admin'), 403);
    }

    public function save(): mixed
    {
        abort_unless(auth('employee')->user()?->hasRole('admin'), 403);

        $validated = $this->validate();

        $item = InventoryItem::query()->create([
            'name' => trim($validated['name']),
            'sku' => filled($validated['sku']) ? trim($validated['sku']) : null,
            'description' => filled($validated['description']) ? trim($validated['description']) : null,
            'unit' => trim($validated['unit']),
            'unit_price' => $validated['unit_price'],
            'quantity' => $validated['quantity'],
            'minimum_quantity' => $validated['minimum_quantity'],
            'is_active' => $validated['is_active'],
        ]);

        session()->flash('message', "تم إضافة المورد {$item->name} بنجاح.");

        return $this->redirect(route('admin.resources.index'));
    }
};
?>

<div dir="rtl" class="mx-auto max-w-5xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
    <x-ui.page-header
        title="إضافة مورد جديد"
        description="أضف بيانات الكريم أو المستهلك أو الأداة، وحدد سعر الوحدة والكمية وحد التنبيه."
    />

    <div class="space-y-3">
        <x-ui.alert type="danger" :message="session('error')" />
    </div>

    <form wire:submit="save" class="space-y-6">
        @include('pages.resources.partials.form', [
            'isEdit' => false,
            'submitLabel' => 'حفظ المورد',
        ])
    </form>
</div>
