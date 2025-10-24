<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentStatus extends Model
{
    protected $table = 'appointment_statuses';

    protected $fillable = [
        'name',
        'description',
    ];
}
