<?php

namespace App\Models\Forum;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Forum extends Model
{
    /** @use HasFactory<\Database\Factories\Forum\ForumFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'forums';

    protected $fillable = [
        'title',
        'content',
        'user_id',
        'forum_status_id',
        'category_id',
    ];

    public function status()
    {
        return $this->belongsTo(ForumStatus::class, 'forum_status_id');
    }

    public function answers()
    {
        return $this->hasMany(ForumAnswer::class, 'forum_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(ForumCategory::class, 'category_id');
    }
}
