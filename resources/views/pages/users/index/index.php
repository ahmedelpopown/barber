<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('العملاء')] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $name = '';
    public string $email = '';
    public string $phone = '';

    public string $sortField = 'name';
    public string $sortDirection = 'asc';
    public int $perPage = 10;

    /** @var array<int, string> */
    public array $selected = [];
    public bool $selectAll = false;

    public bool $showDeleteModal = false;
    public ?int $userToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'name' => ['except' => ''],
        'email' => ['except' => ''],
        'phone' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    private const SORTABLE_FIELDS = [
        'name',
        'email',
        'phone',
        'created_at',
    ];

    private const PER_PAGE_OPTIONS = [10, 25, 50];

    #[Computed]
    public function users()
    {
        $sortField = in_array($this->sortField, self::SORTABLE_FIELDS, true)
            ? $this->sortField
            : 'name';

        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        $perPage = in_array($this->perPage, self::PER_PAGE_OPTIONS, true)
            ? $this->perPage
            : 10;

        return User::query()
            ->withCount('bookings')
            ->when(trim($this->search) !== '', function ($query): void {
                $term = '%' . trim($this->search) . '%';

                $query->where(function ($nestedQuery) use ($term): void {
                    $nestedQuery
                        ->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('phone', 'like', $term);
                });
            })
            ->when(trim($this->name) !== '', fn ($query) =>
                $query->where('name', 'like', '%' . trim($this->name) . '%'))
            ->when(trim($this->email) !== '', fn ($query) =>
                $query->where('email', 'like', '%' . trim($this->email) . '%'))
            ->when(trim($this->phone) !== '', fn ($query) =>
                $query->where('phone', 'like', '%' . trim($this->phone) . '%'))
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);
    }

    #[Computed]
    public function userStats(): array
    {
        return [
            'total' => User::query()->count(),
            'newThisMonth' => User::query()
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count(),
            'withBookings' => User::query()->whereHas('bookings')->count(),
        ];
    }

    public function sortBy(string $field): void
    {
        if (! in_array($field, self::SORTABLE_FIELDS, true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
        $this->clearSelection();
    }

    public function updatedSearch(): void
    {
        $this->resetListState();
    }

    public function updatedName(): void
    {
        $this->resetListState();
    }

    public function updatedEmail(): void
    {
        $this->resetListState();
    }

    public function updatedPhone(): void
    {
        $this->resetListState();
    }

    public function updatedPerPage(): void
    {
        if (! in_array($this->perPage, self::PER_PAGE_OPTIONS, true)) {
            $this->perPage = 10;
        }

        $this->resetListState();
    }

    public function updatedPaginators($page, $pageName): void
    {
        $this->clearSelection();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'name', 'email', 'phone']);
        $this->resetListState();
    }

    public function updatedSelectAll(bool $checked): void
    {
        if (! $checked) {
            $this->selected = [];

            return;
        }

        $this->selected = $this->users
            ->getCollection()
            ->pluck('id')
            ->map(fn ($id): string => (string) $id)
            ->all();
    }

    public function updatedSelected(): void
    {
        $pageIds = $this->users
            ->getCollection()
            ->pluck('id')
            ->map(fn ($id): string => (string) $id)
            ->all();

        $selectedIds = array_map('strval', $this->selected);

        $this->selectAll = $pageIds !== []
            && count(array_diff($pageIds, $selectedIds)) === 0;
    }

    public function confirmDelete(int $userId): void
    {
        User::query()->findOrFail($userId);

        $this->userToDelete = $userId;
        $this->showDeleteModal = true;
    }

    public function deleteUser(): void
    {
        if ($this->userToDelete === null) {
            return;
        }

        $user = User::query()->findOrFail($this->userToDelete);

        if ($user->bookings()->exists()) {
            $this->closeDeleteModal();

            session()->flash(
                'error',
                'لا يمكن حذف هذا العميل لوجود حجوزات محفوظة مرتبطة به.'
            );

            return;
        }

        $user->delete();

        $this->closeDeleteModal();
        $this->clearSelection();
        $this->resetPage();

        session()->flash('message', 'تم حذف العميل بنجاح.');
    }

    public function bulkDelete(): void
    {
        $validated = $this->validate([
            'selected' => ['required', 'array', 'min:1'],
            'selected.*' => ['required', 'integer', 'distinct', 'exists:users,id'],
        ]);

        // Bulk actions are limited to the rows currently visible on this page.
        $visibleIds = $this->users
            ->getCollection()
            ->pluck('id')
            ->map(fn ($id): int => (int) $id);

        $selectedIds = collect($validated['selected'])
            ->map(fn ($id): int => (int) $id)
            ->intersect($visibleIds)
            ->unique()
            ->values();

        if ($selectedIds->isEmpty()) {
            $this->clearSelection();

            session()->flash('error', 'حدد عميلًا واحدًا على الأقل من الصفحة الحالية.');

            return;
        }

        $deletedCount = 0;
        $blockedCount = 0;

        DB::transaction(function () use ($selectedIds, &$deletedCount, &$blockedCount): void {
            $users = User::query()
                ->whereKey($selectedIds)
                ->lockForUpdate()
                ->get();

            foreach ($users as $user) {
                if ($user->bookings()->exists()) {
                    $blockedCount++;

                    continue;
                }

                if ($user->delete()) {
                    $deletedCount++;
                }
            }
        });

        $this->clearSelection();
        $this->resetPage();

        if ($blockedCount > 0) {
            session()->flash(
                'error',
                "تم تخطي {$blockedCount} عميل/عملاء لوجود حجوزات محفوظة مرتبطة بهم."
            );
        }

        if ($deletedCount > 0) {
            session()->flash('message', "تم حذف {$deletedCount} عميل/عملاء بنجاح.");
        }

        if ($deletedCount === 0 && $blockedCount === 0) {
            session()->flash('error', 'لم يتم حذف أي عميل. حدّث الصفحة وحاول مرة أخرى.');
        }
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->userToDelete = null;
    }

    private function resetListState(): void
    {
        $this->resetPage();
        $this->clearSelection();
    }

    private function clearSelection(): void
    {
        $this->selected = [];
        $this->selectAll = false;
    }
};
