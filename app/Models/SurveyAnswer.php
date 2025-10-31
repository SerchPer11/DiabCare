<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_response_id',
        'survey_question_id',
        'likert_value',
        'comment',
    ];

    public function response()
    {
        return $this->belongsTo(SurveyResponse::class, 'survey_response_id');
    }

    public function question()
    {
        return $this->belongsTo(SurveyQuestion::class, 'survey_question_id');
    }

    // Obtener el texto de la escala Likert
    public function getLikertTextAttribute()
    {
        $scales = [
            1 => 'Totalmente en desacuerdo',
            2 => 'En desacuerdo', 
            3 => 'Neutral',
            4 => 'De acuerdo',
            5 => 'Totalmente de acuerdo'
        ];

        return $scales[$this->likert_value] ?? 'No definido';
    }
}
