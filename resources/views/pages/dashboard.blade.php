@extends('admin.layout.app')

@section('content')
<div dir="rtl" class="space-y-6 px-3 py-4 sm:px-5 lg:px-6">
    <header class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-amber-700">نظرة عامة على الصالون</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900 sm:text-3xl">لوحة التحكم</h1>
            <p class="mt-2 text-sm text-slate-500">ملخص الحجوزات والتشغيل والمصروفات والمخزون حتى {{ now()->translatedFormat('l، d F Y') }}.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.bookings.create') }}" class="inline-flex items-center gap-2 rounded-lg !bg-amber-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm   hover:!bg-amber-800 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
                <i class="fas fa-calendar-plus" aria-hidden="true"></i>
                حجز جديد
            </a>
            <a href="{{ route('admin.expenses.create') }}" class=" ptn-primary inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700  hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-300">
                <i class="fas fa-receipt" aria-hidden="true"></i>
                تسجيل مصروف
            </a>
        </div>
    </header>

    <div aria-label="المؤشرات الرئيسية" class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @php
            $cards = [
                ['label' => 'حجوزات اليوم', 'value' => $stats['today_bookings'], 'hint' => 'كل الحالات', 'icon' => 'fa-calendar-day', 'tone' => 'amber'],
                ['label' => 'حجوزات في الانتظار', 'value' => $stats['pending_bookings'], 'hint' => 'تحتاج متابعة', 'icon' => 'fa-clock', 'tone' => 'blue'],
                ['label' => 'حجوزات مكتملة اليوم', 'value' => $stats['completed_bookings'], 'hint' => 'تم إنهاؤها', 'icon' => 'fa-check-circle', 'tone' => 'green'],
                ['label' => 'موظفون نشطون', 'value' => $stats['active_employees'], 'hint' => 'حسابات فعّالة', 'icon' => 'fa-user-tie', 'tone' => 'purple'],
            ];
        @endphp
        @foreach ($cards as $card)
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-slate-500">{{ $card['label'] }}</p>
                        <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($card['value']) }}</p>
                        <p class="mt-2 text-xs text-slate-500">{{ $card['hint'] }}</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-700" aria-hidden="true">
                        <i class="fas {{ $card['icon'] }} text-lg"></i>
                    </span>
                </div>
            </article>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="ملخص الإدارة">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">العملاء المسجلون</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">{{ number_format($stats['customers']) }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">الخدمات الرئيسية</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">{{ number_format($stats['services']) }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">مصروفات الشهر الحالي</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">{{ number_format($stats['month_expenses'], 2) }} <span class="text-sm font-semibold text-slate-500">ج.م</span></p>
            <a href="{{ route('admin.expenses.index') }}" class="mt-2 inline-flex text-sm font-semibold text-amber-800 hover:text-amber-900">عرض المصروفات</a>
        </article>
        <article class="rounded-2xl border {{ $stats['low_stock_items'] > 0 ? 'border-rose-200 bg-rose-50/60' : 'border-slate-200 bg-white' }} p-5 shadow-sm">
            <p class="text-sm text-slate-500">موارد وصلت للحد الأدنى</p>
            <p class="mt-2 text-2xl font-bold {{ $stats['low_stock_items'] > 0 ? 'text-rose-700' : 'text-slate-900' }}">{{ number_format($stats['low_stock_items']) }}</p>
            <p class="mt-2 text-xs text-slate-500">قيمة المخزون النشط: {{ number_format($stats['inventory_value'], 2) }} ج.م</p>
        </article>
    </div>

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="font-bold text-slate-900">حجوزات اليوم</h2>
                    <p class="mt-1 text-sm text-slate-500">المواعيد مرتبة حسب وقت الحجز.</p>
                </div>
                <a href="{{ route('admin.bookings.index') }}" class="text-sm font-semibold text-amber-800 hover:text-amber-900">كل الحجوزات</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-right text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-500">
                        <tr>
                            <th scope="col" class="whitespace-nowrap px-5 py-3">الوقت</th>
                            <th scope="col" class="whitespace-nowrap px-5 py-3">العميل</th>
                            <th scope="col" class="whitespace-nowrap px-5 py-3">الحلاق</th>
                            <th scope="col" class="whitespace-nowrap px-5 py-3">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php
                            $statusLabels = ['pending' => 'في الانتظار', 'completed' => 'مكتمل', 'cancelled' => 'ملغي'];
                            $statusClasses = ['pending' => 'bg-amber-100 text-amber-800', 'completed' => 'bg-emerald-100 text-emerald-800', 'cancelled' => 'bg-slate-100 text-slate-600'];
                        @endphp
                        @forelse ($recentBookings as $booking)
                            <tr class="hover:bg-slate-50/70">
                                <td class="whitespace-nowrap px-5 py-3 font-semibold text-slate-800">{{ \Illuminate\Support\Carbon::parse($booking->time)->format('h:i A') }}</td>
                                <td class="whitespace-nowrap px-5 py-3">
                                    <div class="font-semibold text-slate-800">{{ $booking->user?->name ?? 'عميل محذوف' }}</div>
                                    <div class="mt-1 text-xs text-slate-500">{{ $booking->user?->phone ?? 'بدون رقم هاتف' }}</div>
                                </td>
                                <td class="whitespace-nowrap px-5 py-3 text-slate-600">{{ $booking->employee?->name ?? 'غير محدد' }}</td>
                                <td class="whitespace-nowrap px-5 py-3"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses[$booking->status] ?? 'bg-slate-100 text-slate-600' }}">{{ $statusLabels[$booking->status] ?? $booking->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-10 text-center text-slate-500">لا توجد حجوزات مسجلة لهذا اليوم.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="font-bold text-slate-900">تنبيهات المخزون</h2>
                    <p class="mt-1 text-sm text-slate-500">الموارد عند الحد الأدنى أو أقل.</p>
                </div>
                <a href="{{ route('admin.resources.index') }}" class="text-sm font-semibold text-amber-800 hover:text-amber-900">المخزون</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($lowStockItems as $item)
                    <div class="flex items-center justify-between gap-3 px-5 py-4">
                        <div class="min-w-0">
                            <p class="truncate font-semibold text-slate-800">{{ $item->name }}</p>
                            <p class="mt-1 text-xs text-slate-500">الحد الأدنى {{ $item->minimum_quantity }} {{ $item->unit }}</p>
                        </div>
                        <span class="shrink-0 rounded-lg bg-rose-100 px-3 py-1.5 text-sm font-bold text-rose-800">{{ $item->quantity }} {{ $item->unit }}</span>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-sm text-slate-500">
                        <i class="fas fa-check-circle mb-2 text-2xl text-emerald-600" aria-hidden="true"></i>
                        <p>لا توجد تنبيهات مخزون حاليًا.</p>
                    </div>
                @endforelse
            </div>
        </article>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
            <div>
                <h2 class="font-bold text-slate-900">آخر المصروفات</h2>
                <p class="mt-1 text-sm text-slate-500">آخر خمس عمليات مصروف محفوظة وغير محذوفة.</p>
            </div>
            <a href="{{ route('admin.expenses.index') }}" class="text-sm font-semibold text-amber-800 hover:text-amber-900">عرض الكل</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-right text-sm">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500">
                    <tr>
                        <th scope="col" class="whitespace-nowrap px-5 py-3">المصروف</th>
                        <th scope="col" class="whitespace-nowrap px-5 py-3">التاريخ</th>
                        <th scope="col" class="whitespace-nowrap px-5 py-3">المسجل بواسطة</th>
                        <th scope="col" class="whitespace-nowrap px-5 py-3">القيمة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recentExpenses as $expense)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-3 font-semibold text-slate-800">{{ $expense->title }}</td>
                            <td class="whitespace-nowrap px-5 py-3 text-slate-600">{{ $expense->expense_date?->format('Y-m-d') }}</td>
                            <td class="whitespace-nowrap px-5 py-3 text-slate-600">{{ $expense->createdBy?->name ?? 'غير محدد' }}</td>
                            <td class="whitespace-nowrap px-5 py-3 font-bold text-slate-900">{{ number_format((float) $expense->amount, 2) }} ج.م</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-10 text-center text-slate-500">لا توجد مصروفات مسجلة حتى الآن.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
