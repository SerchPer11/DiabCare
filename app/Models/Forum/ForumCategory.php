<?php

namespace App\Models\Forum;

use Illuminate\Database\Eloquent\Model;

class ForumCategory extends Model
{
    protected $table = 'forum_categories';

    protected $fillable = [
        'name',
    ];

    public function forums()
    {
        return $this->hasMany(Forum::class, 'category_id');
    }
}
