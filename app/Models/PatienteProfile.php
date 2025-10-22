<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use App\Models\User;

class PatienteProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'date_of_birth',
        'weight',
        'height',
        'blood_type',
    ];

    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'profileable');
    }

    public function pathologies()
    {
        return $this->hasOne(PatientPathologies::class);
    }

    
}
