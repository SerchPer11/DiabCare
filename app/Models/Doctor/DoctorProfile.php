<?php

namespace App\Models\Doctor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use App\Models\File;
use App\Models\User;
use App\Models\Doctor\Appointment;


class DoctorProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doctor_profiles';

    protected $fillable = [
        'specialty_id',
        'license_number',
        'titulation_date',
    ];

    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'profileable');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function file(){
        return $this->morphOne(File::class, 'fileable');
    }

    public function specialty()
    {
        return $this->belongsTo(Specialty::class, 'specialty_id');
    }
}
