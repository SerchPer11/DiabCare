<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class PatienteProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'date_of_birth',
        'weight',
        'height',
        'blood_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pathologies()
    {
        return $this->hasOne(PatientPathologies::class);
    }

    
}
