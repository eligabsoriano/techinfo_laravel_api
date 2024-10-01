<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Processors extends Model
{
    use HasFactory;
    protected $primaryKey = 'processor_id';
    protected $fillable = [
        'processor_name',
        'brand',
        'socket_type',
        'compatible_chipsets',
        'power',
        'base_clock_speed',
        'max_clock_speed',
        'link'
    ];
}
