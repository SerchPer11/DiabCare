<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'user_id',
        'completed_at',
        'is_complete',
    ];

    protected $casts = [
        'is_complete' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(SurveyAnswer::class);
    }

    // Calcular promedio de respuestas Likert
    public function getAverageScore()
    {
        return $this->answers()->avg('likert_value');
    }

    // Marcar como completa
    public function markAsComplete()
    {
        $this->update([
            'is_complete' => true,
            'completed_at' => now(),
        ]);
    }
}
