<div dir="rtl" class="space-y-6 px-4 py-6 sm:px-6 lg:px-8">
    <x-ui.page-header
        title="العملاء"
        description="إدارة بيانات العملاء ووسائل التواصل ومراجعة سجل الحجوزات المرتبط بكل عميل."
    >
        <x-slot:actions>
            <x-ui.action href="{{ route('admin.users.create') }}" wire:navigate>
                <span aria-hidden="true">+</span>
                إضافة عميل
            </x-ui.action>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="space-y-3">
        <x-ui.alert type="danger" :message="session('error')" />
        <x-ui.alert type="success" :message="session('message')" />
    </div>

    {{-- Overview --}}
    <div aria-label="ملخص العملاء" class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <x-ui.card>
            <p class="text-sm font-medium text-gray-500">إجمالي العملاء</p>
            <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900">
                {{ number_format($this->userStats['total']) }}
            </p>
        </x-ui.card>

        <x-ui.card>
            <p class="text-sm font-medium text-gray-500">عملاء جدد هذا الشهر</p>
            <p class="mt-2 text-3xl font-bold tracking-tight text-indigo-700">
                {{ number_format($this->userStats['newThisMonth']) }}
            </p>
        </x-ui.card>

        <x-ui.card>
            <p class="text-sm font-medium text-gray-500">لديهم حجوزات محفوظة</p>
            <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900">
                {{ number_format($this->userStats['withBookings']) }}
            </p>
        </x-ui.card>
    </div>

    {{-- Search and filters --}}
    <x-ui.card
        title="البحث والتصفية"
        description="استخدم البحث العام أو اجمع بين الفلاتر للوصول إلى العميل المطلوب."
    >
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <x-ui.input
                label="بحث عام"
                name="search"
                type="search"
                placeholder="الاسم أو البريد أو الهاتف..."
                wire:model.live.debounce.300ms="search"
            />

            <x-ui.input
                label="الاسم"
                name="name"
                placeholder="اسم العميل"
                wire:model.live.debounce.300ms="name"
            />

            <x-ui.input
                label="البريد الإلكتروني"
                name="email"
                type="email"
                placeholder="name@example.com"
                wire:model.live.debounce.300ms="email"
            />

            <x-ui.input
                label="رقم الهاتف"
                name="phone"
                type="tel"
                placeholder="رقم الهاتف"
                wire:model.live.debounce.300ms="phone"
            />
        </div>

        <div class="mt-4 flex flex-col gap-3 border-t border-gray-100 pt-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-wrap items-center gap-2">
                <x-ui.action variant="secondary" wire:click="resetFilters">
                    إعادة تعيين الفلاتر
                </x-ui.action>

                @if (count($selected) > 0)
                    <x-ui.action
                        variant="danger"
                        wire:click="bulkDelete"
                        wire:confirm="هل تريد حذف العملاء المحددين؟ سيتم تخطي أي عميل لديه حجوزات محفوظة."
                        wire:loading.attr="disabled"
                        wire:target="bulkDelete"
                    >
                        <span wire:loading.remove wire:target="bulkDelete">
                            حذف المحدد ({{ count($selected) }})
                        </span>
                        <span wire:loading wire:target="bulkDelete">جارٍ الحذف...</span>
                    </x-ui.action>
                @endif
            </div>

            <div class="flex items-center gap-2 text-sm text-gray-600">
                <label for="users-per-page">عدد النتائج</label>
                <select
                    id="users-per-page"
                    wire:model.live="perPage"
                    class="rounded-lg border-gray-300 py-2 pe-8 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
    </x-ui.card>

    {{-- Users table --}}
    <x-ui.card>
        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="text-base font-semibold text-gray-900">قائمة العملاء</h2>
                <p class="mt-1 text-sm text-gray-500">يمكنك ترتيب النتائج من خلال عناوين الأعمدة.</p>
            </div>

            @if (count($selected) > 0)
                <x-ui.badge variant="info">تم تحديد {{ count($selected) }} عميل</x-ui.badge>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-right text-xs font-semibold text-gray-600">
                    <tr>
                        <th scope="col" class="w-12 px-4 py-3">
                            <input
                                type="checkbox"
                                wire:model.live="selectAll"
                                aria-label="تحديد جميع العملاء في الصفحة الحالية"
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            >
                        </th>
                        <th scope="col" class="px-4 py-3">
                            <button type="button" wire:click="sortBy('name')" class="inline-flex items-center gap-2 hover:text-indigo-700">
                                الاسم
                                @if ($sortField === 'name')
                                    <span aria-hidden="true">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="px-4 py-3">
                            <button type="button" wire:click="sortBy('email')" class="inline-flex items-center gap-2 hover:text-indigo-700">
                                البريد الإلكتروني
                                @if ($sortField === 'email')
                                    <span aria-hidden="true">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="px-4 py-3">
                            <button type="button" wire:click="sortBy('phone')" class="inline-flex items-center gap-2 hover:text-indigo-700">
                                الهاتف
                                @if ($sortField === 'phone')
                                    <span aria-hidden="true">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="whitespace-nowrap px-4 py-3 text-center">الحجوزات</th>
                        <th scope="col" class="whitespace-nowrap px-4 py-3">
                            <button type="button" wire:click="sortBy('created_at')" class="inline-flex items-center gap-2 hover:text-indigo-700">
                                تاريخ التسجيل
                                @if ($sortField === 'created_at')
                                    <span aria-hidden="true">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="px-4 py-3 text-center">الإجراءات</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($this->users as $user)
                        <tr wire:key="user-{{ $user->id }}" class="transition-colors hover:bg-gray-50">
                            <td class="px-4 py-4 align-middle">
                                <input
                                    type="checkbox"
                                    value="{{ $user->id }}"
                                    wire:model.live="selected"
                                    aria-label="تحديد {{ $user->name }}"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                >
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 font-medium text-gray-900">
                                {{ $user->name }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-gray-600">
                                <a href="mailto:{{ $user->email }}" class="hover:text-indigo-700 hover:underline">
                                    {{ $user->email }}
                                </a>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-gray-600">
                                {{ $user->phone ?: 'غير مسجل' }}
                            </td>
                            <td class="px-4 py-4 text-center">
                                <x-ui.badge :variant="$user->bookings_count > 0 ? 'info' : 'neutral'">
                                    {{ $user->bookings_count }}
                                </x-ui.badge>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-gray-600">
                                {{ $user->created_at?->format('Y-m-d') ?? 'غير متاح' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <x-ui.action
                                        href="{{ route('admin.users.edit', $user) }}"
                                        wire:navigate
                                        variant="secondary"
                                        size="sm"
                                    >
                                        تعديل
                                    </x-ui.action>
                                    <x-ui.action
                                        variant="danger"
                                        size="sm"
                                        wire:click="confirmDelete({{ $user->id }})"
                                    >
                                        حذف
                                    </x-ui.action>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="mx-auto flex max-w-sm flex-col items-center gap-2">
                                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-500" aria-hidden="true">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <circle cx="11" cy="11" r="7"></circle>
                                            <path stroke-linecap="round" d="m16 16 4 4"></path>
                                        </svg>
                                    </span>
                                    <p class="font-semibold text-gray-800">لا يوجد عملاء مطابقون</p>
                                    <p class="text-sm text-gray-500">جرّب تغيير كلمات البحث أو إعادة تعيين الفلاتر.</p>
                                    <x-ui.action variant="secondary" size="sm" wire:click="resetFilters">
                                        إعادة تعيين الفلاتر
                                    </x-ui.action>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5 border-t border-gray-100 pt-4">
            {{ $this->users->links() }}
        </div>
    </x-ui.card>

    <x-ui.confirm-modal
        :show="$showDeleteModal"
        title="حذف العميل"
        message="هل أنت متأكد من حذف هذا العميل؟ لا يمكن حذف عميل لديه حجوزات محفوظة."
    >
        <x-slot:actions>
            <x-ui.action
                variant="danger"
                wire:click="deleteUser"
                wire:loading.attr="disabled"
                wire:target="deleteUser"
            >
                <span wire:loading.remove wire:target="deleteUser">تأكيد الحذف</span>
                <span wire:loading wire:target="deleteUser">جارٍ الحذف...</span>
            </x-ui.action>

            <x-ui.action
                variant="secondary"
                wire:click="closeDeleteModal"
                wire:loading.attr="disabled"
                wire:target="deleteUser"
            >
                إلغاء
            </x-ui.action>
        </x-slot:actions>
    </x-ui.confirm-modal>
</div>
