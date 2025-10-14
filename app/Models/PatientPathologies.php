<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientPathologies extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_profile_id',
        'diabetes',
        'diabetes_type',
        'diabetes_diagnosis_date',
        'hypertension',
        'hypertension_diagnosis_date',
        'obesity',
        'obesity_type',
        'allergies',
        'allergy_details',
    ];

    public function patient()
    {
        return $this->belongsTo(PatienteProfile::class, 'patient_id');
    }
}
