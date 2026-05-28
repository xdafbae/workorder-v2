<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'wo_number',
        'device_id',
        'reporter_id',
        'technician_id',
        'status',
        'description',
        'processed_at',
        'completed_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'processed_at' => 'datetime',
            'completed_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'wo_number';
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function symptoms()
    {
        return $this->belongsToMany(Symptom::class, 'wo_symptoms');
    }

    public function indications()
    {
        return $this->belongsToMany(DamageIndication::class, 'wo_indications')
            ->withPivot(['source', 'score'])
            ->withTimestamps();
    }

    public function updates()
    {
        return $this->hasMany(WoUpdate::class)->latest();
    }
}
