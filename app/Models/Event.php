<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;


    protected $fillable = [
        'title',
        'description',
        'venue',
        'event_date',
        'banner_image',
        'created_by',
        'is_active',
    ];
}
