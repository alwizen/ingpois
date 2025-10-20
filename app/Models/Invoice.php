<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Invoice extends Model
{
    protected $fillable = [
        'company_id',
        'invoice_date',
        'invoice_number',
        'subtotal',
        'use_ppn',
        'ppn_percentage',
        'ppn_amount',
        'total',
        'due_date',
        'recipient_address',
        'recipient',
        'status',
        'transaction_number',
        'paid_at',
        'note'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'ppn_percentage' => 'decimal:2',
        'ppn_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'use_ppn' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    // public static function generateInvoiceNumber(): string
    // {
    //     $date = now()->format('Ymd');
    //     $count = static::whereDate('created_at', today())->count() + 1;
    //     return 'INV-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    // }

    public static function generateInvoiceNumber(): string
    {
        do {
            $randomNumber = str_pad(mt_rand(0, 999999999), 9, '0', STR_PAD_LEFT);
            $invoiceNumber = $randomNumber;
        } while (static::where('invoice_number', $invoiceNumber)->exists());

        return $invoiceNumber;
    }

    public static function generateTransactionNumber(): string
    {
        return str_pad(random_int(0, 9999999999), 11, '0', STR_PAD_LEFT);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->invoice_number)) {
                $model->invoice_number = static::generateInvoiceNumber();
            }
        });

        static::saved(function ($model) {
            $model->calculateTotals();
        });
    }

    public function calculateTotals(): void
    {
        $subtotal = $this->items->sum(fn($item) => $item->nominal * $item->quantity);
        $this->subtotal = $subtotal;

        if ($this->use_ppn) {
            $ppnAmount = ($subtotal * $this->ppn_percentage) / 100;
            $this->ppn_amount = $ppnAmount;
            $this->total = $subtotal + $ppnAmount;
        } else {
            $this->ppn_amount = 0;
            $this->total = $subtotal;
        }

        $this->saveQuietly();
    }
}
