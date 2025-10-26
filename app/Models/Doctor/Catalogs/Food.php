<?php

namespace App\Models\Doctor\Catalogs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Admin\Catalogs\Unit;
use App\Models\Photo;
use App\Models\Admin\Catalogs\FoodGroup;

class Food extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'foods';

    protected $fillable = [
        'name',
        'food_group_id',
        'calories',
        'protein',
        'carbohydrates',
        'fats',
        'fiber',
        'description',
        'portion_size',
        'unit_id',
        'is_active',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function foodGroup()
    {
        return $this->belongsTo(FoodGroup::class, 'food_group_id');
    }

    public function photos()
    {
        return $this->morphMany(Photo::class, 'photoable');
    }
}
