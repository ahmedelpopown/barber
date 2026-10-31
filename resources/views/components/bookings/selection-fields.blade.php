@props([
    'isAdmin' => false,
    'allowCustomerChange' => true,
    'currentEmployeeName' => '',
    'customerName' => '',
    'searchUser' => '',
    'userSelected' => false,
    'users' => [],
    'searchService' => '',
    'serviceResults' => [],
    'selectedServices' => [],
    'searchEmployee' => '',
    'employeeSelected' => false,
    'employees' => [],
])

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <div class="space-y-2">
        @if ($allowCustomerChange)
            <x-ui.autocomplete
                label="العميل"
                search-model="searchUser"
                :search="$searchUser"
                :options="$users"
                select-method="selectUser"
                :selected="$userSelected"
                placeholder="ابحث باسم العميل..."
                empty-message="لا يوجد عميل بهذا الاسم."
            />
        @else
            <label class="block text-sm font-medium text-gray-700">العميل</label>
            <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-medium text-gray-800">
                {{ $customerName ?: 'العميل غير متاح' }}
            </div>
        @endif

        @error('form.user_id')
            <p class="text-sm text-red-600" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-2">
        <x-ui.autocomplete
            label="إضافة خدمة"
            search-model="searchService"
            :search="$searchService"
            :options="$serviceResults"
            select-method="selectService"
            placeholder="ابحث باسم الخدمة..."
            empty-message="لا توجد خدمات مطابقة."
        />

        @if (count($selectedServices))
            <div class="flex flex-wrap gap-2 pt-1">
                @foreach ($selectedServices as $serviceId => $serviceName)
                    <span wire:key="selected-service-{{ $serviceId }}" class="inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1.5 text-sm text-indigo-800">
                        {{ $serviceName }}
                        <button
                            type="button"
                            wire:click="removeService({{ $serviceId }})"
                            aria-label="إزالة {{ $serviceName }}"
                            class="font-bold text-indigo-700 hover:text-red-600"
                        >
                            ×
                        </button>
                    </span>
                @endforeach
            </div>
        @else
            <p class="text-xs text-gray-500">يمكنك اختيار أكثر من خدمة للحجز الواحد.</p>
        @endif

        @error('form.service_ids')
            <p class="text-sm text-red-600" role="alert">{{ $message }}</p>
        @enderror
        @error('form.service_ids.*')
            <p class="text-sm text-red-600" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-2">
        @if ($isAdmin)
            <x-ui.autocomplete
                label="الموظف المسؤول"
                search-model="searchEmployee"
                :search="$searchEmployee"
                :options="$employees"
                select-method="selectEmployee"
                :selected="$employeeSelected"
                placeholder="ابحث عن الموظف..."
                empty-message="لا يوجد موظفون بهذا الاسم."
            />
        @else
            <label class="block text-sm font-medium text-gray-700">الموظف المسؤول</label>
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3">
                <p class="text-xs text-green-700">الموظف الحالي</p>
                <p class="mt-1 font-semibold text-green-900">{{ $currentEmployeeName ?: 'الموظف غير متاح' }}</p>
            </div>
        @endif

        @error('form.employee_id')
            <p class="text-sm text-red-600" role="alert">{{ $message }}</p>
        @enderror
    </div>
</div>
