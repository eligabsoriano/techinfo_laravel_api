<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForgetPassword extends Model
{
    use HasFactory;

    protected $table = 'password_reset_tokens';
    protected $guarded = [];

    // Enable timestamps
    public $timestamps = true;

    protected $fillable = [
        'email',
        'token'
    ];
}
