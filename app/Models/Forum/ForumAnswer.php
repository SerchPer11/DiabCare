<?php

namespace App\Models\Forum;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class ForumAnswer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'forum_answers';

    protected $fillable = [
        'answer',
        'forum_id',
        'user_id',
    ];

    public function forum()
    {
        return $this->belongsTo(Forum::class, 'forum_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
