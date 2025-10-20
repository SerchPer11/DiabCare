<?php

namespace App\Models\Doctor\Catalogs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Admin\Catalogs\MedicationType;
use App\Models\Admin\Catalogs\MedicationPresentation;
use App\Models\Admin\Catalogs\MedicationAdministration;
use App\Models\Admin\Catalogs\Unit;
use App\Models\Photo;

class Medication extends Model
{
    protected $table = 'medications';

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'concentration',
        'indications',
        'contraindications',
        'medication_type_id',
        'medication_presentation_id',
        'medication_administration_id',
        'unit_id',
        'is_active',
    ];

    public function type()
    {
        return $this->belongsTo(MedicationType::class, 'medication_type_id');
    }

    public function presentation()
    {
        return $this->belongsTo(MedicationPresentation::class, 'medication_presentation_id');
    }

    public function administration()
    {
        return $this->belongsTo(MedicationAdministration::class, 'medication_administration_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function photos()
    {
        return $this->morphMany(Photo::class, 'photoable');
    }

}
