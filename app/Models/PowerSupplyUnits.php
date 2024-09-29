<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PowerSupplyUnits extends Model
{
    use HasFactory;
    protected $primaryKey = 'psu_id';
    protected $fillable = [
        'psu_name',
        'brand',
        'wattage',
        'efficiency_rating'
    ];
}
