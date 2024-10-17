<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Motherboards extends Model
{
    use HasFactory;
    protected $primaryKey = 'motherboard_id';
    protected $fillable = [
        'motherboard_name',
        'brand',
        'socket_type',
        'ram_type',
        'max_ram_slots',
        'max_ram_capacity',
        'max_ram_speed',
        'supported_ram_type',
        'chipset',
        'has_pcie_slot',
        'has_sata_ports',
        'has_m2_slot',
        'gpu_interface',
        'form_factor',
    ];
}
