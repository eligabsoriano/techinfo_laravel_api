<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CpuCoolers extends Model
{
    use HasFactory;
    protected $fillable = [
        'cooler_name',
        'brand',
        'socket_type_supported',
        'max_cooler_height_mm'
    ];
}