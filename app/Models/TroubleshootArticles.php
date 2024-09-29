<?php

namespace App\Models;

use App\Models\TroubleshootArticles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TroubleshootArticles extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'content'
        ];

}
