<?php

namespace App\Models\Admin\Catalogs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Doctor\Catalogs\Food;

class FoodGroup extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'food_groups';

    protected $fillable = [
        'name',
        'description',
    ];

    public function foods()
    {
        return $this->hasMany(Food::class, 'food_group_id');
    }
}
