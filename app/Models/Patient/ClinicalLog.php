<?php

namespace App\Models\Patient;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class ClinicalLog extends Model
{
    /** @use HasFactory<\Database\Factories\Patient\ClinicalLogFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'clinical_logs';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'loggable_type',
        'loggable_id',
        'event_type',
    ];

    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
