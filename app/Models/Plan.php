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
        'status',
        'overall_adherence',
        'days_tracked',
        'last_tracked_date',
        'total_plan_days'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'last_tracked_date' => 'date',
        'has_progress' => 'boolean',
        'overall_adherence' => 'decimal:2',
        'days_tracked' => 'integer',
        'total_plan_days' => 'integer',
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
        return $this->status === 'activo' && 
               $this->start_date->toDateString() <= $now && 
               $this->end_date->toDateString() >= $now;
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

    /**
     * Calculate and update the total plan days.
     */
    public function calculateTotalPlanDays(): int
    {
        $totalDays = Carbon::parse($this->start_date)->diffInDays(Carbon::parse($this->end_date)) + 1;
        $this->update(['total_plan_days' => $totalDays]);
        return $totalDays;
    }

    /**
     * Update adherence percentage based on tracked days.
     */
    public function updateAdherencePercentage(): void
    {
        if ($this->total_plan_days > 0) {
            $adherencePercentage = ($this->days_tracked / $this->total_plan_days) * 100;
            $this->update(['overall_adherence' => round($adherencePercentage, 2)]);
        }
    }

    /**
     * Record a day as tracked.
     */
    public function recordTrackedDay(\Carbon\Carbon $date = null): void
    {
        $trackingDate = $date ?? Carbon::now();
        
        // Solo actualizar si la fecha es diferente a la última registrada
        if (!$this->last_tracked_date || $trackingDate->toDateString() !== $this->last_tracked_date->toDateString()) {
            $this->increment('days_tracked');
            $this->update(['last_tracked_date' => $trackingDate]);
            $this->updateAdherencePercentage();
        }
    }

    /**
     * Get adherence status based on percentage.
     */
    public function getAdherenceStatusAttribute(): string
    {
        $adherence = $this->overall_adherence;
        
        if ($adherence >= 80) {
            return 'excellent'; // Excelente
        } elseif ($adherence >= 60) {
            return 'good'; // Buena
        } elseif ($adherence >= 40) {
            return 'regular'; // Regular
        } else {
            return 'poor'; // Deficiente
        }
    }

    /**
     * Get adherence status in Spanish.
     */
    public function getAdherenceStatusSpanishAttribute(): string
    {
        return match($this->adherence_status) {
            'excellent' => 'Excelente',
            'good' => 'Buena',
            'regular' => 'Regular',
            'poor' => 'Deficiente',
            default => 'Sin datos'
        };
    }

    /**
     * Check if the plan should be tracked today.
     */
    public function shouldBeTrackedToday(): bool
    {
        $today = Carbon::now()->toDateString();
        return $this->isCurrentlyActive() && 
               (!$this->last_tracked_date || $this->last_tracked_date->toDateString() !== $today);
    }

    /**
     * Scope to filter by adherence status.
     */
    public function scopeByAdherenceStatus($query, $status)
    {
        return match($status) {
            'excellent' => $query->where('overall_adherence', '>=', 80),
            'good' => $query->where('overall_adherence', '>=', 60)->where('overall_adherence', '<', 80),
            'regular' => $query->where('overall_adherence', '>=', 40)->where('overall_adherence', '<', 60),
            'poor' => $query->where('overall_adherence', '<', 40),
            default => $query
        };
    }
}
