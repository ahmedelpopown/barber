<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    public const CATEGORIES = [
        'supplies' => 'شراء مستلزمات',
        'equipment' => 'معدات وأدوات',
        'rent' => 'إيجار',
        'utilities' => 'كهرباء ومياه',
        'salaries' => 'رواتب وأجور',
        'maintenance' => 'صيانة',
        'marketing' => 'تسويق وإعلانات',
        'transport' => 'مواصلات ونقل',
        'internet' => 'إنترنت واتصالات',
        'hospitality' => 'ضيافة',
        'other' => 'مصروفات أخرى',
    ];

    public const PAYMENT_METHODS = [
        'cash' => 'نقدًا',
        'card' => 'بطاقة بنكية',
        'bank_transfer' => 'تحويل بنكي',
        'wallet' => 'محفظة إلكترونية',
        'other' => 'أخرى',
    ];

    protected $fillable = [
        'title',
        'category',
        'amount',
        'expense_date',
        'payment_method',
        'vendor',
        'receipt_reference',
        'notes',
        'receipt_images',
        'inventory_item_id',
        'inventory_quantity',
        'created_by_employee_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expense_date' => 'date',
            'receipt_images' => 'array',
            'inventory_item_id' => 'integer',
            'inventory_quantity' => 'integer',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by_employee_id');
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? 'غير محدد';
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? 'غير محدد';
    }
}
