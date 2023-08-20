<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloodRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'blood_group',
        'units',
        'hospital_name',
        'location',
        'number',
        'needed_within_date',
        'needed_within_time',
        'is_active',
    ];
    protected $casts = [
        'is_active' => 'boolean'
    ];
    

}
