<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education_information extends Model
{
    use HasFactory;
    protected $fillable = [
        'member_id',
        'standard',
        'institute',
        'result',
        'passing_year',
        'is_active',
    ];

}
