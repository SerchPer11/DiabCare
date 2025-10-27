<?php

namespace App\Models\Patient;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Measure extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'measures';

    protected $fillable = [
        'value',
        'measured_at',
        'hour_measured',
        'notes',
        'measure_config_id',
        'is_active',
    ];

    public function measureConfig()
    {
        return $this->belongsTo(MeasureConfig::class);
    }
}