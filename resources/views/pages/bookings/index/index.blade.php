<div dir="rtl" class="space-y-6 px-4 py-6 sm:px-6 lg:px-8">
    @php
        $currentEmployee = auth('employee')->user();
        $isAdmin = $currentEmployee?->hasRole('admin') ?? false;
        $createRoute = $isAdmin ? 'admin.bookings.create' : 'employee.bookings.create';
        $statusLabels = [
            'pending' => 'في الانتظار',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
        ];
        $statusClasses = [
            'pending' => 'bg-amber-100 text-amber-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-gray-100 text-gray-700',
        ];
    @endphp

    <x-ui.page-header
        title="الحجوزات"
        description="متابعة حجوزات اليوم وبيانات العملاء والخدمات والمواعيد وحالة كل حجز."
    >
        <x-slot:actions>
            <x-ui.action :href="route($createRoute)">
                إضافة حجز جديد
            </x-ui.action>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="space-y-3">
        <x-ui.alert type="success" :message="session('success') ?? session('message')" />
        <x-ui.alert type="danger" :message="session('error')" />
        <x-ui.alert type="warning" :message="session('warning')" />
    </div>

    <x-ui.card>
        <div class="grid grid-cols-1 items-end gap-4 md:grid-cols-3">
            <x-ui.input
                label="البحث عن عميل"
                name="search"
                type="search"
                placeholder="الاسم أو البريد أو رقم الهاتف..."
                wire:model.live.debounce.300ms="search"
            />

            <x-ui.select
                label="حالة الحجز"
                name="status"
                placeholder="كل الحالات"
                wire:model.live="status"
            >
                <option value="pending">في الانتظار</option>
                <option value="completed">مكتمل</option>
                <option value="cancelled">ملغي</option>
            </x-ui.select>

            <div class="flex flex-wrap items-end gap-2">
                <x-ui.input
                    label="وقت الحجز"
                    name="time"
                    type="time"
                    wire:model.live="time"
                />
                <x-ui.action variant="secondary" wire:click="resetFilters">
                    إعادة ضبط
                </x-ui.action>
            </div>
        </div>
    </x-ui.card>

    @if (count($selected) > 0)
        <div class="flex flex-col gap-3 rounded-xl border border-indigo-100 bg-indigo-50 p-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm font-medium text-indigo-800">
                تم تحديد {{ count($selected) }} حجز.
            </p>

            <x-ui.action
                variant="danger"
                wire:click="bulkDelete"
                wire:confirm="هل أنت متأكد من حذف الحجوزات المحددة؟"
                wire:loading.attr="disabled"
            >
                حذف المحدد
            </x-ui.action>
        </div>
    @endif

    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-right text-gray-700">
                    <tr>
                        <th scope="col" class="w-12 px-4 py-3">
                            <input
                                type="checkbox"
                                wire:model.live="selectAll"
                                aria-label="تحديد حجوزات الصفحة الحالية"
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            >
                        </th>
                        <th scope="col" class="px-4 py-3 font-semibold">
                            <button type="button" wire:click="sortBy('customer_name')" class="inline-flex items-center gap-1 hover:text-indigo-700">
                                العميل
                                @if ($sortField === 'customer_name')
                                    <span aria-hidden="true">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="px-4 py-3 font-semibold">البريد الإلكتروني</th>
                        <th scope="col" class="px-4 py-3 font-semibold">الهاتف</th>
                        <th scope="col" class="px-4 py-3 font-semibold">الخدمات</th>
                        <th scope="col" class="px-4 py-3 font-semibold">
                            <button type="button" wire:click="sortBy('time')" class="inline-flex items-center gap-1 hover:text-indigo-700">
                                موعد الحجز
                                @if ($sortField === 'time')
                                    <span aria-hidden="true">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="px-4 py-3 font-semibold">الحالة</th>
                        <th scope="col" class="px-4 py-3 text-center font-semibold">الإجراءات</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($this->bookings as $booking)
                        <tr wire:key="booking-{{ $booking->id }}" class="transition hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <input
                                    type="checkbox"
                                    wire:model.live="selected"
                                    value="{{ $booking->id }}"
                                    aria-label="تحديد الحجز رقم {{ $booking->id }}"
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                >
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 font-medium text-gray-900">
                                {{ $booking->user?->name ?? 'عميل غير متاح' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-gray-600">
                                {{ $booking->user?->email ?? '—' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-gray-600">
                                {{ $booking->user?->phone ?? '—' }}
                            </td>
                            <td class="min-w-48 px-4 py-3 text-gray-600">
                                {{ $booking->services->pluck('name')->join('، ') ?: 'لا توجد خدمات' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-gray-600">
                                {{ \Carbon\Carbon::parse($booking->time)->format('h:i A') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses[$booking->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $statusLabels[$booking->status] ?? $booking->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap items-center justify-center gap-2">
                                    @if ($booking->status === 'pending')
                                        <x-ui.action
                                            size="sm"
                                            variant="secondary"
                                            wire:click="cancelled({{ $booking->id }})"
                                            wire:confirm="هل أنت متأكد من إلغاء هذا الحجز؟"
                                            wire:loading.attr="disabled"
                                        >
                                            إلغاء
                                        </x-ui.action>

                                        <x-ui.action
                                            size="sm"
                                            wire:click="completed({{ $booking->id }})"
                                            wire:confirm="تأكيد إكمال هذا الحجز؟"
                                            wire:loading.attr="disabled"
                                        >
                                            إكمال
                                        </x-ui.action>
                                    @endif

                                    <x-ui.action
                                        size="sm"
                                        variant="danger"
                                        wire:click="confirmDelete({{ $booking->id }})"
                                    >
                                        حذف
                                    </x-ui.action>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <p class="font-medium text-gray-700">لا توجد حجوزات مطابقة.</p>
                                <p class="mt-1 text-sm text-gray-500">جرّب تغيير البحث أو الفلاتر.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5 border-t border-gray-100 pt-4">
            {{ $this->bookings->links() }}
        </div>
    </x-ui.card>

    @if ($showDeleteModal)
        <x-ui.confirm-modal
            title="حذف الحجز"
            message="هل أنت متأكد من حذف هذا الحجز؟ لا يمكن التراجع عن هذه العملية."
            confirm-text="حذف الحجز"
            confirm-action="deleteBooking"
            cancel-action="closeDeleteModal"
        />
    @endif
</div>
