<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Plan extends Model
{
    protected $fillable = [
        'patient_id',
        'plan_type_id',
        'assigned_by',
        'title',
        'description',
        'start_date',
        'end_date',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'has_progress' => 'boolean',
    ];

    /**
     * Get the patient this plan belongs to.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Get the plan type.
     */
    public function planType(): BelongsTo
    {
        return $this->belongsTo(PlanType::class);
    }

    /**
     * Get the doctor/admin who assigned this plan.
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get the plan elements.
     */
    public function elements(): HasMany
    {
        return $this->hasMany(PlanElement::class)->orderBy('order');
    }

    /**
     * Get active elements only.
     */
    public function activeElements(): HasMany
    {
        return $this->hasMany(PlanElement::class)->where('is_active', true)->orderBy('order');
    }

    /**
     * Check if plan is currently active (within date range and status active).
     */
    public function isCurrentlyActive(): bool
    {
        $now = Carbon::now()->toDateString();
        return $this->status === 'active' && 
               $this->start_date <= $now && 
               $this->end_date >= $now;
    }

    /**
     * Check if plan is expired.
     */
    public function isExpired(): bool
    {
        return Carbon::now()->toDateString() > $this->end_date;
    }

    /**
     * Get vigency status (active/expired).
     */
    public function getVigencyStatusAttribute(): string
    {
        if ($this->isExpired()) {
            return 'expired';
        }
        return 'active';
    }

    /**
     * Scope to filter by patient.
     */
    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    /**
     * Scope to filter by plan type.
     */
    public function scopeOfType($query, $planTypeId)
    {
        return $query->where('plan_type_id', $planTypeId);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by vigency.
     */
    public function scopeWithVigency($query, $vigency)
    {
        $now = Carbon::now()->toDateString();
        
        if ($vigency === 'active') {
            return $query->where('end_date', '>=', $now);
        } elseif ($vigency === 'expired') {
            return $query->where('end_date', '<', $now);
        }
        
        return $query;
    }

    /**
     * Scope for date range filtering.
     */
    public function scopeDateRange($query, $startDate = null, $endDate = null)
    {
        if ($startDate) {
            $query->whereDate('start_date', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('end_date', '<=', $endDate);
        }

        return $query;
    }
}
