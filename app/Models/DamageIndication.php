<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DamageIndication extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'severity', 'description'];

    public function suggestions()
    {
        return $this->hasMany(RepairSuggestion::class)->orderBy('step_order');
    }

    public function rules()
    {
        return $this->belongsToMany(Rule::class, 'rule_indications');
    }
}
