<?php

namespace App\Models\Doctor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\User;
use App\Models\Photo;
use App\Models\File;
use Carbon\Carbon;

class ClinicalLogEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'event_type',
        'title',
        'description',
        'notes',
        'event_datetime',
        'related_type',
        'related_id',
        'is_active'
    ];

    protected $casts = [
        'event_datetime' => 'datetime',
        'is_active' => 'boolean'
    ];

    protected $dates = [
        'event_datetime',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Event types constants
    const EVENT_TYPES = [
        'observation' => 'Observación',
        'medication_adjustment' => 'Ajuste de Medicamento',
        'incident' => 'Incidencia',
        'document' => 'Documento'
    ];

    // Relationships
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    public function photos()
    {
        return $this->morphMany(Photo::class, 'photoable');
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeByDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    public function scopeByEventType($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('event_datetime', [$startDate, $endDate]);
    }

    // Accessors
    public function getEventTypeNameAttribute()
    {
        return self::EVENT_TYPES[$this->event_type] ?? $this->event_type;
    }

    public function getEventDatetimeFormattedAttribute()
    {
        return $this->event_datetime?->format('d/m/Y H:i');
    }

    public function getEventDatetimeHumanAttribute()
    {
        return $this->event_datetime?->diffForHumans();
    }

    public function getHasAttachmentsAttribute()
    {
        return $this->photos()->count() > 0 || $this->files()->count() > 0;
    }
}
