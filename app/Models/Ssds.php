<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ssds extends Model
{
    use HasFactory;
    protected $primaryKey = 'ssd_id';
    protected $fillable = [
        'ssd_name',
        'brand',
        'interface_type',
        'capacity_gb',
        'link'
    ];
}
