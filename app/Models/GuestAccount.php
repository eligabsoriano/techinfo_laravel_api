<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestAccount extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function builds()
    {
        return $this->hasMany(GuestBuild::class);
    } 
}
