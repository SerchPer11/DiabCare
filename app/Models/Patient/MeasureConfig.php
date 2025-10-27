<?php

namespace App\Models\Patient;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Patient\MeasureType;
use App\Models\User;

class MeasureConfig extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'measure_configs';

    protected $fillable = [
        'min_value',
        'max_value',
        'range',
        'severity',
        'frequency',
        'patient_id',
        'measure_type_id',
        'is_active',
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function measureType()
    {
        return $this->belongsTo(MeasureType::class, 'measure_type_id');
    }
}
