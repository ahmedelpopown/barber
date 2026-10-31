<?php

use App\Models\Expense;
use App\Services\ExpenseInventoryService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('المصروفات')] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $category = 'all';
    public string $paymentMethod = 'all';
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $sortField = 'expense_date';
    public string $sortDirection = 'desc';
    public int $perPage = 10;

    public bool $showDeleteModal = false;
    public ?int $expenseToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => 'all'],
        'paymentMethod' => ['except' => 'all'],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'sortField' => ['except' => 'expense_date'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount(): void
    {
        $this->ensureAdmin();
    }

    protected function ensureAdmin(): void
    {
        abort_unless(auth('employee')->user()?->hasRole('admin'), 403);
    }

    #[Computed]
    public function expenses()
    {
        $allowedSortFields = ['title', 'category', 'amount', 'expense_date', 'payment_method', 'created_at'];
        $sortField = in_array($this->sortField, $allowedSortFields, true) ? $this->sortField : 'expense_date';
        $sortDirection = $this->sortDirection === 'asc' ? 'asc' : 'desc';
        $perPage = in_array($this->perPage, [10, 25, 50], true) ? $this->perPage : 10;
        $category = array_key_exists($this->category, Expense::CATEGORIES) ? $this->category : 'all';
        $paymentMethod = array_key_exists($this->paymentMethod, Expense::PAYMENT_METHODS) ? $this->paymentMethod : 'all';
        $dateFrom = $this->validDate($this->dateFrom);
        $dateTo = $this->validDate($this->dateTo);

        return Expense::query()
            ->with(['createdBy:id,name', 'inventoryItem:id,name,unit'])
            ->when(trim($this->search) !== '', function (Builder $query): void {
                $term = trim($this->search);
                $query->where(function (Builder $nested) use ($term): void {
                    $nested->where('title', 'like', "%{$term}%")
                        ->orWhere('vendor', 'like', "%{$term}%")
                        ->orWhere('receipt_reference', 'like', "%{$term}%")
                        ->orWhere('notes', 'like', "%{$term}%");
                });
            })
            ->when($category !== 'all', fn (Builder $query) => $query->where('category', $category))
            ->when($paymentMethod !== 'all', fn (Builder $query) => $query->where('payment_method', $paymentMethod))
            ->when($dateFrom, fn (Builder $query) => $query->whereDate('expense_date', '>=', $dateFrom))
            ->when($dateTo, fn (Builder $query) => $query->whereDate('expense_date', '<=', $dateTo))
            ->orderBy($sortField, $sortDirection)
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    #[Computed]
    public function stats(): array
    {
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();

        return [
            'today_total' => (float) Expense::query()->whereDate('expense_date', $today)->sum('amount'),
            'month_total' => (float) Expense::query()->whereBetween('expense_date', [$monthStart, $monthEnd])->sum('amount'),
            'total_count' => Expense::query()->count(),
            'month_count' => Expense::query()->whereBetween('expense_date', [$monthStart, $monthEnd])->count(),
        ];
    }

    protected function validDate(string $value): ?string
    {
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        [$year, $month, $day] = array_map('intval', explode('-', $value));

        return checkdate($month, $day, $year) ? $value : null;
    }

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedCategory(): void { $this->resetPage(); }
    public function updatedPaymentMethod(): void { $this->resetPage(); }
    public function updatedDateFrom(): void { $this->resetPage(); }
    public function updatedDateTo(): void { $this->resetPage(); }
    public function updatedPerPage(): void { $this->resetPage(); }

    public function sortBy(string $field): void
    {
        if (! in_array($field, ['title', 'category', 'amount', 'expense_date', 'payment_method', 'created_at'], true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = $field === 'expense_date' ? 'desc' : 'asc';
        }

        $this->resetPage();
        unset($this->expenses);
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'dateFrom', 'dateTo']);
        $this->category = 'all';
        $this->paymentMethod = 'all';
        $this->perPage = 10;
        $this->sortField = 'expense_date';
        $this->sortDirection = 'desc';
        $this->resetPage();
        unset($this->expenses);
    }

    public function confirmDelete(int $expenseId): void
    {
        $this->ensureAdmin();
        Expense::query()->findOrFail($expenseId);
        $this->expenseToDelete = $expenseId;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->expenseToDelete = null;
    }

    public function deleteExpense(ExpenseInventoryService $inventoryPurchases): void
    {
        $this->ensureAdmin();

        if (! $this->expenseToDelete) {
            return;
        }

        $hadInventoryPurchase = false;

        try {
            DB::transaction(function () use ($inventoryPurchases, &$hadInventoryPurchase): void {
                $expense = Expense::query()
                    ->lockForUpdate()
                    ->findOrFail($this->expenseToDelete);

                $hadInventoryPurchase = $expense->inventory_item_id !== null;
                $inventoryPurchases->reversePurchase(
                    $expense->inventory_item_id !== null ? (int) $expense->inventory_item_id : null,
                    $expense->inventory_quantity !== null ? (int) $expense->inventory_quantity : null,
                );

                $expense->delete(); // Soft delete keeps the financial audit trail.
            });
        } catch (\DomainException $exception) {
            $this->closeDeleteModal();
            session()->flash('error', $exception->getMessage());
            return;
        }

        $this->closeDeleteModal();
        unset($this->expenses, $this->stats);
        session()->flash(
            'message',
            $hadInventoryPurchase
                ? 'تم حذف المصروف من العرض وخصم الكمية المرتبطة به من المخزون.'
                : 'تم حذف المصروف من العرض بنجاح.'
        );
    }
};
