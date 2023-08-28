<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventPhoto extends Model
{
    use HasFactory;


    protected $fillable = [
        'event_id',
        'image',
        'status',
        'is_active',
        'alt_text',
        'caption',
    ];
}
