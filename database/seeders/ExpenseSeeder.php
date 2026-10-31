<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Expense;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        

        $employeeId = Employee::query()->value('id');

        if (! $employeeId) {
            $this->command?->warn('ExpenseSeeder skipped: create employees before seeding expenses.');
            return;
        }

        $samples = [
            ['title' => 'شراء كريمات ومستلزمات', 'category' => 'supplies', 'amount' => 850.00, 'payment_method' => 'cash', 'vendor' => 'مورد مستلزمات حلاقة'],
            ['title' => 'فاتورة الكهرباء', 'category' => 'utilities', 'amount' => 620.50, 'payment_method' => 'bank_transfer', 'vendor' => 'شركة الكهرباء'],
            ['title' => 'صيانة كرسي الحلاقة', 'category' => 'maintenance', 'amount' => 450.00, 'payment_method' => 'cash', 'vendor' => 'فني صيانة'],
            ['title' => 'فاتورة الإنترنت', 'category' => 'internet', 'amount' => 390.00, 'payment_method' => 'wallet', 'vendor' => 'شركة الاتصالات'],
            ['title' => 'شراء أدوات جديدة', 'category' => 'equipment', 'amount' => 1275.00, 'payment_method' => 'card', 'vendor' => 'محل الأدوات'],
        ];

        foreach ($samples as $index => $sample) {
            Expense::query()->firstOrCreate(
                [
                    'title' => $sample['title'],
                ],
                array_merge($sample, [
                    'expense_date' => now()->subDays($index * 3)->toDateString(),
                    'created_by_employee_id' => $employeeId,
                    'notes' => 'بيانات تجريبية للتطوير فقط.',
                ])
            );
        }
    }
}
