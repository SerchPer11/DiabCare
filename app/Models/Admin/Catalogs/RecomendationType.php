<?php

namespace App\Models\Admin\Catalogs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Doctor\Recomendation;

class RecomendationType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'recomendation_types';

    protected $fillable = [
        'name',
        'description',
    ];

    public function recomendations()
    {
        return $this->hasMany(Recomendation::class, 'recomendation_type_id');
    }
}
