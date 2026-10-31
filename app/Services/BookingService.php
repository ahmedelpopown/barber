<?php

namespace App\Services;

use App\Jobs\SendBookingNotifications;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class BookingService
{
    /** Return a user-facing conflict message, or null if the time is available. */
    public function hasConflict(
        int $employeeId,
        string $date,
        string $time,
        int $userId,
        ?int $ignoreBooking = null
    ): ?string {
        $requestedTime = Carbon::parse($date)->setTimeFromTimeString($time);

        $bookings = Booking::query()
            ->whereDate('date', $date)
            ->where('status', 'pending')
            ->where(function ($query) use ($employeeId, $userId) {
                $query
                    ->where('employee_id', $employeeId)
                    ->orWhere('user_id', $userId);
            })
            ->when($ignoreBooking, fn ($query) => $query->where('id', '!=', $ignoreBooking))
            ->get(['id', 'employee_id', 'user_id', 'date', 'time']);

        foreach ($bookings as $booking) {
            $bookingTime = Carbon::parse($booking->date)
                ->setTimeFromTimeString((string) $booking->time);

            if (
                (int) $booking->employee_id === $employeeId
                && $bookingTime->format('H:i') === $requestedTime->format('H:i')
            ) {
                return 'هذا الموظف لديه حجز بالفعل في نفس الموعد.';
            }

            if (
                (int) $booking->user_id === $userId
                && $bookingTime->format('H:i') === $requestedTime->format('H:i')
            ) {
                return 'لدى العميل حجز بالفعل في نفس الموعد.';
            }

            $minutesApart = abs($bookingTime->diffInMinutes($requestedTime, false));

            if ((int) $booking->employee_id === $employeeId && $minutesApart < 10) {
                return 'يوجد حجز آخر للموظف خلال أقل من 10 دقائق.';
            }

            if ((int) $booking->user_id === $userId && $minutesApart < 10) {
                return 'لدى العميل حجز آخر خلال أقل من 10 دقائق.';
            }
        }

        return null;
    }

    /** Create one booking and attach one or more services atomically. */
    public function create(array $data): Booking
    {
        return DB::transaction(function () use ($data) {
            $serviceIds = $this->normalizeServiceIds($data['service_ids'] ?? []);

            $conflict = $this->hasConflict(
                (int) $data['employee_id'],
                (string) $data['date'],
                (string) $data['time'],
                (int) $data['user_id'],
            );

            if ($conflict !== null) {
                throw ValidationException::withMessages(['time' => $conflict]);
            }

            $booking = Booking::create([
                'user_id' => (int) $data['user_id'],
                'employee_id' => (int) $data['employee_id'],
                'date' => $data['date'],
                'time' => $data['time'],
                'status' => 'pending',
                'turn' => 0,
            ]);

            $booking->services()->sync($serviceIds);

            if (Carbon::parse($booking->date)->isToday()) {
                $this->reorderTodayTurns();
            }

            SendBookingNotifications::dispatch($booking->id)->afterCommit();

            return $booking->fresh(['services', 'user', 'employee']);
        });
    }

    /** Update booking details and its selected services atomically. */
    public function update(Booking $booking, array $data): bool
    {
        return DB::transaction(function () use ($booking, $data) {
            $serviceIds = $this->normalizeServiceIds($data['service_ids'] ?? []);

            $conflict = $this->hasConflict(
                (int) $data['employee_id'],
                (string) $data['date'],
                (string) $data['time'],
                (int) $data['user_id'],
                (int) $booking->id,
            );

            if ($conflict !== null) {
                throw ValidationException::withMessages(['time' => $conflict]);
            }

            $oldDate = $booking->date;

            $updated = $booking->update([
                'user_id' => (int) $data['user_id'],
                'employee_id' => (int) $data['employee_id'],
                'date' => $data['date'],
                'time' => $data['time'],
            ]);

            if (! $updated) {
                return false;
            }

            $booking->services()->sync($serviceIds);

            if (
                Carbon::parse($oldDate)->isToday()
                || Carbon::parse($data['date'])->isToday()
            ) {
                $this->reorderTodayTurns();
            }

            return true;
        });
    }

    public function complete(Booking $booking): bool
    {
        if ($booking->status !== 'pending') {
            return false;
        }

        $bookingDateTime = Carbon::parse($booking->date)
            ->setTimeFromTimeString((string) $booking->time);

        if (now()->lt($bookingDateTime)) {
            return false;
        }

        return DB::transaction(function () use ($booking) {
            $updated = $booking->update([
                'status' => 'completed',
                'turn' => 0,
            ]);

            if (! $updated) {
                return false;
            }

            if (Carbon::parse($booking->date)->isToday()) {
                $this->reorderTodayTurns();
            }

            return true;
        });
    }

    public function cancel(Booking $booking): bool
    {
        if ($booking->status !== 'pending') {
            return false;
        }

        return DB::transaction(function () use ($booking) {
            $updated = $booking->update([
                'status' => 'cancelled',
                'turn' => 0,
            ]);

            if ($updated && Carbon::parse($booking->date)->isToday()) {
                $this->reorderTodayTurns();
            }

            return $updated;
        });
    }

    public function deleteById(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $booking = Booking::findOrFail($id);
            $isToday = Carbon::parse($booking->date)->isToday();
            $deleted = $booking->delete();

            if ($deleted && $isToday) {
                $this->reorderTodayTurns();
            }

            return $deleted;
        });
    }

    public function reorderTodayTurns(): void
    {
        $bookings = Booking::query()
            ->whereDate('date', today())
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderBy('time')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        foreach ($bookings as $index => $booking) {
            $booking->update(['turn' => $index + 1]);
        }

        Booking::query()
            ->whereDate('date', today())
            ->whereIn('status', ['completed', 'cancelled'])
            ->update(['turn' => 0]);
    }

    /** @param array<int, mixed> $serviceIds */
    protected function normalizeServiceIds(array $serviceIds): array
    {
        $validated = Validator::make(
            ['service_ids' => array_values($serviceIds)],
            [
                'service_ids' => ['required', 'array', 'min:1'],
                'service_ids.*' => [
                    'required',
                    'integer',
                    'distinct',
                    'exists:sub_services,id',
                ],
            ],
            [
                'service_ids.required' => 'يجب اختيار خدمة واحدة على الأقل.',
                'service_ids.min' => 'يجب اختيار خدمة واحدة على الأقل.',
                'service_ids.*.exists' => 'إحدى الخدمات المختارة لم تعد متاحة.',
                'service_ids.*.distinct' => 'لا يمكن اختيار الخدمة نفسها أكثر من مرة.',
            ],
        )->validate();

        return array_map('intval', $validated['service_ids']);
    }
}
