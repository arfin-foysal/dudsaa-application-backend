<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image',
        'company_name',
        'position',
        'location',
        'job_nature',
        'vacancy',
        'description',
        'link',
        'remuneration',
        'created_by',
        'is_active',
    ];
}
