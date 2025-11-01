<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'instructions',
        'is_active',
        'created_by',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function questions()
    {
        return $this->hasMany(SurveyQuestion::class)->orderBy('order');
    }

    public function responses()
    {
        return $this->hasMany(SurveyResponse::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('starts_at')
                  ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', now());
            });
    }

    // Verificar si el usuario ya respondió completamente
    public function hasResponseFrom($userId)
    {
        return $this->responses()->where('user_id', $userId)->where('is_complete', true)->exists();
    }
    
    // Verificar si el usuario tiene una respuesta en progreso
    public function hasInProgressResponseFrom($userId)
    {
        return $this->responses()->where('user_id', $userId)->where('is_complete', false)->exists();
    }
    
    // Obtener la respuesta en progreso del usuario
    public function getInProgressResponseFrom($userId)
    {
        return $this->responses()->where('user_id', $userId)->where('is_complete', false)->first();
    }

    // Calcular porcentaje de respuestas completadas
    public function calculateCompletionRate()
    {
        $totalResponses = $this->responses()->count();
        if ($totalResponses === 0) {
            return 0;
        }
        
        $completedResponses = $this->responses()->where('is_complete', true)->count();
        return round(($completedResponses / $totalResponses) * 100);
    }

    // Obtener número de respuestas completadas
    public function getCompletedResponsesCount()
    {
        return $this->responses()->where('is_complete', true)->count();
    }
}
