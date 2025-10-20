<?php

namespace App\Models\Doctor\Catalogs;

use Illuminate\Database\Eloquent\Model;
use App\Models\Doctor\Catalogs\Exercise;

class ExerciseType extends Model
{
    protected $table = 'exercise_types';

    protected $fillable = [
        'name',
        'description',
    ];

    public function exercises()
    {
        return $this->hasMany(Exercise::class);
    }
}
