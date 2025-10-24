<?php

namespace App\Models\Patient;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function patientProfile(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class);
    }
}
