<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\File;
use App\Models\User;

class DoctorProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'specialty',
        'license_number',
        'titulation_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function file(){
        return $this->morphOne(File::class, 'fileable');
    }
}
