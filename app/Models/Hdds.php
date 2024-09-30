<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hdds extends Model
{
    use HasFactory;
    protected $primaryKey = 'hdd_id';
    protected $fillable = [
        'hdd_name',
        'brand',
        'interface_type',
        'capacity_gb',
        'link'
    ];
}
