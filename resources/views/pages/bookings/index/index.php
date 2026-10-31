<?php

use App\Models\Booking;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('الحجوزات')] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $time = '';
    public string $status = '';
    public string $sortField = 'turn';
    public string $sortDirection = 'asc';
    public int $perPage = 10;
    public array $selected = [];
    public bool $selectAll = false;
    public bool $showDeleteModal = false;
    public ?int $bookingToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'time' => ['except' => ''],
        'status' => ['except' => ''],
        'sortField' => ['except' => 'turn'],
        'sortDirection' => ['except' => 'asc'],
    ];

    #[Computed]
    public function bookings()
    {
        $query = $this->bookingQuery()
            ->with([
                'user:id,name,email,phone',
                'employee:id,name',
                'services:id,name',
            ])
            ->whereDate('bookings.date', today());

        $term = trim($this->search);

        if ($term !== '') {
            $query->whereHas('user', function (Builder $userQuery) use ($term) {
                $userQuery->where(function (Builder $nestedQuery) use ($term) {
                    $nestedQuery
                        ->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%");
                });
            });
        }

        if ($this->status !== '') {
            $query->where('bookings.status', $this->status);
        }

        if ($this->time !== '') {
            $query->whereTime('bookings.time', $this->time);
        }

        $direction = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        switch ($this->sortField) {
            case 'customer_name':
                $query
                    ->orderBy(
                        User::query()
                            ->select('name')
                            ->whereColumn('users.id', 'bookings.user_id'),
                        $direction
                    )
                    ->orderBy('bookings.time');
                break;

            case 'time':
                $query->orderBy('bookings.time', $direction);
                break;

            case 'status':
                $query
                    ->orderBy('bookings.status', $direction)
                    ->orderBy('bookings.time');
                break;

            case 'turn':
            default:
                // Keep active queue entries before completed/cancelled entries (turn = 0).
                $query
                    ->orderByRaw('CASE WHEN bookings.turn = 0 THEN 1 ELSE 0 END')
                    ->orderBy('bookings.turn', 'asc')
                    ->orderBy('bookings.time', 'asc');
                break;
        }

        $allowedPerPage = in_array($this->perPage, [10, 25, 50], true)
            ? $this->perPage
            : 10;

        return $query->paginate($allowedPerPage);
    }

    protected function bookingQuery(): Builder
    {
        $employee = auth('employee')->user();

        abort_unless($employee, 403);

        $query = Booking::query();

        if (! $employee->hasRole('admin')) {
            $query->where('bookings.employee_id', $employee->id);
        }

        return $query;
    }

    public function sortBy(string $field): void
    {
        $allowedFields = ['customer_name', 'time', 'status', 'turn'];

        if (! in_array($field, $allowedFields, true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
        $this->selected = [];
        $this->selectAll = false;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->clearSelection();
    }

    public function updatedTime(): void
    {
        $this->resetPage();
        $this->clearSelection();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
        $this->clearSelection();
    }

    public function updatedSelectAll(bool $value): void
    {
        if (! $value) {
            $this->selected = [];
            return;
        }

        $this->selected = $this->bookings
            ->getCollection()
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'time', 'status']);
        $this->resetPage();
        $this->clearSelection();
    }

    public function bulkDelete(BookingService $bookingService): void
    {
        // A client can tamper with the public selected IDs. Only delete rows
        // that are actually present on the currently displayed page.
        $visibleIds = $this->bookings->getCollection()->pluck('id')->map(fn ($id) => (string) $id);
        $selectedIds = collect($this->selected)
            ->map(fn ($id) => (string) $id)
            ->intersect($visibleIds)
            ->values();

        $ids = $this->bookingQuery()
            ->whereDate('bookings.date', today())
            ->whereIn('bookings.id', $selectedIds)
            ->pluck('bookings.id');

        foreach ($ids as $id) {
            $bookingService->deleteById((int) $id);
        }

        $this->clearSelection();
        $this->resetPage();

        session()->flash('success', 'تم حذف الحجوزات المحددة بنجاح.');
    }

    public function completed(int $bookingId, BookingService $bookingService): void
    {
        $booking = $this->bookingQuery()->findOrFail($bookingId);

        if (! $bookingService->complete($booking)) {
            session()->flash('error', 'لا يمكن إكمال هذا الحجز. تأكد أن الحجز ما زال قيد الانتظار وأن موعده قد بدأ.');
            return;
        }

        session()->flash('success', 'تم إكمال الحجز بنجاح.');
    }

    public function cancelled(int $bookingId, BookingService $bookingService): void
    {
        $booking = $this->bookingQuery()->findOrFail($bookingId);

        if (! $bookingService->cancel($booking)) {
            session()->flash('error', 'لا يمكن إلغاء هذا الحجز بعد تغيير حالته.');
            return;
        }

        session()->flash('success', 'تم إلغاء الحجز بنجاح.');
    }

    public function confirmDelete(int $bookingId): void
    {
        $this->bookingQuery()->findOrFail($bookingId);

        $this->bookingToDelete = $bookingId;
        $this->showDeleteModal = true;
    }

    public function deleteBooking(BookingService $bookingService): void
    {
        abort_unless($this->bookingToDelete, 404);

        $booking = $this->bookingQuery()->findOrFail($this->bookingToDelete);
        $bookingService->deleteById((int) $booking->id);

        $this->closeDeleteModal();
        $this->resetPage();

        session()->flash('success', 'تم حذف الحجز بنجاح.');
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->bookingToDelete = null;
    }

    private function clearSelection(): void
    {
        $this->selected = [];
        $this->selectAll = false;
    }
};
?>