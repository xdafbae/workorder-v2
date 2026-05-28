<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WoUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_order_id',
        'user_id',
        'status',
        'final_diagnosis',
        'notes',
        'photo_path',
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
