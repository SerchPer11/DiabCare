<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Patient\ClinicalLog;
use App\Models\User;

class Appointment extends Model
{

    /** @use HasFactory<\Database\Factories\AppointmentFactory> */
    use HasFactory, softDeletes;

    protected $table = 'appointments';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'date',
        'time',
        'modality',
        'reason',
        'additional_notes',
        'video_call_link',
        'appointment_status_id',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Appointment $appointment) {
            $appointment->clinicalLogs()->delete();
        });
    }

    public function clinicalLogs()
    {
        return $this->morphMany(ClinicalLog::class, 'loggable');
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function status()
    {
        return $this->belongsTo(AppointmentStatus::class, 'appointment_status_id');
    }
}
