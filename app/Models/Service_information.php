<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service_information extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'designation',
        'organization',
        'start_date',
        'end_date',
        'is_continue',
        'is_active',
    ];
}
