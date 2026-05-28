<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'name',
        'type',
        'model',
        'serial_number',
        'inventory_number',
        'barcode_code',
        'status',
        'purchased_at',
    ];

    protected function casts(): array
    {
        return ['purchased_at' => 'date'];
    }

    protected static function booted(): void
    {
        static::creating(function (Device $device) {
            if (blank($device->barcode_code)) {
                $device->barcode_code = self::generateBarcodeCode();
            }
        });
    }

    public static function generateBarcodeCode(): string
    {
        do {
            $code = 'WO-QR-'.Str::upper(Str::random(8));
        } while (self::query()->where('barcode_code', $code)->exists());

        return $code;
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }
}
