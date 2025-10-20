<?php

namespace App\Models\Doctor\Catalogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Doctor\Catalogs\ExerciseType;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    /** @use HasFactory<\Database\Factories\ExerciseFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'exercises';

    protected $fillable = [
        'name',
        'exercise_type_id',
        'intensity',
        'duration_minutes',
        'description',
        'calories_burned',
        'sets',
        'repetitions',
        'rest_seconds',
        'equipment',
        'contraindications',
        'notes',
        'is_active',
    ];

    public function exerciseType()
    {
        return $this->belongsTo(ExerciseType::class);
    }

}
