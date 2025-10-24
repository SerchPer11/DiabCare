<?php

namespace App\Models\Patient;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use App\Models\User;
use App\Models\Patient\PatientPathologies;
use Illuminate\Database\Eloquent\Relations\HasOne;


class PatientProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'weight',
        'height',
        'blood_type',
    ];

    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'profileable');
    }

    public function pathology(): HasOne
    {
        return $this->hasOne(PatientPathologies::class);
    }

    
}
