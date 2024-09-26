<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rams extends Model
{
    use HasFactory;
    protected $fillable = [
        'ram_name',
        'brand',
        'ram_type',
        'ram_capacity_gb',
        'ram_speed_mhz'
    ];
}
