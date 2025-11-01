<?php

namespace App\Models\Forum;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumStatus extends Model
{
    use HasFactory;

    protected $table = 'forum_statuses';

    protected $fillable = [
        'name',
        'description',
    ];

    public function forums()
    {
        return $this->hasMany(Forum::class, 'forum_status_id');
    }
}
