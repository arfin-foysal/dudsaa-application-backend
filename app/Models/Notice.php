<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'body',
        'published_by',
        'is_active',
    ];
    protected $casts = [
        'is_active' => 'boolean'
    ];
}
