<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'contact_no',
        'alternative_contact_no',
        'gender',
        'blood_group',
        'bio',
        'father_name',
        'mother_name',
        'religion',
        'marital_status',
        'date_of_birth',
        'current_address',
        'permanent_address',
        'country_id',
        'state_id',
        'city_id',
        'nid_no',
        'birth_certificate_no',
        'passport_no',
        'image',
        'last_blood_donation_date',
        'interested_to_donate',
        'student_id_no',
        'department',
        'institute_id',
        'batch_no',
        'facebook',
        'linkedin',
        'twitter',
        'instagram',
        'status',
        'is_active',
    ];
    protected $casts = [
        'is_active' => 'boolean',
        'interested_to_donate' => 'boolean',

       
    ];
}
