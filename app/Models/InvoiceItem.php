<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = ['invoice_id', 'title', 'nominal', 'quantity'];

    protected $casts = [
        'nominal' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::saved(function ($model) {
            $model->invoice->calculateTotals();
        });

        static::deleted(function ($model) {
            $model->invoice->calculateTotals();
        });
    }
}
