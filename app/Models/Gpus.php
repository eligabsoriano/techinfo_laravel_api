<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gpus extends Model
{
    use HasFactory;
    protected $primaryKey = 'gpu_id';
    protected $fillable = [
        'gpu_name',
        'brand',
        'cuda_cores',
        'compute_units',
        'stream_processors',
        'game_clock_ghz',
        'base_clock_ghz',
        'boost_clock_ghz',
        'memory_size_gb',
        'memory_type',
        'memory_interface_bits',
        'tdp_wattage',
        'gpu_length_mm',
        'required_power',
        'required_6_pin_connectors',
        'required_8_pin_connectors',
        'required_12_pin_connectors',
    ];
}
