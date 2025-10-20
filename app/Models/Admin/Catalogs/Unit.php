<?php

namespace App\Models\Admin\Catalogs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Doctor\Catalogs\Medication;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'units';

    protected $fillable = [
        'name',
        'abbreviation',
    ];

    public function medications()
    {
        return $this->hasMany(Medication::class);
    }
}
