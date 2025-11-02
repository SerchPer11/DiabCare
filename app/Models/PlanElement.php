<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Doctor\Catalogs\Food;
use App\Models\Doctor\Catalogs\Exercise;

class PlanElement extends Model
{
    protected $fillable = [
        'plan_id',
        'food_id',
        'exercise_id', 
        'frequency',
        'intensity',
        'quantity',
        'unit',
        'instructions',
        'time_schedule',
        'notes',
        'order',
        'is_active'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the plan this element belongs to.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the food (for nutrition plans).
     */
    public function food(): BelongsTo
    {
        return $this->belongsTo(Food::class);
    }

    /**
     * Get the exercise (for activity plans).
     */
    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

    /**
     * Get the item (food or exercise) depending on plan type.
     */
    public function getItemAttribute()
    {
        if ($this->food_id) {
            return $this->food;
        } elseif ($this->exercise_id) {
            return $this->exercise;
        }
        return null;
    }

    /**
     * Get the item name.
     */
    public function getItemNameAttribute(): string
    {
        $item = $this->item;
        return $item ? $item->name : 'Sin elemento';
    }

    /**
     * Scope to get only active elements.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by position.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
