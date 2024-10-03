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
        'continuous_wattage',
        'gpu_6_pin_connectors',
        'gpu_8_pin_connectors',
        'gpu_12_pin_connectors',
        'efficiency_rating',
        'has_required_connectors',
        'link'
    ];
}
