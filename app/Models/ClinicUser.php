<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicUser extends Model
{
    protected $fillable = [
        'clinic_id',
        'user_id',
    ];
}