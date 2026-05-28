<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'weight', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function symptoms()
    {
        return $this->belongsToMany(Symptom::class, 'rule_symptoms');
    }

    public function indications()
    {
        return $this->belongsToMany(DamageIndication::class, 'rule_indications');
    }
}
