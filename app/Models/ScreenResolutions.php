<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScreenResolutions extends Model
{
    use HasFactory;
    protected $primaryKey = 'screen_resolutions_id';
    protected $fillable = [
        'resolution_size',
        'resolutions_name'
    ];
}
