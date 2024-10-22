<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestBuild extends Model
{
    use HasFactory;

    protected $fillable = [
        'guest_name', // Change this to guest_account_name
        'build_name',
        'processor_id',
        'motherboard_id',
        'ram_id',
        'gpu_id',
        'psu_id',
        'case_id',
        'cooler_id',
        'hdd_id',
        'ssd_id',
    ];

    // A build belongs to a guest account
    public function guestAccount()
    {
        return $this->belongsTo(GuestAccount::class);
    }

}
