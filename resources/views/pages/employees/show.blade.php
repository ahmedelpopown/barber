<?php

use App\Models\Booking;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('حجوزات الموظف')] class extends Component {
    use WithPagination;

    public Employee $employee;

    public function mount(Employee $employee): void
    {
        $this->employee = $employee;
    }

    #[Computed]
    public function bookings(): LengthAwarePaginator
    {
        return Booking::query()
            ->with([
                'user:id,name',
                'employee:id,name',
                'services:id,name,duration',
            ])
            ->whereBelongsTo($this->employee, 'employee')
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->paginate(15)
            ->through(fn (Booking $booking): Booking => $this->addTimingDetails($booking));
    }

    private function addTimingDetails(Booking $booking): Booking
    {
        $start = Carbon::parse($booking->date)
            ->setTimeFromTimeString((string) $booking->time);

        $duration = max(0, (int) $booking->services->sum('duration'));
        $end = $start->copy()->addMinutes($duration);
        $now = now();

        $booking->setAttribute('date_display', $start->format('Y-m-d'));
        $booking->setAttribute('start_time_display', $start->format('H:i'));
        $booking->setAttribute('end_time_display', $end->format('H:i'));

        if ($duration === 0) {
            $booking->setAttribute('display_status', 'مدة غير محددة');
            $booking->setAttribute('progress', 0);
            $booking->setAttribute('remaining_display', 'غير متاح');
            $booking->setAttribute('progress_color', 'bg-gray-400');

            return $booking;
        }

        if ($now->lt($start)) {
            $booking->setAttribute('display_status', 'في الانتظار');
            $booking->setAttribute('progress', 0);
            $booking->setAttribute(
                'remaining_display',
                $now->diffForHumans($start, [
                    'syntax' => Carbon::DIFF_ABSOLUTE,
                    'parts' => 2,
                ])
            );
            $booking->setAttribute('progress_color', 'bg-slate-400');

            return $booking;
        }

        if ($now->lt($end)) {
            $totalSeconds = $duration * 60;
            $elapsedSeconds = max(0, (int) $start->diffInSeconds($now));
            $progress = (int) round(($elapsedSeconds / $totalSeconds) * 100);

            $booking->setAttribute('display_status', 'جاري الآن');
            $booking->setAttribute('progress', min(99, max(0, $progress)));
            $booking->setAttribute(
                'remaining_display',
                $now->diffForHumans($end, [
                    'syntax' => Carbon::DIFF_ABSOLUTE,
                    'parts' => 2,
                ])
            );
            $booking->setAttribute('progress_color', 'bg-emerald-500');

            return $booking;
        }

        $booking->setAttribute('display_status', 'منتهٍ');
        $booking->setAttribute('progress', 100);
        $booking->setAttribute('remaining_display', 'انتهى');
        $booking->setAttribute('progress_color', 'bg-blue-600');

        return $booking;
    }
};
?>

<div dir="rtl" class="space-y-6 px-4 py-6 sm:px-6 lg:px-8">
    <x-ui.page-header
        :title="'حجوزات الموظف: ' . $employee->name"
        description="عرض الحجوزات المرتبطة بهذا الموظف وتقدم كل حجز."
    >
        <x-slot:actions>
            <x-ui.action variant="secondary" href="{{ route('admin.employees.index') }}">
                العودة للموظفين
            </x-ui.action>
        </x-slot:actions>
    </x-ui.page-header>

    <div wire:poll.10s>
        <x-ui.card title="سجل الحجوزات" description="تتحدث حالة التقدم كل 10 ثوانٍ أثناء بقاء الصفحة مفتوحة.">
            <div class="-mx-4 overflow-x-auto sm:mx-0">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-right text-xs font-semibold text-gray-600">رقم</th>
                            <th scope="col" class="px-3 py-3 text-right text-xs font-semibold text-gray-600">العميل</th>
                            <th scope="col" class="px-3 py-3 text-right text-xs font-semibold text-gray-600">الموظف</th>
                            <th scope="col" class="px-3 py-3 text-right text-xs font-semibold text-gray-600">الخدمات</th>
                            <th scope="col" class="px-3 py-3 text-right text-xs font-semibold text-gray-600">المدة</th>
                            <th scope="col" class="px-3 py-3 text-right text-xs font-semibold text-gray-600">التاريخ</th>
                            <th scope="col" class="px-3 py-3 text-right text-xs font-semibold text-gray-600">البداية / النهاية</th>
                            <th scope="col" class="px-3 py-3 text-right text-xs font-semibold text-gray-600">الوقت المتبقي</th>
                            <th scope="col" class="min-w-36 px-3 py-3 text-right text-xs font-semibold text-gray-600">التقدم</th>
                            <th scope="col" class="px-3 py-3 text-right text-xs font-semibold text-gray-600">الحالة</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($this->bookings as $booking)
                            <tr wire:key="booking-{{ $booking->id }}" class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">{{ $booking->id }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-gray-900">{{ $booking->user?->name ?? 'غير متاح' }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">{{ $booking->employee?->name ?? 'غير متاح' }}</td>
                                <td class="min-w-48 px-3 py-4 text-sm text-gray-600">{{ $booking->services->pluck('name')->join('، ') ?: 'لا توجد خدمات' }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">{{ (int) $booking->services->sum('duration') }} دقيقة</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">{{ $booking->date_display }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">
                                    <span>{{ $booking->start_time_display }}</span>
                                    <span class="text-gray-400"> / </span>
                                    <span>{{ $booking->end_time_display }}</span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">{{ $booking->remaining_display }}</td>
                                <td class="px-3 py-4">
                                    <div
                                        class="h-2.5 w-full overflow-hidden rounded-full bg-gray-100"
                                        role="progressbar"
                                        aria-label="نسبة تقدم الحجز {{ $booking->id }}"
                                        aria-valuemin="0"
                                        aria-valuemax="100"
                                        aria-valuenow="{{ $booking->progress }}"
                                    >
                                        <div class="h-full rounded-full transition-all duration-300 {{ $booking->progress_color }}" style="width: {{ $booking->progress }}%"></div>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">{{ $booking->progress }}%</p>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    @if ($booking->progress === 100)
                                      <x-ui.badge :status="$booking->status" />
                                    @elseif ($booking->progress === 0)
                                        <x-ui.badge :status="$booking->status" />
                                    @else
                                        <x-ui.badge :status="$booking->status" />
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-4 py-12 text-center">
                                    <p class="text-sm font-medium text-gray-900">لا توجد حجوزات لهذا الموظف</p>
                                    <p class="mt-1 text-sm text-gray-500">ستظهر الحجوزات هنا عندما يتم تسجيلها.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 border-t border-gray-100 pt-4">
                {{ $this->bookings->links() }}
            </div>
        </x-ui.card>
    </div>
</div>
