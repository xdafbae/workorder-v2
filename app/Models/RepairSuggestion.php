<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairSuggestion extends Model
{
    use HasFactory;

    protected $fillable = ['damage_indication_id', 'step_order', 'action_text'];

    public function indication()
    {
        return $this->belongsTo(DamageIndication::class, 'damage_indication_id');
    }
}
