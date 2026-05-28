<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Symptom extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'category', 'description'];

    public function rules()
    {
        return $this->belongsToMany(Rule::class, 'rule_symptoms');
    }

    public function workOrders()
    {
        return $this->belongsToMany(WorkOrder::class, 'wo_symptoms');
    }
}
