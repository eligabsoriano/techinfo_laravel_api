<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rams extends Model
{
    use HasFactory;
    protected $primaryKey = 'ram_id';
    protected $fillable = [
        'ram_name',
        'brand',
        'ram_type',
        'ram_capacity_gb',
        'ram_speed_mhz',
        'cas_latency',
        'power_consumption',
        'link'
    ];
}
