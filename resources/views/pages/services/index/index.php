<?php

use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('الخدمات')] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $name = '';
    public string $sup_service = '';

    public string $sortField = 'name';
    public string $sortDirection = 'asc';

    public int $perPage = 10;

    public array $selected = [];
    public bool $selectAll = false;

    public bool $showDeleteModal = false;
    public ?int $serviceToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'name' => ['except' => ''],
        'sup_service' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    #[Computed]
    public function services()
    {
        $sortField = in_array($this->sortField, ['name'], true)
            ? $this->sortField
            : 'name';

        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';
        $perPage = in_array($this->perPage, [10, 25, 50], true) ? $this->perPage : 10;

        return Service::query()
            ->with('subServices')
            ->when(trim($this->search) !== '', function (Builder $query) {
                $term = trim($this->search);

                $query->where(function (Builder $nested) use ($term) {
                    $nested
                        ->where('name', 'like', "%{$term}%")
                        ->orWhereHas('subServices', fn (Builder $subQuery) =>
                            $subQuery->where('name', 'like', "%{$term}%")
                        );
                });
            })
            ->when(trim($this->name) !== '', fn (Builder $query) =>
                $query->where('name', 'like', '%' . trim($this->name) . '%')
            )
            ->when(trim($this->sup_service) !== '', fn (Builder $query) =>
                $query->whereHas('subServices', fn (Builder $subQuery) =>
                    $subQuery->where('name', 'like', '%' . trim($this->sup_service) . '%')
                )
            )
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->selectAll = false;
        $this->selected = [];
    }

    public function updatedName(): void
    {
        $this->resetPage();
        $this->selectAll = false;
        $this->selected = [];
    }

    public function updatedSupService(): void
    {
        $this->resetPage();
        $this->selectAll = false;
        $this->selected = [];
    }

    public function sortBy(string $field): void
    {
        if (! in_array($field, ['name'], true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
        unset($this->services);
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'name', 'sup_service']);
        $this->selected = [];
        $this->selectAll = false;
        $this->resetPage();
        unset($this->services);
    }

    public function updatedSelectAll(bool $checked): void
    {
        $this->selected = $checked
            ? $this->services->getCollection()->pluck('id')->map(fn ($id) => (string) $id)->all()
            : [];
    }

    public function confirmDelete(int $serviceId): void
    {
        Service::query()->findOrFail($serviceId);

        $this->serviceToDelete = $serviceId;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->serviceToDelete = null;
    }

    public function deleteService(): void
    {
        if (! $this->serviceToDelete) {
            return;
        }

        $service = Service::query()->findOrFail($this->serviceToDelete);

        if ($service->subServices()->whereHas('bookings')->exists()) {
            $this->closeDeleteModal();

            session()->flash(
                'error',
                'لا يمكن حذف خدمة لديها خدمات فرعية مستخدمة في حجوزات محفوظة.'
            );

            return;
        }

        $service->delete();

        $this->closeDeleteModal();
        $this->selected = [];
        $this->selectAll = false;
        unset($this->services);

        session()->flash('message', 'تم حذف الخدمة بنجاح.');

        if ($this->services->isEmpty() && $this->getPage() > 1) {
            $this->resetPage();
        }
    }

    public function bulkDelete(): void
    {
        $ids = collect($this->selected)
            ->filter(fn ($id) => is_numeric($id) && (int) $id > 0)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return;
        }

        $blockedIds = Service::query()
            ->whereKey($ids)
            ->whereHas('subServices.bookings')
            ->pluck('id');

        $deletableIds = $ids->diff($blockedIds)->values();
        $deletedCount = $deletableIds->isNotEmpty()
            ? Service::query()->whereKey($deletableIds)->delete()
            : 0;

        $this->selected = [];
        $this->selectAll = false;
        unset($this->services);

        if ($blockedIds->isNotEmpty()) {
            session()->flash(
                'error',
                'تم تخطي ' . $blockedIds->count() . ' خدمة مرتبطة بحجوزات محفوظة لحماية سجل الحجوزات.'
            );
        }

        if ($deletedCount > 0) {
            session()->flash('message', "تم حذف {$deletedCount} خدمة بنجاح.");
        }

        if ($this->services->isEmpty() && $this->getPage() > 1) {
            $this->resetPage();
        }
    }
};
