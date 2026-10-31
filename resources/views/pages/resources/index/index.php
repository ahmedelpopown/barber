<?php

use App\Models\InventoryItem;
use App\Models\InventoryUsage;
use App\Services\InventoryService;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('الموارد والمخزون')] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $stockFilter = 'all';
    public string $sortField = 'name';
    public string $sortDirection = 'asc';
    public int $perPage = 10;

    public array $selected = [];
    public bool $selectAll = false;

    public bool $showDeleteModal = false;
    public ?int $itemToDelete = null;

    public bool $showHistoryModal = false;
    public ?int $historyItemId = null;
    public string $historyItemName = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'stockFilter' => ['except' => 'all'],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    #[Computed]
    public function items()
    {
        $sortField = in_array($this->sortField, ['name', 'quantity', 'unit_price', 'created_at'], true)
            ? $this->sortField
            : 'name';

        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';
        $perPage = in_array($this->perPage, [10, 25, 50], true) ? $this->perPage : 10;

        return InventoryItem::query()
            ->with(['currentUsage.startedBy', 'currentUsage.finishedBy'])
            ->withCount('usages')
            ->search($this->search)
            ->when($this->stockFilter === 'low', fn (Builder $query) =>
                $query->whereColumn('quantity', '<=', 'minimum_quantity')
                    ->where('quantity', '>', 0)
            )
            ->when($this->stockFilter === 'out', fn (Builder $query) =>
                $query->where('quantity', 0)
            )
            ->when($this->stockFilter === 'in_use', fn (Builder $query) =>
                $query->whereHas('usages', fn (Builder $usageQuery) =>
                    $usageQuery->where('status', InventoryUsage::STATUS_IN_USE)
                )
            )
            ->when($this->stockFilter === 'inactive', fn (Builder $query) =>
                $query->where('is_active', false)
            )
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);
    }

    #[Computed]
    public function stats(): array
    {
        $query = InventoryItem::query();

        return [
            'items' => (clone $query)->count(),
            'quantity' => (int) (clone $query)->sum('quantity'),
            'low_stock' => (clone $query)
                ->whereColumn('quantity', '<=', 'minimum_quantity')
                ->where('quantity', '>', 0)
                ->count(),
            'out_of_stock' => (clone $query)->where('quantity', 0)->count(),
            'stock_value' => (float) (clone $query)
                ->selectRaw('COALESCE(SUM(quantity * unit_price), 0) as aggregate')
                ->value('aggregate'),
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPageAndSelection();
    }

    public function updatedStockFilter(): void
    {
        $this->resetPageAndSelection();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
        $this->selected = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll(bool $checked): void
    {
        $this->selected = $checked
            ? $this->items->getCollection()->pluck('id')->map(fn ($id) => (string) $id)->all()
            : [];
    }

    public function sortBy(string $field): void
    {
        if (! in_array($field, ['name', 'quantity', 'unit_price', 'created_at'], true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
        unset($this->items);
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'stockFilter']);
        $this->stockFilter = 'all';
        $this->selected = [];
        $this->selectAll = false;
        $this->resetPage();
        unset($this->items);
    }

    public function startNewUnit(int $itemId, InventoryService $inventoryService): void
    {
        $actor = auth('employee')->user();
        abort_unless($actor, 403);

        try {
            $inventoryService->startNewUnit($itemId, $actor);
            session()->flash('message', 'تم تسجيل بدء الاستخدام. وإذا كانت هناك عبوة سابقة قيد الاستخدام فقد تم إنهاؤها وخصمها من المخزون.');
        } catch (\DomainException $exception) {
            session()->flash('error', $exception->getMessage());
        }

        $this->refreshInventoryData();
    }

    public function finishCurrentUnit(int $itemId, InventoryService $inventoryService): void
    {
        $actor = auth('employee')->user();
        abort_unless($actor, 403);

        try {
            $inventoryService->finishCurrentUnit($itemId, $actor);
            session()->flash('message', 'تم إنهاء الاستخدام وخصم وحدة واحدة من المخزون.');
        } catch (\DomainException $exception) {
            session()->flash('error', $exception->getMessage());
        }

        $this->refreshInventoryData();
    }

    #[Computed]
    public function usageHistory()
    {
        if (! $this->historyItemId) {
            return collect();
        }

        return InventoryUsage::query()
            ->where('inventory_item_id', $this->historyItemId)
            ->with(['startedBy', 'finishedBy'])
            ->latest('started_at')
            ->limit(20)
            ->get();
    }

    public function showHistory(int $itemId): void
    {
        $item = InventoryItem::query()->findOrFail($itemId);

        $this->historyItemId = $item->id;
        $this->historyItemName = $item->name;
        $this->showHistoryModal = true;
        unset($this->usageHistory);
    }

    public function closeHistoryModal(): void
    {
        $this->showHistoryModal = false;
        $this->historyItemId = null;
        $this->historyItemName = '';
        unset($this->usageHistory);
    }

    public function confirmDelete(int $itemId): void
    {
        $this->ensureAdmin();
        InventoryItem::query()->findOrFail($itemId);

        $this->itemToDelete = $itemId;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->itemToDelete = null;
    }

    public function deleteItem(): void
    {
        $this->ensureAdmin();

        if (! $this->itemToDelete) {
            return;
        }

        $item = InventoryItem::query()->findOrFail($this->itemToDelete);

        if ($item->quantity > 0 || $item->usages()->exists() || $item->expenses()->exists()) {
            $this->closeDeleteModal();
            session()->flash('error', 'لا يمكن حذف مورد لديه مخزون أو سجل استخدام أو عملية شراء مرتبطة. يمكنك تعطيله بدلًا من حذفه.');
            return;
        }

        $item->delete();
        $this->closeDeleteModal();
        $this->selected = [];
        $this->selectAll = false;
        $this->refreshInventoryData();

        session()->flash('message', 'تم حذف المورد بنجاح.');
    }

    public function bulkDelete(): void
    {
        $this->ensureAdmin();

        $ids = collect($this->selected)
            ->filter(fn ($id) => is_numeric($id) && (int) $id > 0)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return;
        }

        $items = InventoryItem::query()->whereKey($ids)->get();
        $deletableIds = [];
        $skippedCount = 0;

        foreach ($items as $item) {
            if ($item->quantity === 0 && ! $item->usages()->exists() && ! $item->expenses()->exists()) {
                $deletableIds[] = $item->id;
            } else {
                $skippedCount++;
            }
        }

        $deletedCount = $deletableIds
            ? InventoryItem::query()->whereKey($deletableIds)->delete()
            : 0;

        $this->selected = [];
        $this->selectAll = false;
        $this->refreshInventoryData();

        if ($deletedCount > 0) {
            session()->flash('message', "تم حذف {$deletedCount} مورد بنجاح.");
        }

        if ($skippedCount > 0) {
            session()->flash('error', "تم تخطي {$skippedCount} موردًا لديه مخزون أو سجل استخدام أو عملية شراء مرتبطة.");
        }
    }

    private function ensureAdmin(): void
    {
        abort_unless(auth('employee')->user()?->hasRole('admin'), 403);
    }

    private function resetPageAndSelection(): void
    {
        $this->resetPage();
        $this->selected = [];
        $this->selectAll = false;
        unset($this->items);
    }

    private function refreshInventoryData(): void
    {
        unset($this->items, $this->stats);
        $this->resetPage();
    }
};
