<?php

namespace App\Models\Admin\Catalogs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Doctor\Catalogs\Medication;

class MedicationAdministration extends Model
{
    use HasFactory;

    protected $table = 'medication_administrations';

    protected $fillable = [
        'name',
    ];

    public function medications()
    {
        return $this->hasMany(Medication::class);
    }
}
