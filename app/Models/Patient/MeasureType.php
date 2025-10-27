<?php

namespace App\Models\Patient;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Patient\Measure;

class MeasureType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'measure_types';

    protected $fillable = [
        'name',
        'unit',
    ];

    protected function measures()
    {
        return $this->hasMany(Measure::class);
    }
}
