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
        'interface_type',
        'tdp_wattage',
        'gpu_length_mm',
        'link'
    ];
}
