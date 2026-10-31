<?php

use App\Livewire\Forms\BookingForm;
use App\Models\Booking;
use App\Models\Employee;
use App\Models\SubService;
use App\Models\User;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('إنشاء حجز جديد')] class extends Component
{
    public BookingForm $form;

    public string $searchUser = '';
    public bool $userSelected = false;
    public array $users = [];

    public string $searchService = '';
    public array $serviceResults = [];
    public array $selectedServices = [];

    public string $searchEmployee = '';
    public bool $employeeSelected = false;
    public array $employees = [];

    public array $bookedTimes = [];
    public bool $timeAvailable = false;

    public function mount(): void
    {
        $employee = Auth::guard('employee')->user();
        abort_unless($employee, 403);

        $this->form->date = today()->toDateString();

        if ($employee->hasAnyRole(['employee', 'barber'])) {
            $this->form->employee_id = (string) $employee->id;
            $this->employeeSelected = true;
            $this->searchEmployee = $employee->name;
            $this->loadBookedTimes();
        }
    }

    public function updatedSearchUser(): void
    {
        $this->userSelected = false;
        $this->form->user_id = '';
        $this->timeAvailable = false;

        if (mb_strlen(trim($this->searchUser)) < 2) {
            $this->users = [];
            return;
        }

        $this->users = User::query()
            ->where('name', 'like', '%' . trim($this->searchUser) . '%')
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name'])
            ->toArray();
    }

    public function selectUser(int $id): void
    {
        $user = User::query()->findOrFail($id);

        $this->form->user_id = (string) $user->id;
        $this->searchUser = $user->name;
        $this->users = [];
        $this->userSelected = true;

        $this->validateTime();
    }

    public function updatedSearchService(): void
    {
        if (mb_strlen(trim($this->searchService)) < 2) {
            $this->serviceResults = [];
            return;
        }

        $this->serviceResults = SubService::query()
            ->where('name', 'like', '%' . trim($this->searchService) . '%')
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name'])
            ->toArray();
    }

    public function selectService(int $id): void
    {
        $service = SubService::query()->findOrFail($id);
        $selectedIds = array_map('intval', $this->form->service_ids);

        if (! in_array((int) $service->id, $selectedIds, true)) {
            $this->form->service_ids[] = (int) $service->id;
            $this->selectedServices[(int) $service->id] = $service->name;
        }

        $this->searchService = '';
        $this->serviceResults = [];
    }

    public function removeService(int $id): void
    {
        $this->form->service_ids = array_values(array_filter(
            array_map('intval', $this->form->service_ids),
            fn (int $serviceId) => $serviceId !== $id
        ));

        unset($this->selectedServices[$id]);
    }

    public function updatedSearchEmployee(): void
    {
        $currentEmployee = Auth::guard('employee')->user();

        if ($currentEmployee?->hasAnyRole(['employee', 'barber'])) {
            return;
        }

        $this->employeeSelected = false;
        $this->form->employee_id = '';

        if (mb_strlen(trim($this->searchEmployee)) < 2) {
            $this->employees = [];
            $this->loadBookedTimes();
            $this->validateTime();
            return;
        }

        $this->employees = Employee::query()
            ->where('name', 'like', '%' . trim($this->searchEmployee) . '%')
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name'])
            ->toArray();

        $this->loadBookedTimes();
        $this->validateTime();
    }

    public function selectEmployee(int $id): void
    {
        $currentEmployee = Auth::guard('employee')->user();
        abort_unless($currentEmployee?->hasRole('admin'), 403);

        $employee = Employee::query()->findOrFail($id);

        $this->form->employee_id = (string) $employee->id;
        $this->searchEmployee = $employee->name;
        $this->employees = [];
        $this->employeeSelected = true;

        $this->loadBookedTimes();
        $this->validateTime();
    }

    public function updatedFormTime(): void
    {
        $this->validateTime();
    }

    public function updatedFormDate(): void
    {
        // The creation workflow intentionally books for today only.
        $this->form->date = today()->toDateString();
        $this->loadBookedTimes();
        $this->validateTime();
    }

    protected function validateTime(): void
    {
        $this->resetErrorBag('form.time');

        if (! $this->form->employee_id || ! $this->form->user_id || ! $this->form->time) {
            $this->timeAvailable = false;
            return;
        }

        $conflict = app(BookingService::class)->hasConflict(
            (int) $this->form->employee_id,
            today()->toDateString(),
            (string) $this->form->time,
            (int) $this->form->user_id,
        );

        if ($conflict !== null) {
            $this->addError('form.time', $conflict);
            $this->timeAvailable = false;
            return;
        }

        $this->timeAvailable = true;
    }

    protected function loadBookedTimes(): void
    {
        if (! $this->form->employee_id) {
            $this->bookedTimes = [];
            return;
        }

        $this->bookedTimes = Booking::query()
            ->where('employee_id', $this->form->employee_id)
            ->whereDate('date', today())
            ->where('status', 'pending')
            ->orderBy('time')
            ->get(['id', 'time'])
            ->map(fn (Booking $booking) => [
                'id' => $booking->id,
                'time' => (string) $booking->time,
                'formatted' => Carbon::parse($booking->time)->format('h:i A'),
            ])
            ->all();
    }

    public function save()
    {
        $employee = Auth::guard('employee')->user();
        abort_unless($employee, 403);

        if ($employee->hasAnyRole(['employee', 'barber'])) {
            $this->form->employee_id = (string) $employee->id;
        }

        $this->form->date = today()->toDateString();
        $booking = $this->form->store();

        if (! $booking) {
            return null;
        }

        session()->flash('success', 'تم إنشاء الحجز بنجاح.');

        $indexRoute = $employee->hasRole('admin')
            ? 'admin.bookings.index'
            : 'employee.bookings.index';

        return $this->redirect(route($indexRoute));
    }
};
?>

<div dir="rtl" class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
    @php
        $currentEmployee = auth('employee')->user();
        $isAdmin = $currentEmployee?->hasRole('admin') ?? false;
        $indexRoute = $isAdmin ? 'admin.bookings.index' : 'employee.bookings.index';
    @endphp

    <x-ui.page-header
        title="إنشاء حجز جديد"
        description="اختر العميل والخدمات والموظف، ثم حدّد الموعد المتاح."
    />

    <div class="space-y-3">
        <x-ui.alert type="danger" :message="session('error')" />
        <x-ui.alert type="warning" :message="session('warning')" />
    </div>

    <form wire:submit="save" class="space-y-6">
        <x-ui.card title="بيانات الحجز" description="الحقول المطلوبة يجب استكمالها قبل الحفظ.">
            <x-bookings.selection-fields
                :is-admin="$isAdmin"
                :allow-customer-change="true"
                :current-employee-name="$currentEmployee?->name ?? ''"
                :search-user="$searchUser"
                :user-selected="$userSelected"
                :users="$users"
                :search-service="$searchService"
                :service-results="$serviceResults"
                :selected-services="$selectedServices"
                :search-employee="$searchEmployee"
                :employee-selected="$employeeSelected"
                :employees="$employees"
            />

            <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <x-ui.input
                        label="تاريخ الحجز"
                        name="form.date"
                        type="date"
                        :value="$form->date"
                        disabled
                    />
                    <p class="mt-1 text-xs text-gray-500">الحجوزات الجديدة تُسجّل بتاريخ اليوم حسب قواعد النظام الحالية.</p>
                </div>

                <div class="space-y-3">
                    <x-ui.input
                        label="وقت الحجز"
                        name="form.time"
                        type="time"
                        wire:model.live="form.time"
                        :error="$errors->first('form.time')"
                        required
                    />

                    @if ($form->time && $timeAvailable)
                        <x-ui.alert type="success" message="الموعد متاح مبدئيًا." />
                    @endif
                </div>
            </div>
        </x-ui.card>

        <x-ui.card title="المواعيد المحجوزة اليوم" description="المواعيد المعروضة تخص الموظف المحدد والحجوزات قيد الانتظار فقط.">
            @if (count($bookedTimes))
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
                    @foreach ($bookedTimes as $bookedTime)
                        <div wire:key="booked-time-{{ $bookedTime['id'] }}" class="rounded-lg border border-red-200 bg-red-50 p-3 text-center">
                            <p class="font-semibold text-red-800">{{ $bookedTime['formatted'] }}</p>
                            <p class="mt-1 text-xs text-red-600">محجوز</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                    لا توجد حجوزات قيد الانتظار لهذا الموظف اليوم.
                </p>
            @endif
        </x-ui.card>

        <div class="flex flex-wrap justify-start gap-3">
            <x-ui.action :href="route($indexRoute)" variant="secondary">إلغاء</x-ui.action>
            <x-ui.action type="submit" wire:loading.attr="disabled">
                <span wire:loading.remove>إنشاء الحجز</span>
                <span wire:loading>جارٍ الحفظ...</span>
            </x-ui.action>
        </div>
    </form>
</div>
