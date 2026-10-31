<?php

namespace App\Livewire\Forms;

use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Form;

class BookingForm extends Form
{
    public ?Booking $booking = null;

    public $user_id = '';

    public $employee_id = '';

    /** @var array<int, int|string> */
    public array $service_ids = [];

    public $date = '';

    public $time = '';

    protected function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'service_ids' => ['required', 'array', 'min:1'],
            'service_ids.*' => ['required', 'integer', 'distinct', 'exists:sub_services,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time' => ['required', 'date_format:H:i'],
        ];
    }

    public function setBooking(Booking $booking): void
    {
        $booking->loadMissing('services');

        $this->booking = $booking;
        $this->user_id = (string) $booking->user_id;
        $this->employee_id = (string) $booking->employee_id;
        $this->date = $booking->date instanceof \DateTimeInterface
            ? $booking->date->format('Y-m-d')
            : substr((string) $booking->date, 0, 10);
        $this->time = substr((string) $booking->time, 0, 5);
        $this->service_ids = $booking->services
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    public function store(): ?Booking
    {
        // Creating a booking from this workflow is for today only.
        $this->date = today()->toDateString();
        $this->validate();

        $bookingService = app(BookingService::class);

        try {
            return $bookingService->create([
                'user_id' => (int) $this->user_id,
                'employee_id' => (int) $this->employee_id,
                'date' => (string) $this->date,
                'time' => (string) $this->time,
                'service_ids' => array_map('intval', $this->service_ids),
            ]);
        } catch (ValidationException $exception) {
            $this->addServiceErrors($exception);
            return null;
        }
    }

    public function update(): bool
    {
        abort_unless($this->booking instanceof Booking, 404);

        $employee = Auth::guard('employee')->user();

        abort_unless(
            $employee && (
                $employee->hasRole('admin')
                || (int) $this->booking->employee_id === (int) $employee->id
            ),
            403
        );

        $this->validate();

        $isAdmin = $employee->hasRole('admin');
        $userId = $isAdmin ? (int) $this->user_id : (int) $this->booking->user_id;
        $employeeId = $isAdmin ? (int) $this->employee_id : (int) $this->booking->employee_id;

        $bookingService = app(BookingService::class);

        try {
            return $bookingService->update($this->booking, [
                'user_id' => $userId,
                'employee_id' => $employeeId,
                'date' => (string) $this->date,
                'time' => (string) $this->time,
                'service_ids' => array_map('intval', $this->service_ids),
            ]);
        } catch (ValidationException $exception) {
            $this->addServiceErrors($exception);
            return false;
        }
    }

    private function addServiceErrors(ValidationException $exception): void
    {
        foreach ($exception->errors() as $field => $messages) {
            $field = preg_replace('/^form\./', '', (string) $field);

            if (str_starts_with($field, 'service_ids.')) {
                $field = 'service_ids';
            }

            if (! in_array($field, ['user_id', 'employee_id', 'service_ids', 'date', 'time'], true)) {
                $field = 'time';
            }

            $this->addError($field, (string) ($messages[0] ?? 'تعذر إكمال العملية.'));
        }
    }
}
