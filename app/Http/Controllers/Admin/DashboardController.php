<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\InventoryItem;
use App\Models\Service;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = today()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();

        $stats = [
            'today_bookings' => Booking::query()
                ->whereDate('date', $today)
                ->count(),
            'pending_bookings' => Booking::query()
                ->whereDate('date', $today)
                ->where('status', 'pending')
                ->count(),
            'completed_bookings' => Booking::query()
                ->whereDate('date', $today)
                ->where('status', 'completed')
                ->count(),
            'customers' => User::query()->count(),
            'active_employees' => Employee::query()
                ->where('status', 'active')
                ->count(),
            'services' => Service::query()->count(),
            'month_expenses' => (float) Expense::query()
                ->whereBetween('expense_date', [$monthStart, $monthEnd])
                ->sum('amount'),
            'low_stock_items' => InventoryItem::query()
                ->where('is_active', true)
                ->whereColumn('quantity', '<=', 'minimum_quantity')
                ->count(),
            'inventory_value' => (float) InventoryItem::query()
                ->where('is_active', true)
                ->selectRaw('COALESCE(SUM(quantity * unit_price), 0) AS aggregate')
                ->value('aggregate'),
        ];

        $recentBookings = Booking::query()
            ->with([
                'user:id,name,phone',
                'employee:id,name',
                'services:id,name',
            ])
            ->whereDate('date', $today)
            ->orderBy('time')
            ->limit(8)
            ->get();

        $lowStockItems = InventoryItem::query()
            ->where('is_active', true)
            ->whereColumn('quantity', '<=', 'minimum_quantity')
            ->orderBy('quantity')
            ->orderBy('name')
            ->limit(5)
            ->get(['id', 'name', 'quantity', 'minimum_quantity', 'unit']);

        $recentExpenses = Expense::query()
            ->with('createdBy:id,name')
            ->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->limit(5)
            ->get(['id', 'title', 'category', 'amount', 'expense_date', 'created_by_employee_id']);

        return view('pages.dashboard', compact(
            'stats',
            'recentBookings',
            'lowStockItems',
            'recentExpenses',
        ));
    }
}
