<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComputerCases extends Model
{
    use HasFactory;
    protected $primaryKey = 'case_id';
    protected $fillable = [
        'case_name',
        'brand',
        'form_factor_supported',
        'max_gpu_length_mm',
        'link'
    ];
}
