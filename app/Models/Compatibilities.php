<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compatibilities extends Model
{
    use HasFactory;
    protected $fillable = [
        'processor_id',
        'motherboard_id',
        'ram_id',
        'gpu_id',
        'psu_id',
        'case_id',
        'cooler_id',
        'hdd_id',
        'ssd_id',
    ];
}
