<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'name',
        'logo',
        'address',
        'phone',
    ];

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function getLogoUrlAttribute(): ?string
    {
        // Untuk browser / asset()
        if (empty($this->logo)) return null;
        // Kalau di DB sudah tersimpan "logos/xxx.png"
        return asset('storage/' . ltrim($this->logo, '/'));
    }

    public function getLogoPathForPdfAttribute(): ?string
    {
        // Untuk DOMPDF (butuh path lokal)
        if (empty($this->logo)) return null;

        $relative = 'storage/' . ltrim($this->logo, '/'); // public/storage/logos/xxx.png
        $fullPath = public_path($relative);

        return file_exists($fullPath) ? $fullPath : null;
    }
}
