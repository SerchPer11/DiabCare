<?php

namespace App\Models\Doctor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Doctor\DoctorProfile;

class Specialty extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'specialties';

    protected $fillable = [
        'name',
        'description',
    ];

    public function doctorProfiles()
    {
        return $this->hasMany(DoctorProfile::class, 'specialty_id');
    }
}
