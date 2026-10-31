<?php

use App\Models\InventoryItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('تعديل بيانات المورد')] class extends Component
{
    public InventoryItem $resource;

    public string $name = '';
    public string $sku = '';
    public string $description = '';
    public string $unit = 'عبوة';
    public string $unit_price = '0.00';
    public int $quantity = 0;
    public int $minimum_quantity = 0;
    public bool $is_active = true;

    public function mount(InventoryItem $resource): void
    {
        $this->ensureAdmin();

        $this->resource = $resource;
        $this->name = $resource->name;
        $this->sku = $resource->sku ?? '';
        $this->description = $resource->description ?? '';
        $this->unit = $resource->unit;
        $this->unit_price = (string) $resource->unit_price;
        $this->quantity = $resource->quantity;
        $this->minimum_quantity = $resource->minimum_quantity;
        $this->is_active = $resource->is_active;
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('inventory_items', 'name')->ignore($this->resource->id)],
            'sku' => ['nullable', 'string', 'max:64', Rule::unique('inventory_items', 'sku')->ignore($this->resource->id)],
            'description' => ['nullable', 'string', 'max:5000'],
            'unit' => ['required', 'string', 'max:40'],
            'unit_price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'quantity' => ['required', 'integer', 'min:0', 'max:100000000'],
            'minimum_quantity' => ['required', 'integer', 'min:0', 'max:100000000'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function save(): mixed
    {
        $this->ensureAdmin();
        $validated = $this->validate();

        DB::transaction(function () use ($validated): void {
            $resource = InventoryItem::query()
                ->lockForUpdate()
                ->findOrFail($this->resource->id);

            if ($resource->currentUsage()->exists() && $validated['quantity'] < 1) {
                throw ValidationException::withMessages([
                    'quantity' => 'لا يمكن جعل الكمية صفرًا بينما توجد وحدة قيد الاستخدام. أنهِ الاستخدام أولًا.',
                ]);
            }

            $resource->update([
                'name' => trim($validated['name']),
                'sku' => filled($validated['sku']) ? trim($validated['sku']) : null,
                'description' => filled($validated['description']) ? trim($validated['description']) : null,
                'unit' => trim($validated['unit']),
                'unit_price' => $validated['unit_price'],
                'quantity' => $validated['quantity'],
                'minimum_quantity' => $validated['minimum_quantity'],
                'is_active' => $validated['is_active'],
            ]);

            $this->resource = $resource->fresh();
        });

        session()->flash('message', 'تم تحديث بيانات المورد بنجاح.');

        return $this->redirect(route('admin.resources.index'));
    }

    private function ensureAdmin(): void
    {
        abort_unless(auth('employee')->user()?->hasRole('admin'), 403);
    }
};
?>

<div dir="rtl" class="mx-auto max-w-5xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
    <x-ui.page-header
        title="تعديل المورد"
        description="عدّل تفاصيل المورد وحدّث رصيد المخزون. الكمية تشمل العبوة المفتوحة حاليًا إن وجدت."
    >
        <x-slot:actions>
            <x-ui.action variant="secondary" href="{{ route('admin.resources.index') }}" wire:navigate>
                رجوع للمخزون
            </x-ui.action>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="space-y-3">
        <x-ui.alert type="danger" :message="session('error')" />
    </div>

    <form wire:submit="save" class="space-y-6">
        @include('pages.resources.partials.form', [
            'isEdit' => true,
            'submitLabel' => 'حفظ التعديلات',
        ])
    </form>
</div>
