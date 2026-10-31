<?php

use App\Models\Employee;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('الموظفون')] class extends Component {
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $status = '';

    public string $sortField = 'name';

    public string $sortDirection = 'asc';

    public array $selected = [];

    public bool $selectAll = false;

    public bool $showDeleteModal = false;

    public ?int $employeeToDelete = null;

    private const SORTABLE_FIELDS = ['name', 'email', 'phone', 'status', 'created_at'];

    #[Computed]
    public function employees()
    {
        $search = trim($this->search);

        $status = in_array($this->status, ['active', 'inactive'], true)
            ? $this->status
            : null;

        $sortField = in_array($this->sortField, self::SORTABLE_FIELDS, true)
            ? $this->sortField
            : 'name';

        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        return Employee::query()
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($status !== null, fn (Builder $query) => $query->where('status', $status))
            ->orderBy($sortField, $sortDirection)
            ->paginate(10);
    }

    public function updatedSearch(): void
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

        $this->selected = $this->employees
            ->getCollection()
            ->pluck('id')
            ->map(fn ($id): string => (string) $id)
            ->all();
    }

    public function updatedSelected(): void
    {
        $this->syncSelectAllState();
    }

    public function updatedPage(): void
    {
        $this->syncSelectAllState();
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
        $this->syncSelectAllState();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->status = '';
        $this->resetPage();
        $this->clearSelection();
    }

    public function confirmDelete(int $employeeId): void
    {
        $employee = Employee::query()->findOrFail($employeeId);

        $this->employeeToDelete = (int) $employee->getKey();
        $this->showDeleteModal = true;
    }

    public function deleteEmployee(): void
    {
        if ($this->employeeToDelete === null) {
            return;
        }

        $employee = Employee::query()->findOrFail($this->employeeToDelete);

        if ($employee->bookings()->exists()) {
            $this->showDeleteModal = false;
            $this->employeeToDelete = null;

            session()->flash(
                'error',
                'لا يمكن حذف موظف لديه حجوزات مسجلة. غيّر حالته إلى "لا يعمل" للحفاظ على سجل الحجوزات.'
            );

            return;
        }

        $employee->delete();

        $this->showDeleteModal = false;
        $this->employeeToDelete = null;
        $this->clearSelection();
        $this->resetPage();

        session()->flash('message', 'تم حذف الموظف بنجاح.');
    }

    public function bulkDelete(): void
    {
        $validated = $this->validate([
            'selected' => ['required', 'array', 'min:1'],
            'selected.*' => ['required', 'integer', 'distinct', 'exists:employees,id'],
        ]);

        $selectedIds = collect($validated['selected'])->map(fn ($id) => (int) $id);
        $blockedCount = Employee::query()
            ->whereKey($selectedIds)
            ->whereHas('bookings')
            ->count();

        $deletedCount = Employee::query()
            ->whereKey($selectedIds)
            ->whereDoesntHave('bookings')
            ->delete();

        $this->clearSelection();
        $this->resetPage();

        if ($blockedCount > 0) {
            session()->flash(
                'error',
                "تم تخطي {$blockedCount} موظف/موظفين لارتباطهم بحجوزات محفوظة. غيّر حالتهم إلى لا يعمل بدلًا من حذفهم."
            );
        }

        if ($deletedCount > 0) {
            session()->flash('message', "تم حذف {$deletedCount} موظف/موظفين بنجاح.");
        }
    }

    private function clearSelection(): void
    {
        $this->selected = [];
        $this->selectAll = false;
    }

    private function syncSelectAllState(): void
    {
        $pageIds = $this->employees
            ->getCollection()
            ->pluck('id')
            ->map(fn ($id): string => (string) $id)
            ->all();

        $selectedIds = array_map('strval', $this->selected);

        $this->selectAll = $pageIds !== []
            && count(array_diff($pageIds, $selectedIds)) === 0;
    }
};
?>

<div dir="rtl" class="space-y-6 px-4 py-6 sm:px-6 lg:px-8">
    <x-ui.page-header
        title="الموظفون"
        description="قائمة الموظفين وبيانات التواصل وحالات العمل."
    >
        <x-slot:actions>
            <x-ui.action href="{{ route('admin.employees.create') }}">
                إضافة موظف
            </x-ui.action>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="space-y-3">
        <x-ui.alert type="danger" :message="session('error')" />
        <x-ui.alert type="success" :message="session('message')" />
    </div>

    <x-ui.card title="البحث والتصفية" description="ابحث بالاسم أو البريد الإلكتروني أو رقم الهاتف.">
        <div class="grid grid-cols-1 items-end gap-4 md:grid-cols-[minmax(0,1fr)_12rem_auto]">
            <x-ui.input
                label="بحث"
                name="search"
                placeholder="اكتب بيانات الموظف..."
                wire:model.live.debounce.300ms="search"
            />

            <x-ui.select label="حالة الموظف" name="status" wire:model.live="status">
                <option value="">كل الحالات</option>
                <option value="active">يعمل</option>
                <option value="inactive">لا يعمل</option>
            </x-ui.select>

            <x-ui.action variant="secondary" wire:click="resetFilters">
                إعادة ضبط
            </x-ui.action>
        </div>
    </x-ui.card>

    @if (count($selected) > 0)
        <div class="flex flex-col gap-3 rounded-xl border border-indigo-200 bg-indigo-50 p-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm font-medium text-indigo-900">
                تم تحديد {{ count($selected) }} موظف/موظفين.
            </p>

            <x-ui.action
                variant="danger"
                size="sm"
                wire:click="bulkDelete"
                wire:confirm="هل أنت متأكد من حذف الموظفين المحددين؟"
            >
                حذف المحدد
            </x-ui.action>
        </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="w-12 px-4 py-3 text-right">
                            <input
                                type="checkbox"
                                aria-label="تحديد كل الموظفين في الصفحة الحالية"
                                wire:model.live="selectAll"
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            >
                        </th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">
                            <button type="button" wire:click="sortBy('name')" class="inline-flex items-center gap-2 hover:text-gray-900">
                                الاسم
                                @if ($sortField === 'name')
                                    <span aria-hidden="true">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">البريد الإلكتروني</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">الهاتف</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">الحالة</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">الإجراءات</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($this->employees as $employee)
                        <tr wire:key="employee-{{ $employee->id }}" class="transition-colors hover:bg-gray-50">
                            <td class="px-4 py-4">
                                <input
                                    type="checkbox"
                                    aria-label="تحديد {{ $employee->name }}"
                                    wire:model.live="selected"
                                    value="{{ $employee->id }}"
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                >
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-gray-900">
                                {{ $employee->name }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-600">
                                {{ $employee->email }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-600">
                                {{ $employee->phone ?: '—' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-sm">
                                <x-ui.badge :status="$employee->status" />
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <x-ui.action
                                        size="sm"
                                        variant="secondary"
                                        href="{{ route('admin.employees.edit', $employee) }}"
                                    >
                                        تعديل
                                    </x-ui.action>

                                    <x-ui.action
                                        size="sm"
                                        variant="danger"
                                        wire:click="confirmDelete({{ $employee->id }})"
                                    >
                                        حذف
                                    </x-ui.action>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <p class="text-sm font-medium text-gray-900">لا يوجد موظفون مطابقون</p>
                                <p class="mt-1 text-sm text-gray-500">غيّر كلمات البحث أو أعد ضبط الفلاتر.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 px-4 py-3 sm:px-6">
            {{ $this->employees->links() }}
        </div>
    </div>

    <x-ui.confirm-modal
        :show="$showDeleteModal"
        title="حذف الموظف"
        message="هل أنت متأكد من حذف هذا الموظف؟ لا يمكن التراجع عن هذا الإجراء."
    >
        <x-slot:actions>
            <x-ui.action
                variant="danger"
                wire:click="deleteEmployee"
                wire:loading.attr="disabled"
                wire:target="deleteEmployee"
            >
                <span wire:loading.remove wire:target="deleteEmployee">تأكيد الحذف</span>
                <span wire:loading wire:target="deleteEmployee">جارٍ الحذف...</span>
            </x-ui.action>

            <x-ui.action
                variant="secondary"
                wire:click="$set('showDeleteModal', false)"
                wire:loading.attr="disabled"
                wire:target="deleteEmployee"
            >
                إلغاء
            </x-ui.action>
        </x-slot:actions>
    </x-ui.confirm-modal>
</div>
