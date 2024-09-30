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
        'gpu_interface',
        'form_factor',
        'link'
    ];
}
